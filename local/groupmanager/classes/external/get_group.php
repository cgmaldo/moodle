<?php

namespace local_groupmanager;

use core_external\external_function_parameters;
use core_external\external_multiple_structure;
use core_external\external_single_structure;
use core_external\external_value;

class get_group extends \core_external\external_api {

    /**
     * Returns description of method parameters
     * @return external_function_parameters
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
        
        $transaction = $DB->start_delegated_transaction(); //If an exception is thrown in the below code, all DB queries in this code will be rollback.

        if ((trim($params['groupid']) == '') || !is_numeric($params['groupid'])) {
            throw new invalid_parameter_exception('Invalid id group');
        }
        if (!$group = $DB->get_record('groups', ['id' => $params['groupid']])) {
            throw new invalid_parameter_exception('No exist that id group in the course');
        }
        
        // now security checks
        $context = \context_course::instance($group->courseid);
        self::validate_context($context);
        require_capability('moodle/course:managegroups', $context);

        $transaction->allow_commit();

        $infoGroupSearched = (object)[
            'id' => (int)$group->id,
            'courseid' => (int)$group->courseid,
            'name' => $group->name,
            'description' => $group->description
        ];

        return $infoGroupSearched;
    }

    public static function execute_returns() {
        return new external_single_structure([
            'id' => new external_value(PARAM_INT, 'group record id'),
            'courseid' => new external_value(PARAM_INT, 'id of course'),
            'name' => new external_value(PARAM_TEXT, 'multilang compatible name, group unique'),
            'description' => new external_value(PARAM_TEXT, 'group description text'),
        ]);
     
    }
}