<?php

namespace local_groupmanager\external;

use core_external\external_api;
use core_external\external_function_parameters;
use core_external\external_multiple_structure;
use core_external\external_single_structure;
use core_external\external_value;
use invalid_parameter_exception;

class get_group extends external_api {

    /**
     * Returns description of method parameters.
     *
     * @return external_function_parameters.
     */
    public static function execute_parameters() {
        return new external_function_parameters([
            'groupid' => new external_value(PARAM_INT, 'id of group'),
        ]);
    }

    /**
    * Create groups
    * @param array $groups array of group description arrays (with keys groupname and courseid)
    * @return array of newly created groups
    */
    public static function execute(int $groupid) {
        global $CFG, $DB;
        require_once($CFG->dirroot."/group/lib.php");

        $params = self::validate_parameters(self::execute_parameters(), ['groupid' => $groupid]);
        
        if ((trim($params['groupid']) == '') || !is_numeric($params['groupid'])) {
            throw new invalid_parameter_exception('Invalid group id');
        }
        if (!$group = $DB->get_record('groups', ['id' => $params['groupid']])) {
            throw new invalid_parameter_exception('No exist that id group');
        }

        $infoGroupSearched = (object)[
            'id' => (int)$group->id,
            'courseid' => (int)$group->courseid,
            'name' => $group->name,
            'description' => $group->description
        ];

        return $infoGroupSearched;
    }

    /**
     * Returns description of method result value.
     *
     * @return \core_external\external_description.
     */
    public static function execute_returns() {
        return new external_single_structure([
            'id' => new external_value(PARAM_INT, 'group record id'),
            'courseid' => new external_value(PARAM_INT, 'id of course'),
            'name' => new external_value(PARAM_TEXT, 'multilang compatible name, group unique'),
            'description' => new external_value(PARAM_TEXT, 'group description text'),
        ]);
     
    }
}