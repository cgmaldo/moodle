<?php 

$functions = [
 
    'local_studentprogress_get_progress' => [
        'classname'   => 'local_studentprogress\external\get_progress',
        'description' => 'Obtiene el progreso de un usuario en un curso.',
        'type'        => 'read',
        'ajax'        => true,
        'services' => [ MOODLE_OFFICIAL_MOBILE_SERVICE ],
        // Lista de permisos usados/necesarios en esta funcion
        'capabilities' => 'moodle/site:config'
    ],
];

// // We define the services to install as pre-build services. A pre-build service is not editable by administrator.
// $services = array(
//     'Skillup Konecta webservice' => array(
//             'functions' => array (								
//                 'local_groupmanager_create_groups'
//             ),
//             'restrictedusers' => 0,
//             'shortname' => 'local_groupmanager',
//             'enabled'=>1,
//     )
// );