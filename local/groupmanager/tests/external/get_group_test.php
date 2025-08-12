<?php

declare(strict_types=1);

use local_groupmanager\external\create_groups;
use local_groupmanager\external\get_group;

global $CFG;
require_once($CFG->dirroot . '/webservice/tests/helpers.php');
require_once ($CFG->dirroot.'/local/groupmanager/classes/external/create_groups.php');
require_once ($CFG->dirroot.'/local/groupmanager/classes/external/get_group.php');

class get_group_test extends \externallib_advanced_testcase {
    public function test_get_group_empty_groupid() {
        $this->expectException(TypeError::class);
        $groupid=null;
        $objGetGroup = new get_group();
        $objGetGroup->execute($groupid);
    }
    public function test_get_group_non_existent_groupid() {
        // $this->expectException(\Error::class);
        $this->expectException(\invalid_parameter_exception::class);
        $groupid=-1;
        $objGetGroup = new get_group();
        $objGetGroup->execute($groupid);
    }
    public function test_get_group_by_groupid() {
        global $DB;
        $this->resetAfterTest(true);
        $this->setAdminUser();

        $course = $this->getDataGenerator()->create_course();
        $context = \context_course::instance($course->id);
        $roleid = $this->assignUserCapability('moodle/course:managegroups', $context->id);

        $objCreateGroups = new create_groups();
        $newGroupCreated=$objCreateGroups->execute(array('groups'=>[
            'courseid' => $course->id,
            'name' => $course->fullname,
            'description' => $course->summary,
            'enrolmentkey' => '',
        ]));
        $objGetGroup = new get_group();
        $groupGetted=$objGetGroup->execute(intval($newGroupCreated[0]['id']));
        $this->assertEquals($groupGetted->name, 'Test course 1');
        $this->assertEquals($groupGetted->name, $newGroupCreated[0]['name']);
    }
}