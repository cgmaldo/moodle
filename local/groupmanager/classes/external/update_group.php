<?php

namespace local_groupmanager\external;

use core_external\external_api;
use core_external\external_function_parameters;
use core_external\external_multiple_structure;
use core_external\external_single_structure;
use core_external\external_value;
use invalid_parameter_exception;

class update_group extends external_api {

    /**
     * Returns description of method parameters.
     *
     * @return external_function_parameters.
     */
    public static function execute_parameters() {
        return new external_function_parameters([
            'groupid' => new external_value(PARAM_INT, 'id of group'),
            'name' => new external_value(PARAM_TEXT, 'multilang compatible name, course unique'),
            'description' => new external_value(PARAM_RAW, 'group description text'),
        ]);
    }

    /**
    * Update group
    * @param array groupid, name, description for update
    * @return array of newly created groups
    */
    public static function execute(int $groupid, string $name, string $description='') {
        global $CFG, $DB;
        require_once($CFG->dirroot."/group/lib.php");

        $params = self::validate_parameters(self::execute_parameters(), [
            'groupid' => $groupid, 
            'name' => $name,
            'description' => $description,
        ]);
        
        $transaction = $DB->start_delegated_transaction(); //If an exception is thrown in the below code, all DB queries in this code will be rollback.

        if ((trim($params['groupid']) == '') || (!is_numeric($params['groupid']))) {
            throw new invalid_parameter_exception('Invalid id group');
        }
        if (!$searchedGroup=$DB->get_record('groups', ['id' => $params['groupid']])) {
            throw new invalid_parameter_exception('No exist that group id');
        }
        if (trim($params['name']) == '') {
            throw new invalid_parameter_exception('Invalid name group');
        }
        $sameNameGroup = $DB->get_record('groups', ['name' => $params['name']]);
        if ($sameNameGroup && $searchedGroup->courseid===$sameNameGroup->courseid && $sameNameGroup->id!=$params['groupid']) {
            throw new invalid_parameter_exception('The group name already exists in this course');
        }
        // now security checks
        $context = \context_course::instance($searchedGroup->courseid);
        self::validate_context($context);
        require_capability('moodle/course:managegroups', $context);

        $params['id']=$params['groupid'];
        $params['courseid']=$searchedGroup->courseid;
        $params = (object) $params;
        
        // finally update the group and event, grouping, memebers, conversations
        if (!groups_update_group($params)) {
            throw new invalid_parameter_exception('Error on update the group');        
        }
        
        $transaction->allow_commit();
        
        unset($params->groupid);
        
        return $params;
    }

    /**
     * Returns description of method result value.
     *
     * @return \core_external\external_description.
     */
    public static function execute_returns() {
        return new external_single_structure([
            'id' => new external_value(PARAM_INT, 'id of group'),
            'courseid' => new external_value(PARAM_INT, 'id of course'),
            'name' => new external_value(PARAM_TEXT, 'multilang compatible name, course unique'),
            'description' => new external_value(PARAM_RAW, 'group description text'),
        ]);
    }
}