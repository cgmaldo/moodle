<?php 

namespace local_companyservice\external;

use core_external\external_api;
use core_external\external_function_parameters;
use core_external\external_multiple_structure;
use core_external\external_single_structure;
use core_external\external_value;
use invalid_parameter_exception;
use core_completion_external;

class get_courseprogress extends external_api {
    /**
     * Returns description of method parameters.
     *
     * @return external_function_parameters.
     */
    public static function execute_parameters() {
        return new external_function_parameters([
            'userid' => new external_value(PARAM_TEXT, 'userid'),
            'courseid' => new external_value(PARAM_TEXT, 'courseid'),
        ]);
    }

    /**
     * Returns 
     *
     * @return object with user id, username, firstname, lastname, email, idnumber, agency 
     * @throws moodle_exception
     */
    public static function execute(string $userid='', $courseid=-1) {
        global $DB, $USER; 
        require_once (__DIR__.'/../../lib.php');

        $params = self::validate_parameters(self::execute_parameters(), ['userid'=>$userid, 'courseid'=>$courseid]);
        
        if (!$user = $DB->get_record('user', ['id'=>$params['userid'], 'suspended'=>0, 'deleted'=>0])) {
            throw new invalid_parameter_exception('No exist that user id active');
        }

        if (!$course = $DB->get_record('course', ['id'=>$params['courseid']])) {
            throw new invalid_parameter_exception('No exist that course id');
        }

        $contextUser = \core\context\user::instance($USER->id);
        self::validate_context($contextUser);
        
        $contextCourse = \core\context\course::instance($course->id);
        self::validate_context($contextCourse);
        
        $enrolled = is_enrolled($contextCourse, $params['userid'], '', false);
        if (!$enrolled) {
            throw new invalid_parameter_exception('User not enrolled in the course');
        }
        
        require_capability('report/progress:view', $contextCourse);

        $modsCourse=core_completion_external::get_activities_completion_status($course->id, $user->id);
        $modsCourse=$modsCourse['statuses'];
        
        $numberActivities=0;
        $numberActivitiesCompleted=0;
        $mods = get_course_mods($courseid);
        foreach($mods as $cm) {
            if (($cm->completion==='1') && ($cm->visible==='1') && ($cm->deletioninprogress!=='1')) {
                $numberActivities++;
                if (isActivityCompletedByUser($modsCourse, $cm->id) ) {
                    $numberActivitiesCompleted++;
                }
            }
        }

        $percentProgress = ($numberActivities==0) ? 0.00 : round(($numberActivitiesCompleted*100)/$numberActivities, 2);
        
        $progressUserCourse = (object)[
            'progress' => $percentProgress
        ];

        return $progressUserCourse;
    }

    /**
     * Returns description of method result value.
     *
     * @return \core_external\external_description.
     */
    public static function execute_returns() {
        return new external_single_structure([
            'progress' => new external_value(PARAM_FLOAT, 'user percent progress in the course'),
        ]);
    }
}