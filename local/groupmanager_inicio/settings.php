<?php
/**
 * 
 * @package    local_skillup_informe_global_excel
 * @copyright  2019 Skill&Craft {@link http://www.skillandcraft.es}
 *
 */

defined('MOODLE_INTERNAL') || die();

require_login();

$context = context_system::instance();

// $tiene_capacidad_ver_informe = has_capability('local/skillup_informe_global_excel:view', $context);

// if ($tiene_capacidad_ver_informe) {
    	
//     $url_users = $CFG->wwwroot.INFORME_GENERAL_EXCEL_FOLDER.'/view.php';
   	 
//    	$ADMIN->add('reports', new admin_externalpage('informeusuarios',
//    												  get_string ('titulo-enlace-informe', 'local_skillup_informe_global_excel'),
//    												  $url_users, 
//    												  'local/skillup_informe_global_excel:view'));
// }