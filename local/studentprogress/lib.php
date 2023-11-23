<?php 

/**
 * Funciones auxiliares
 * @package   local_studentprogress
 * @copyright De libre uso por cualquiera
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

/*
 * Devuelve si un alumno ha finalizado un curso
 *
 * @param int $userid - id  user
 * @param int $courseid - id  course
 * @return null or bool - Si el usuario no existe el curso se devuelve null, si el usuario no esta matriculado en ese curso devuelve null.
 * En resto de casos false o true si ha no finalizado o finalizado el curso dicho alumno
 */ 
function course_completion ($userid, $courseid) {
    global $DB, $CFG;
    require_once ($CFG->dirroot.'/lib/completionlib.php');

    $course = $DB->get_record('course', array('id'=>$courseid));
    if (empty($course)) {
        return null;
    } else {
        $context = context_course::instance($courseid);
        $matriculado = is_enrolled($context, $userid, '', true);
        if (!$matriculado) {
            return null;
        } else {
            $infoComp = new completion_info($course);
            $courseFinished = $infoComp->is_course_complete($userid);
            return $courseFinished ? 'Finalizado' : 'No finalizado';    
        }
    }
}

/*
 * Devuelve las fechas en las que un usuario ha sido matriculado manualmente en un curso. Son las fechas de la matricula no del curso
 *
 * @param int $userid - id  user
 * @param int $courseid - id  course
 * @return null or fechas de inicio y fin matricula - Si el usuario no esta matriculado en el curso se devuelve null.
 * En resto de casos se devuelve un array con las claves timestart y timeend
 */ 
function enrol_dates ($userid, $courseid) {
    global $DB;

    $sql = "
        SELECT ue.id, FROM_UNIXTIME(ue.timestart, '%d/%m/%Y') timestart, FROM_UNIXTIME(ue.timeend, '%d/%m/%Y') timeend
        FROM mdl_enrol e
        JOIN mdl_user_enrolments ue ON (ue.enrolid = e.id AND ue.userid = $userid)
        WHERE e.courseid=$courseid AND e.enrol='manual'
    ";
    $enrolmentsDates = $DB->get_record_sql($sql, array());
    if (!$enrolmentsDates) {
        return null;
    } else {
        return array('timestart'=>$enrolmentsDates->timestart, 'timeend'=>$enrolmentsDates->timeend);
    }
}    

/*
 * Devuelve la información sobre campos estandar/custom del usuario 
 *
 * @param int $userid - id  user
 * @return null or object - Si el usuario no existe se devuelve null. Los campos que se devuelve son id, nombre,apellidos,email y el campo specialcode
 */
function fields_user ($userid) {
    global $DB, $CFG;
    require_once ($CFG->dirroot.'/local/studentprogress/constantes.php');
    require_once ($CFG->dirroot.'/user/profile/lib.php');

    $user = $DB->get_record('user', array('id'=>$userid));
    if (!$user) {
        $respuesta= null;
    } else {
        profile_load_data($user);
        $respuesta = new stdClass();
        foreach (USER_FIELDS as $campo) {
            $respuesta->$campo = $user->$campo;
        }
    }
    return $respuesta;
 }

 /*
 * Devuelve el numero de visitas al curso contando los evento course viewed del log
 *
 * @param int $userid - id  user
 * @param int $courseid - id  course
 * @return int numero visitas
 */ 
function numero_visitas ($userid, $courseid) {
    global $DB;

    $context = context_course::instance($courseid);
    
    $sql = "
        SELECT count(*) as num_visitas
        FROM mdl_logstore_standard_log
        WHERE action='viewed' AND target='course' AND contextid={$context->id} AND userid=$userid
    ";
    $infoAccesos = $DB->get_record_sql($sql, array());
    if (!$infoAccesos) {
        return 0;
    } else {
        return $infoAccesos->num_visitas;
    }
}

/*
 * Devuelve el numero de visitas al curso contando los evento course viewed del log
 *
 * @param int $userid - id  user
 * @param int $courseid - id  course
 * @return int numero visitas
 */ 
function ultimo_acceso ($userid, $courseid) {
    global $DB;

    $sql = "
        SELECT id,  FROM_UNIXTIME(timeaccess, '%d/%m/%Y') timeaccess
        FROM mdl_user_lastaccess
        WHERE userid=$userid AND courseid=$courseid;    
    ";
    $infoAcceso = $DB->get_record_sql($sql, array());
    if (!$infoAcceso) {
        return 0;
    } else {
        return $infoAcceso->timeaccess;
    }
}

function datos_curso ($courseid) {
    global $DB;

    $course = $DB->get_record('course', array('id' => $courseid));
    return array(
        'id' => $course->id, 
        'fullname' => $course->fullname, 
        'shortname' => $course->shortname 
    );
}






