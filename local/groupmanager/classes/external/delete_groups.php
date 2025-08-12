<?php

namespace local_groupmanager\external;

use core_external\external_api;
use core_external\external_function_parameters;
use core_external\external_multiple_structure;
use core_external\external_single_structure;
use core_external\external_value;
use invalid_parameter_exception;

class delete_groups extends external_api {

    /**
     * Returns description of method parameters.
     *
     * @return external_function_parameters.
     */
    public static function execute_parameters() {
        return new external_function_parameters([
            'groups' => new external_multiple_structure(
                new external_single_structure([
                    'id' => new external_value(PARAM_INT, 'id of group'),
                ])
            )
        ]);
    }

    /**
     * Return list of groups id deleted
     *
     * @return array groups deleted
     * @throws moodle_exception
     */
    public static function execute($groups) {
        global $CFG, $DB;
        require_once($CFG->dirroot."/group/lib.php");
        
        $params = self::validate_parameters(self::execute_parameters(), ['groups' => $groups]);
        
        $transaction = $DB->start_delegated_transaction(); //If an exception is thrown in the below code, all DB queries in this code will be rollback.
        
        if (!isset($params['groups']) || (count($params['groups'])===0)) {
            throw new invalid_parameter_exception('Invalid list of groups id');
        }

        $returnValue = array();

        foreach ($params['groups'] as $group) {
            $group = (object)$group;
            if (!isset($group->id) || !is_numeric($group->id)) {
                throw new invalid_parameter_exception('Invalid group id');
            }
            if (!$existsGroup=$DB->get_record('groups', ['id' => $group->id])) {
                throw new invalid_parameter_exception("No exist group id {$group->id}");
            }
            // finally delete the group and event, grouping, memebers, conversations
            if (!groups_delete_group($group->id)) {
                
                throw new invalid_parameter_exception("Error in deleting the group {$group->id}");
            }
            $returnValue['groups'][]=$group;
        }

        $transaction->allow_commit();
        
        return $returnValue;
    }
    
    /**
     * Returns description of method result value.
     *
     * @return \core_external\external_description.
     */
    public static function execute_returns() {
        return new external_function_parameters([
            'groups' => new external_multiple_structure(
                new external_single_structure([
                    'id' => new external_value(PARAM_INT, 'id of group'),
                ])
            )
        ]);
    }
}