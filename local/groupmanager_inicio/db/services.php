<?php 

$functions = [
 
    'local_groupmanager_create_groups' => [
        'classname'   => 'local_groupmanager\external\create_groups',
        'description' => 'Crea nuevos grupos.',
        'type'        => 'write',
        'ajax'        => true,
        'services' => [ MOODLE_OFFICIAL_MOBILE_SERVICE ],
        // Lista de permisos usados/necesarios en esta funcion
        'capabilities' => 'moodle/course:creategroups,moodle/course:managegroups'
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