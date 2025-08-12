<?php

declare(strict_types=1);

use local_groupmanager\external\delete_groups;
use local_groupmanager\external\create_groups;

global $CFG;
require_once($CFG->dirroot .'/webservice/tests/helpers.php');
require_once ($CFG->dirroot.'/local/groupmanager/classes/external/delete_groups.php');
require_once ($CFG->dirroot.'/local/groupmanager/classes/external/create_groups.php');


class delete_groups_test extends \externallib_advanced_testcase {
    public function test_delete_group_empty_groupid() {
        $this->resetAfterTest(true);
        $this->setAdminUser();

        $course = $this->getDataGenerator()->create_course();
        $context = \context_course::instance($course->id);
        $roleid = $this->assignUserCapability('moodle/course:managegroups', $context->id);
        $groups=array();
        $class = new delete_groups();
        // $this->expectException(TypeError::class);
        $this->expectException(\invalid_parameter_exception::class);
        $class->execute($groups);
    }
    public function test_delete_group_non_existent_groupid() {
        $this->resetAfterTest(true);
        $this->setAdminUser();

        $course = $this->getDataGenerator()->create_course();
        $context = \context_course::instance($course->id);
        $roleid = $this->assignUserCapability('moodle/course:managegroups', $context->id);
        $groups=array();
        $groups[0]['id']=-1;
        $class = new delete_groups();
        $this->expectException(\invalid_parameter_exception::class);
        $class->execute($groups);
    }
    public function test_delete_group_groupid() {
        global $DB;
        $this->resetAfterTest(true);
        $this->setAdminUser();

        $course = $this->getDataGenerator()->create_course();
        $context = \context_course::instance($course->id);
        $roleid = $this->assignUserCapability('moodle/course:managegroups', $context->id);
        
        $class = new create_groups();
        $newGroupCreated=$class->execute(array('groups'=>[
            'courseid' => $course->id,
            'name' => $course->fullname,
            'description' => $course->summary,
            'enrolmentkey' => '',
        ]));
        $groups=array();
        $groups[0]['id']=intval($newGroupCreated[0]['id']);
        $class = new delete_groups();
        $deletGroupeResponse= $class->execute($groups);
        $groupsInNewCourse=$DB->get_records('groups', array('courseid'=>$course->id));
        $this->assertEquals(count($groupsInNewCourse), 0);
    }
}