<?php

namespace local_companyservice\external;

use core_calendar\output\humandate;

use core_external\external_api;
use core_external\external_function_parameters;
use core_external\external_multiple_structure;
use core_external\external_single_structure;
use core_external\external_value;
use invalid_parameter_exception;

global $CFG;
require_once ($CFG->dirroot.'/config.php');

class get_infocourses extends external_api {
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
     * Returns information on the user's enrolled courses
     *
     * @return object with user id, username, firstname, lastname, email, idnumber, agency 
     * @throws invalid_parameter_exception
     */
    public static function execute(string $username='') {
        global $DB, $PAGE;

        require_once(__DIR__.'/../../lib.php');
        
        $params = self::validate_parameters(self::execute_parameters(), ['username' => $username]);
        
        if ($params['username']=='') {
            throw new invalid_parameter_exception('Invalid username');
        }
        
        $user=$DB->get_record('user', array('username'=>$params['username'],'suspended'=>0,'deleted'=>0),'id,username');
        if (!$user) {
            throw new invalid_parameter_exception('User not found');
        }
        
        $enrolledCoursesUser=array();

        $enrolledCoursesInfo=enrol_get_all_users_courses($user->id, false, array('summary', 'startdate', 'enddate'));
        
        foreach ($enrolledCoursesInfo as $course) {
            $contextCourse = \core\context\course::instance($course->id);
            require_capability('gradereport/user:view', $contextCourse);
            
            $completiondate = completedDateCourse($user->id, $course->id);
            
            $enrolmentDates = getEnrolmentDates($user->id, $course->id);
            
            if (!isset($enrolmentDates->timestart) || !isset($enrolmentDates->timeend)) {
                throw new invalid_parameter_exception('Enrolment without time start or time end');
            }
            $startDayHuman=tsToDMY($enrolmentDates->timestart);
            $endDayHuman=tsToDMY($enrolmentDates->timeend);
            
            $infoCourse = array(
                'id'            => $course->id,
                'fullname'      => $course->fullname,
                'idnumber'      => $course->idnumber,
                'shortname'     => $course->shortname,
                'summary'       => $course->summary,
                'startdate'     => $startDayHuman,
                'enddate'       => $endDayHuman,
                'completiondate'=> $completiondate,
            );
            $enrolledCoursesUser[]=$infoCourse;
        } 
        return $enrolledCoursesUser;
    }

    /**
     * Returns description of method result value.
     *
     * @return \core_external\external_description.
     */
    public static function execute_returns() {
        return new external_multiple_structure(
            new external_single_structure([
                'id'         => new external_value(PARAM_INT, 'Course id'),
                'fullname'   => new external_value(PARAM_TEXT, 'Course name'),
                'idnumber'   => new external_value(PARAM_TEXT, 'Course idnumber'),
                'shortname'  => new external_value(PARAM_TEXT, 'Course shortname'),
                'summary'    => new external_value(PARAM_TEXT, 'Course summary'),
                'startdate'  => new external_value(PARAM_TEXT, 'User start date in the course'),
                'enddate'    => new external_value(PARAM_TEXT, 'User end date in the course'),
                'completiondate' => new external_value(PARAM_TEXT, 'Date when user has completed the course'),
            ])
        );
    }
}