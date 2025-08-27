<?php

/*
* Transform timestamp date to human format dd/mm/yyyy
*/
function tsToDMY(int $timestampDate) {
    try {
        return ($timestampDate) ? date( 'd/m/Y', $timestampDate) : '';
    } catch (err) {
        return '';
    }
}

/*
* Get user's completion date in a course
*/
function completedDateCourse(int $userid, int $courseid) {
    global $DB;
		
    // compruebo que este alumno tiene acceso a este curso
    $sql = "SELECT timecompleted 
            FROM {course_completions} 
            WHERE userid=$userid AND course=$courseid AND NOT ISNULL(timecompleted)";
    
    $curso_completado = $DB->get_record_sql($sql);
    
    if (!$curso_completado) // si no tiene este registro, el usuario no ha completado el curso
        return '';
    else
        return tsToDMY($curso_completado->timecompleted);
}

/*
* Get user enrolment dates
*/
function getEnrolmentDates(int $userid, int $courseid) {
    global $DB;
    $maninstance1 = $DB->get_record('enrol', array('courseid'=>$courseid, 'enrol'=>'manual'), '*', MUST_EXIST);
    $manual = enrol_get_plugin('manual');
    $enrolments = $DB->get_record('user_enrolments', array('enrolid'=>$maninstance1->id, 'userid'=>$userid, 'status'=>ENROL_USER_ACTIVE));
    return (!$enrolments) 
        ? 
        (object)array('timestart'=>0, 'timeend'=>0) 
        : 
        (object)array('timestart'=>$enrolments->timestart, 'timeend'=>$enrolments->timeend);
}

/*
* Return if a activity is completed by user 
* Param: 
* $activitiesCourseInfo course activities completed user 
* $cmid course module id of a activity in the course
*/
function isActivityCompletedByUser($activitiesCourseInfo, $cmid) {
    foreach ($activitiesCourseInfo as $info) {
        if ($info['cmid']==$cmid) {
            return (($info['timecompleted']>0) && ($info['state']==1));
        }
    }
}

/*
* Returns the percentage of completable, visible, and not pending deletion activities completed compared to the total number of these
*/
function percentActivitiesCompleted($course, $user) {
    $modsCourse=core_completion_external::get_activities_completion_status($course->id, $user->id);
    $modsCourse=$modsCourse['statuses'];
    
    $numberActivities=0;
    $numberActivitiesCompleted=0;
    $mods = get_course_mods($courseid);
    foreach($mods as $cm) {
        if (($cm->completion==1) && ($cm->visible)) {
            $numberActivities++;
            if (isActivityCompletedByUser($modsCourse, $cm->id) ) {
                $numberActivitiesCompleted++;
            }
        }
    }
    $percentProgress = ($numberActivities==0) ? 0.00 : round(($numberActivitiesCompleted*100)/$numberActivities, 2);
}