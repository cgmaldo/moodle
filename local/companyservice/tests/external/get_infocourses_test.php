<?php

declare(strict_types=1);

use local_companyservice\external\get_infocourses;

global $CFG;
require_once (__DIR__.'/../../lib.php');
require_once($CFG->dirroot.'/completion/criteria/completion_criteria.php');
require_once($CFG->dirroot.'/completion/criteria/completion_criteria_activity.php');

require_once($CFG->dirroot.'/webservice/tests/helpers.php');
require_once ($CFG->dirroot.'/local/companyservice/classes/external/get_infocourses.php');

class get_infocourses_test extends \externallib_advanced_testcase {
    public function test_no_username() {
        $class = new get_infocourses();
        
        $this->expectException(\invalid_parameter_exception::class);
        
        $returnValue=$class->execute();
    }
    
    public function test_noexistent_username() {
        $this->resetAfterTest(true);
        $this->setAdminUser();
        
        $nonExistentUsername='noname';

        $class = new get_infocourses();
        
        $this->expectException(\invalid_parameter_exception::class);
        
        $returnValue=$class->execute($nonExistentUsername);
    }

    public function test_existent_username_without_enrolment() {
        $this->resetAfterTest(true);
        $this->setAdminUser();
        
        $course = $this->getDataGenerator()->create_course();
        $user = $this->getDataGenerator()->create_user();

        $class = new get_infocourses();
        
        $returnValue=$class->execute($user->username);

        $this->assertCount(0, $returnValue);
    }

    public function test_existent_username_with_enrolment_course_non_completed() {
        global $DB;

        $this->resetAfterTest(true);
        $this->setAdminUser();

        $studentrole = $DB->get_record('role', array('shortname'=>'student'));
        $this->assertNotEmpty($studentrole);        
        $user = $this->getDataGenerator()->create_user();
        $course = $this->getDataGenerator()->create_course(array(
            'fullname' => 'Curso prueba 1',
            'idnumber' => '1',
            'shortname' => 'curso_prueba_1',
            'summary' => 'curso_prueba_1 summary',
            'enablecompletion' => 1,
        ));
        $maninstance1 = $DB->get_record('enrol', array('courseid'=>$course->id, 'enrol'=>'manual'), '*', MUST_EXIST);
        $manual = enrol_get_plugin('manual');
        $this->assertNotEmpty($manual);
        
        $date = time();

        $manual->enrol_user($maninstance1, $user->id, $studentrole->id, $date, $date+3600, ENROL_USER_ACTIVE);
        
        $class = new get_infocourses();
        $returnValue=$class->execute($user->username);

        $this->assertCount(1, $returnValue);
        $this->assertObjectHasProperty('id', (object)$returnValue[0]);
        $this->assertObjectHasProperty('fullname', (object)$returnValue[0]);
        $this->assertObjectHasProperty('idnumber', (object)$returnValue[0]);
        $this->assertObjectHasProperty('shortname', (object)$returnValue[0]);
        $this->assertObjectHasProperty('summary', (object)$returnValue[0]);
        $this->assertObjectHasProperty('startdate', (object)$returnValue[0]);
        $this->assertObjectHasProperty('enddate', (object)$returnValue[0]);
        $this->assertObjectHasProperty('completiondate', (object)$returnValue[0]);

        $this->assertEquals($returnValue[0]['id'], $course->id);
        $this->assertEquals($returnValue[0]['fullname'], $course->fullname);
        $this->assertEquals($returnValue[0]['idnumber'], $course->idnumber);
        $this->assertEquals($returnValue[0]['shortname'], $course->shortname);
        $this->assertEquals($returnValue[0]['summary'], $course->summary);
        $this->assertEquals($returnValue[0]['startdate'], tsToDMY($date));
        $this->assertEquals($returnValue[0]['enddate'], tsToDMY($date+3600));
        $this->assertEquals($returnValue[0]['completiondate'], "");
    }

    public function test_existent_username_with_enrolment_course_completed() {
        global $DB;

        $this->resetAfterTest(true);
        $this->setAdminUser();

        $studentrole = $DB->get_record('role', array('shortname'=>'student'));
        $this->assertNotEmpty($studentrole);        
        $user = $this->getDataGenerator()->create_user();
        $course = $this->getDataGenerator()->create_course(array(
            'fullname' => 'Curso prueba 1',
            'idnumber' => '1',
            'shortname' => 'curso_prueba_1',
            'summary' => 'curso_prueba_1 summary',
            'enablecompletion' => 1,
        ));
        $maninstance1 = $DB->get_record('enrol', array('courseid'=>$course->id, 'enrol'=>'manual'), '*', MUST_EXIST);
        $manual = enrol_get_plugin('manual');
        $this->assertNotEmpty($manual);
        
        $date = time();
        $manual->enrol_user($maninstance1, $user->id, $studentrole->id, $date, $date+3600, ENROL_USER_ACTIVE);

        $ccompletion = new \completion_completion([
            'course' => $course->id,
            'userid' => $user->id
        ]);
        $ccompletion->mark_complete();

        $class = new get_infocourses();
        $returnValue=$class->execute($user->username);

        $this->assertCount(1, $returnValue);
        $this->assertObjectHasProperty('id', (object)$returnValue[0]);
        $this->assertObjectHasProperty('fullname', (object)$returnValue[0]);
        $this->assertObjectHasProperty('idnumber', (object)$returnValue[0]);
        $this->assertObjectHasProperty('shortname', (object)$returnValue[0]);
        $this->assertObjectHasProperty('summary', (object)$returnValue[0]);
        $this->assertObjectHasProperty('startdate', (object)$returnValue[0]);
        $this->assertObjectHasProperty('enddate', (object)$returnValue[0]);
        $this->assertObjectHasProperty('completiondate', (object)$returnValue[0]);

        $this->assertEquals($returnValue[0]['id'], $course->id);
        $this->assertEquals($returnValue[0]['fullname'], $course->fullname);
        $this->assertEquals($returnValue[0]['idnumber'], $course->idnumber);
        $this->assertEquals($returnValue[0]['shortname'], $course->shortname);
        $this->assertEquals($returnValue[0]['summary'], $course->summary);
        $this->assertEquals($returnValue[0]['startdate'], tsToDMY($date));
        $this->assertEquals($returnValue[0]['enddate'], tsToDMY($date+3600));
        $this->assertEquals($returnValue[0]['completiondate'], date("d/m/Y", time()));
    }
}