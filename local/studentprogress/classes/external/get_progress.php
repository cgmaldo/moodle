<?php
namespace local_studentprogress\external;

use external_api;
use invalid_parameter_exception;
use external_function_parameters;
use external_multiple_structure;
use external_single_structure;
use external_value;

global $CFG;
require ($CFG->dirroot.'/local/studentprogress/lib.php');

class get_progress extends external_api {

    /**
     * Returns description of method parameters
     * @return external_function_parameters
     */
    public static function execute_parameters() {
        return new external_function_parameters([
            'data' => new external_multiple_structure(
                new external_single_structure([
                    'userid' => new external_value(PARAM_INT, 'id of user'),
                    'courseid' => new external_value(PARAM_INT, 'id of course')
                ])
            )
        ]);
    }

    /**
     * Returns description of returns
     * @return external_function_returns
     */
    public static function execute_returns() {
        return new external_multiple_structure(
            new external_single_structure([
                'firstname' => new external_value(PARAM_TEXT, 'Nombre del usuario'),
                'lastname' => new external_value(PARAM_TEXT, 'Apellidos del usuario'),
                'email' => new external_value(PARAM_TEXT, 'Email del usuario'),
                'profile_field_specialcode' => new external_value(PARAM_TEXT, 'Codigo especial asociado a cada usuario'),
                'coursename' => new external_value(PARAM_TEXT, 'Nombre del curso'),
                'courseshortname' => new external_value(PARAM_TEXT, 'Nombre corto del curso'),
                'startdate' => new external_value(PARAM_RAW, 'Fecha de inicio de la matricula'),
                'enddate' => new external_value(PARAM_RAW, 'Fecha de fin de la matricula'),
                'coursestatus' => new external_value(PARAM_TEXT, 'Estado del curso FINALIZADO o <VACIO>'),
                'access' => new external_value(PARAM_INT, 'Numero de accesos al curso'),
                'lastaccess' => new external_value(PARAM_TEXT, 'Último acceso'),
            ])
        );
    }

    /**
     * studentprogress
     * @param array $data array of student progress description arrays (with keys userid and courseid)
     * @return array of progress student into course o empty array
     */
    public static function execute($data) {
        global $DB;
        $params = self::validate_parameters(self::execute_parameters(), ['data' => $data]);
        $response = array();
        foreach ($params['data'] as $data) {
            $data = (object)$data;
            if ($DB->get_record('user', array('id'=>$data->userid, 'suspended'=>0, 'deleted'=>0)) && $DB->get_record('course', array('id'=>$data->courseid))) {
                $usuario = fields_user ($data->userid);
                $curso = datos_curso ($data->courseid);
                $acabado = course_completion ($data->userid, $data->courseid);
                $matricula = enrol_dates($data->userid, $data->courseid);
                $accesos = numero_visitas ($data->userid, $data->courseid);
                $ultimo_acceso = ultimo_acceso ($data->userid, $data->courseid);
                
                $response[] = array(
                    'firstname' => $usuario->firstname,
                    'lastname' => $usuario->lastname,
                    'email' => $usuario->email,
                    'profile_field_specialcode' => $usuario->profile_field_specialcode,
                    'coursename' => $curso['fullname'],
                    'courseshortname' => $curso['shortname'],
                    'startdate' => $matricula['timestart'],
                    'enddate' => $matricula['timeend'],
                    'coursestatus' => $acabado,
                    'access' => $accesos,
                    'lastaccess' => $ultimo_acceso
                );
            } 
        }
        return $response;
    }

}