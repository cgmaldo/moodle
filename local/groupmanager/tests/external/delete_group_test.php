<?php

declare(strict_types=1);

use local_groupmanager\delete_group;
use local_groupmanager\create_groups;

global $CFG;
require_once($CFG->dirroot . '/webservice/tests/helpers.php');
require_once ($CFG->dirroot.'/local/groupmanager/classes/external/delete_group.php');
require_once ($CFG->dirroot.'/local/groupmanager/classes/external/create_groups.php');


class delete_group_test extends \externallib_advanced_testcase {
    public function test_delete_group_empty_groupid() {
        $this->resetAfterTest(true);
        $this->setAdminUser();

        $course = $this->getDataGenerator()->create_course();
        $context = \context_course::instance($course->id);
        $roleid = $this->assignUserCapability('moodle/course:managegroups', $context->id);
        $groupid='';
        $class = new delete_group();
        $this->expectException(TypeError::class);
        $class->execute($groupid);
    }
    public function test_delete_group_non_existent_groupid() {
        $this->resetAfterTest(true);
        $this->setAdminUser();

        $course = $this->getDataGenerator()->create_course();
        $context = \context_course::instance($course->id);
        $roleid = $this->assignUserCapability('moodle/course:managegroups', $context->id);
        $groupid=-1;
        $class = new delete_group();
        $this->expectException(\invalid_parameter_exception::class);
        $class->execute($groupid);
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
            'summary' => $course->summary,
        ]));
        $class = new delete_group();
        $deletGroupeResponse= $class->execute(intval($newGroupCreated[0]['id']));
        $groupsInNewCourse=$DB->get_records('groups', array('courseid'=>$course->id));
        $this->assertEquals(count($groupsInNewCourse), 0);
    }
}