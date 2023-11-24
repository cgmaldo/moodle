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

// We define the services to install as pre-build services. A pre-build service is not editable by administrator.
$services = array(
    'Progreso alumno' => array (
            'functions' => array (								
                'local_studentprogress_get_progress'
            ),
            'restrictedusers' => 1,
            // This field os optional, but requried if the `restrictedusers` value is set, so as to allow configuration via the Web UI.
            'shortname' => 'local_studentprogress',
            // For example: 'local_groupmanager/integration:access'
            'requiredcapability' => 'moodle/site:config',
            // If enabled, the Moodle administrator must link a user to this service from the Web UI.
            'restrictedusers' => 1,
            // Whether to allow file downloads.
            'downloadfiles' => 0,
            //Whether to allow file uploads.
            'uploadfiles'  => 0,
            'enabled'=> 1,
    )
);
