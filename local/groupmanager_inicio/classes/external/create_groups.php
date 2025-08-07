<?php
namespace local_groupmanager\external;

use external_api;
use invalid_parameter_exception;
use external_function_parameters;
use external_multiple_structure;
use external_single_structure;
use external_value;

class create_groups extends external_api {

    /**
     * Returns description of method parameters
     * @return external_function_parameters
     */
    public static function execute_parameters() {
        return new external_function_parameters([
            'groups' => new external_multiple_structure(
                new external_single_structure([
                    'courseid' => new external_value(PARAM_INT, 'id of course'),
                    'name' => new external_value(PARAM_TEXT, 'multilang compatible name, course unique'),
                    'description' => new external_value(PARAM_RAW, 'group description text'),
                    'enrolmentkey' => new external_value(PARAM_RAW, 'group enrol secret phrase'),
                ])
            )
        ]);
    }

    public static function execute_returns() {
        return new external_multiple_structure(
            new external_single_structure([
                'id' => new external_value(PARAM_INT, 'group record id'),
                'courseid' => new external_value(PARAM_INT, 'id of course'),
                'name' => new external_value(PARAM_TEXT, 'multilang compatible name, course unique'),
                'description' => new external_value(PARAM_RAW, 'group description text'),
                'enrolmentkey' => new external_value(PARAM_RAW, 'group enrol secret phrase'),
            ])
        );
    }

    /**
     * Create groups
     * @param array $groups array of group description arrays (with keys groupname and courseid)
     * @return array of newly created groups
     */
    public static function execute($groups) {
        global $CFG, $DB;
        require_once($CFG->dirroot."/group/lib.php");
        
        $params = self::validate_parameters(self::execute_parameters(), ['groups' => $groups]);

        $transaction = $DB->start_delegated_transaction(); //If an exception is thrown in the below code, all DB queries in this code will be rollback.

        $groups = array();

        foreach ($params['groups'] as $group) {
            $group = (object)$group;

            if (trim($group->name) == '') {
                throw new invalid_parameter_exception('Invalid group name');
            }
            if ($DB->get_record('groups', ['courseid' => $group->courseid, 'name' => $group->name])) {
                throw new invalid_parameter_exception('Group with the same name already exists in the course');
            }

            // now security checks
            $context = get_context_instance(CONTEXT_COURSE, $group->courseid);
            self::validate_context($context);
            require_capability('moodle/course:managegroups', $context);

            // finally create the group
            $group->id = groups_create_group($group, false);
            $groups[] = (array) $group;
        }

        $transaction->allow_commit();

        return $groups;
    }

}