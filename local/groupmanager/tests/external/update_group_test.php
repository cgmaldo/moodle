<?php

declare(strict_types=1);

use local_groupmanager\external\create_groups;
use local_groupmanager\external\get_group;
use local_groupmanager\external\update_group;

global $CFG;
require_once($CFG->dirroot . '/webservice/tests/helpers.php');
require_once ($CFG->dirroot.'/local/groupmanager/classes/external/create_groups.php');
require_once ($CFG->dirroot.'/local/groupmanager/classes/external/get_group.php');
require_once ($CFG->dirroot.'/local/groupmanager/classes/external/update_group.php');

class update_group_test extends \externallib_advanced_testcase {
    public function test_update_group_empty_groupid() {
        $courseid=null;
        $groupid=null;
        $name='';
        $description='';
        $this->expectException(\Error::class);
        $objUpdateGroup = new update_group();
        $objUpdateGroup->execute($courseid, $groupid, $name, $description);
    }
    public function test_update_group_non_existent_groupid() {
        global $DB;
        $this->resetAfterTest(true);
        $this->setAdminUser();

        $course = $this->getDataGenerator()->create_course();
        $context = \context_course::instance($course->id);
        $roleid = $this->assignUserCapability('moodle/course:managegroups', $context->id);

        $this->expectException(\Error::class);

        $groupIdNonExistent = -1;
        $objupdateGroup = new update_group();
        $objupdateGroup->execute(
            intval($course->id),
            $groupIdNonExistent,
            'nombre no repetido cualquiera',
            'descripción cualquiera',
        );
    }
    public function test_update_group_empty_name() {
        global $DB;
        $this->resetAfterTest(true);
        $this->setAdminUser();

        $course = $this->getDataGenerator()->create_course();
        $context = \context_course::instance($course->id);
        $roleid = $this->assignUserCapability('moodle/course:managegroups', $context->id);

        $objCreateGroup = new create_groups();
        $newGroupCreated=$objCreateGroup->execute(array('groups'=>[
            'courseid' => $course->id,
            'name' => $course->fullname,
            'description' => $course->summary,
            'enrolmentkey' => '',
        ]));

        $this->expectException(\TypeError::class);

        $emptyNameGroup='';
        
        $objupdateGroup = new update_group();
        $objupdateGroup->execute(
            intval($newGroupCreated[0]['courseid']),
            intval($newGroupCreated[0]['id']),
            $emptyNameGroup,
            'descripción cualquiera',
        );
    }
    public function test_update_group_duplicated_name() {
        global $DB;
        $this->resetAfterTest(true);
        $this->setAdminUser();

        $course = $this->getDataGenerator()->create_course();
        $context = \context_course::instance($course->id);
        $roleid = $this->assignUserCapability('moodle/course:managegroups', $context->id);

        $objCreateGroup = new create_groups();
        $newGroupCreated1=$objCreateGroup->execute(array('groups'=>[
            'courseid' => $course->id,
            'name' => $course->fullname.'-1',
            'description' => $course->summary,
            'enrolmentkey' => '',
        ]));
        $newGroupCreated2=$objCreateGroup->execute(array('groups'=>[
            'courseid' => $course->id,
            'name' => $course->fullname.'-2',
            'description' => $course->summary,
            'enrolmentkey' => '',
        ]));

        $duplicatedName=$newGroupCreated2[0]['name'];

        $this->expectException(\invalid_parameter_exception::class);
        
        $objupdateGroup1 = new update_group();
        $objupdateGroup1->execute(
            // intval($newGroupCreated1[0]['courseid']),
            intval($newGroupCreated1[0]['id']),
            $duplicatedName,
            'descripción cualquiera',
        );
    }
    public function test_update_group_name() {
        global $DB;
        $this->resetAfterTest(true);
        $this->setAdminUser();

        $course = $this->getDataGenerator()->create_course();
        $context = \context_course::instance($course->id);
        $roleid = $this->assignUserCapability('moodle/course:managegroups', $context->id);

        $objCreateGroup = new create_groups();
        $newGroupCreated=$objCreateGroup->execute(array('groups'=>[
            'courseid' => $course->id,
            'name' => $course->fullname,
            'description' => $course->summary,
            'enrolmentkey' => '',
        ]));
        
        $objUpdateGroup=new update_group();
        $newGroup = $objUpdateGroup->execute(
            intval($newGroupCreated[0]['id']),
            $newGroupCreated[0]['name'].'-MOD',
            'descripción cualquiera',
        );
        
        $groupInfo=$DB->get_record('groups', array('id'=>$newGroup->id));
        $this->assertEquals($newGroup->name, $groupInfo->name);
    }
}