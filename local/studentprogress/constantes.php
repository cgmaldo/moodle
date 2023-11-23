<?php 

/**
 *
 * @package   local_studentprogress
 * @copyright De libre uso por cualquiera
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

 defined('MOODLE_INTERNAL') || die();

 if (!defined('MOODLE_ROOT_FOLDER')) 
 	define('MOODLE_ROOT_FOLDER', __DIR__.'/../..');
 
 if (!defined('PLUGIN_FOLDER')) 
 	define('PLUGIN_FOLDER', '/local/studentprogress');
  
 if (!defined('USER_FIELDS')) 
 	define('USER_FIELDS', ARRAY('id', 'firstname', 'lastname', 'email', 'profile_field_specialcode'));