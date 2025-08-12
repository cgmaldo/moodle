<?php

declare(strict_types=1);

use local_groupmanager\external\create_groups;

global $CFG;
require_once($CFG->dirroot .'/webservice/tests/helpers.php');
require_once ($CFG->dirroot.'/local/groupmanager/classes/external/create_groups.php');

class create_groups_test extends \externallib_advanced_testcase {
    public function test_new_group_id_course() {
        global $DB;
        $this->resetAfterTest(true);
        $this->setAdminUser();
        $course = $this->getDataGenerator()->create_course();
        $context = \context_course::instance($course->id);
        $roleid = $this->assignUserCapability('moodle/course:managegroups', $context->id);
        
        $classTesting = new create_groups();
        
        $returnValue=$classTesting->execute(array('groups'=>[
            'courseid' => $course->id,
            'name' => $course->fullname,
            'description' => $course->fullname,
            'enrolmentkey'=>'',
        ]));
        $this->assertEquals($returnValue[0]['courseid'], $course->id);
    }
    public function test_number_groups_new_course() {
        global $DB;
        $this->resetAfterTest(true);
        $this->setAdminUser();
        $course = $this->getDataGenerator()->create_course();
        $context = \context_course::instance($course->id);
        $roleid = $this->assignUserCapability('moodle/course:managegroups', $context->id);
        
        $class = new create_groups();
        
        $returnValue=$class->execute(array('groups'=>[
            'courseid' => $course->id,
            'name' => $course->fullname,
            'description' => $course->fullname,
            'enrolmentkey'=>'',
        ]));
        $groupsInNewCourse=$DB->get_records('groups', array('courseid'=>$course->id));
        $this->assertEquals(count($groupsInNewCourse), 1);
    }
    public function test_fail_on_duplicate_name_groups() {
        global $DB;
        $this->resetAfterTest(true);
        $this->setAdminUser();
        $course = $this->getDataGenerator()->create_course();
        $context = \context_course::instance($course->id);
        $roleid = $this->assignUserCapability('moodle/course:managegroups', $context->id);
        
        $class = new create_groups();
        
        $returnValue=$class->execute(array('groups'=>[
            'courseid' => $course->id,
            'name' => $course->fullname,
            'description' => $course->fullname,
            'enrolmentkey'=>'',
        ]));

        $this->expectException(\invalid_parameter_exception::class);

        $returnValue=$class->execute(array('groups'=>[
            'courseid' => $course->id,
            'name' => $course->fullname,
            'description' => $course->fullname,
            'enrolmentkey'=>'',
        ]));
    }
    public function test_fail_on_empty_name_group() {
        global $DB;
        $this->resetAfterTest(true);
        $this->setAdminUser();
        $course = $this->getDataGenerator()->create_course();
        $context = \context_course::instance($course->id);
        $roleid = $this->assignUserCapability('moodle/course:managegroups', $context->id);
        
        $class = new create_groups();
        
        $this->expectException(\invalid_parameter_exception::class);
        
        $returnValue=$class->execute(array('groups'=>[
            'courseid' => $course->id,
            'name' => '',
            'description' => $course->fullname,
            'enrolmentkey'=>'',
        ]));
    }
}