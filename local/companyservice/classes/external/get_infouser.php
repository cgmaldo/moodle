<?php

namespace local_companyservice\external;

use core_external\external_api;
use core_external\external_function_parameters;
use core_external\external_multiple_structure;
use core_external\external_single_structure;
use core_external\external_value;
use invalid_parameter_exception;

global $CFG;
require_once ($CFG->dirroot.'/config.php');

class get_infouser extends external_api {
    /**
     * Returns description of method parameters.
     *
     * @return external_function_parameters.
     */
    public static function execute_parameters() {
        return new external_function_parameters([
            'username' => new external_value(PARAM_TEXT, 'username'),
        ]);
    }

    /**
     * Returns user information by username
     *
     * @return object with user id, username, firstname, lastname, email, idnumber, agency 
     * @throws moodle_exception
     */
    public static function execute(string $username='') {
        global $CFG, $DB, $USER; 
        require_once ($CFG->dirroot.'/user/profile/lib.php');

        $params = self::validate_parameters(self::execute_parameters(), ['username' => $username]);
        
        if ((trim($params['username']) == '')) {
            throw new invalid_parameter_exception('Username empty');
        }

        if (!$user = $DB->get_record('user', ['username'=>$params['username'], 'suspended'=>0, 'deleted'=>0])) {
            throw new invalid_parameter_exception('No exist that user username');
        }

        $context = \core\context\user::instance($USER->id);
        self::validate_context($context);
        require_capability('moodle/user:viewalldetails', $context);

        profile_load_data($user);

        if (!isset($user->profile_field_agency)) {
            throw new invalid_parameter_exception('No exist profile filed agency');
        }
        
        $infoUserSearched = (object)[
            'id'        => $user->id,
            'username'  => $user->username,
            'firstname' => $user->firstname,
            'lastname'  => $user->lastname,
            'email'     => $user->email,
            'idnumber'  => $user->idnumber,
            'agency'    => $user->profile_field_agency,
        ];

        return $infoUserSearched;
    }

    /**
     * Returns description of method result value.
     *
     * @return \core_external\external_description.
     */
    public static function execute_returns() {
        return new external_single_structure([
            'id'        => new external_value(PARAM_INT, 'user id'),
            'username'  => new external_value(PARAM_TEXT, 'user username'),
            'firstname' => new external_value(PARAM_TEXT, 'user firstname'),
            'lastname'  => new external_value(PARAM_TEXT, 'user lastname'),
            'email'     => new external_value(PARAM_EMAIL, 'user email'),
            'idnumber'  => new external_value(PARAM_TEXT, 'user idnumber'),
            'agency'    => new external_value(PARAM_TEXT, 'user agency'),
        ]);
    }
}