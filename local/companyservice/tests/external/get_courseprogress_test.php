<?php

declare(strict_types=1);

use local_companyservice\external\get_courseprogress;

global $CFG;

require_once($CFG->dirroot . '/completion/criteria/completion_criteria.php');
require_once($CFG->dirroot . '/completion/criteria/completion_criteria_activity.php');

require_once($CFG->dirroot .'/webservice/tests/helpers.php');
require_once ($CFG->dirroot.'/local/companyservice/classes/external/get_courseprogress.php');

class get_courseprogress_test extends \externallib_advanced_testcase {
    public function test_no_userid_courseid() {
        $class = new get_courseprogress();
        
        $this->expectException(\invalid_parameter_exception::class);
        
        $returnValue=$class->execute();
    }
    
    public function test_noexistent_userid() {
        $this->resetAfterTest(true);
        $this->setAdminUser();
        $course = $this->getDataGenerator()->create_course();

        $courseid=$course->id;
        $nonExistentUserid="-1";

        $class = new get_courseprogress();
        
        $this->expectException(\invalid_parameter_exception::class);
        
        $returnValue=$class->execute($nonExistentUserid, $courseid);
    }
    
    public function test_noexistent_courseid() {
        $this->resetAfterTest(true);
        $this->setAdminUser();
        
        $user = $this->getDataGenerator()->create_user();
        $nonExistentCourseid="-1";
        
        $this->expectException(\invalid_parameter_exception::class);
        
        $class = new get_courseprogress();
        $returnValue=$class->execute($user->id, $nonExistentCourseid);
    }
    
    public function test_noexistent_enrolment() {
        $this->resetAfterTest(true);
        $this->setAdminUser();
        
        $user = $this->getDataGenerator()->create_user();
        $course = $this->getDataGenerator()->create_course();
        
        $this->expectException(\invalid_parameter_exception::class);
        
        $class = new get_courseprogress();
        $returnValue=$class->execute($user->id, $course->id);
    }

    public function test_existent_enrolment_progress() {
        global $DB;
        
        $this->resetAfterTest(true);
        $this->setAdminUser();
        
        $user = $this->getDataGenerator()->create_user();
        $course = $this->getDataGenerator()->create_course(array('enablecompletion' => 1));
        $studentroleid = $DB->get_field('role', 'id', array('shortname'=>'student'));
        $this->getDataGenerator()->enrol_user($user->id, $course->id, $studentroleid, 'manual');
        
        $activity = $this->getDataGenerator()->create_module('page', array('course' => $course->id), array('completion' => 1));
        // Set completion criteria and mark the user to complete the criteria.
        $criteriadata = (object) [
            'id' => $course->id,
            'criteria_activity' => [$activity->cmid => 1],
        ];
        $criterion = new \completion_criteria_activity();
        $criterion->update_config($criteriadata);
        $cmactivity = get_coursemodule_from_id('page', $activity->cmid);
        $completion = new \completion_info($course);
        $completion->update_state($cmactivity, COMPLETION_COMPLETE, $user->id);

        $activity2 = $this->getDataGenerator()->create_module('page', array('course' => $course->id), array('completion' => 1));

        $class = new get_courseprogress();
        $returnValue=$class->execute($user->id, $course->id);
        
        $this->assertEquals($returnValue->progress, 50.0);
     }

    public function test_existent_enrolment_progress() {
        global $DB;
        
        $this->resetAfterTest(true);
        $this->setAdminUser();
        
        $studentrole = $DB->get_record('role', array('shortname'=>'student'));
        $this->assertNotEmpty($studentrole);        
        $user = $this->getDataGenerator()->create_user();
        $course = $this->getDataGenerator()->create_course(array(
            'enablecompletion' => 1,
            'shortname' => 'Z',
            'idnumber' => '123',
        ));
        $maninstance1 = $DB->get_record('enrol', array('courseid'=>$course->id, 'enrol'=>'manual'), '*', MUST_EXIST);
        $DB->set_field('enrol', 'status', ENROL_INSTANCE_DISABLED, array('id'=>$maninstance1->id));
        $manual = enrol_get_plugin('manual');
        $this->assertNotEmpty($manual);
        $manual->enrol_user($maninstance1, $user->id, $studentrole->id, 0, 0);

        $activity = $this->getDataGenerator()->create_module('page', array('course' => $course->id), array('completion' => 1));
        $criteriadata = (object) [
            'id' => $course->id,
            'criteria_activity' => [$activity->cmid => 1],
        ];
        $criterion = new \completion_criteria_activity();
        $criterion->update_config($criteriadata);
        $cmactivity = get_coursemodule_from_id('page', $activity->cmid);
        $completion = new \completion_info($course);
        $completion->update_state($cmactivity, COMPLETION_COMPLETE, $user->id);
        $activity2 = $this->getDataGenerator()->create_module('page', array('course' => $course->id), array('completion' => 1));

        $class = new get_courseprogress();
        $returnValue=$class->execute($user->id, $course->id);
        
        $this->assertEquals($returnValue->progress, 50.0);
     }

     public function test_progress_without_completable_activities() {
        global $DB;
        
        $this->resetAfterTest(true);
        $this->setAdminUser();
        
        $user = $this->getDataGenerator()->create_user();
        $course = $this->getDataGenerator()->create_course(array('enablecompletion' => 1));
        $studentroleid = $DB->get_field('role', 'id', array('shortname'=>'student'));
        $this->getDataGenerator()->enrol_user($user->id, $course->id, $studentroleid, 'manual');
        
        $this->getDataGenerator()->create_module('page', array('course' => $course->id), array('completion' => 0));
        
        $class = new get_courseprogress();
        $returnValue=$class->execute($user->id, $course->id);
        
        $this->assertEquals($returnValue->progress, 0);
     }
}