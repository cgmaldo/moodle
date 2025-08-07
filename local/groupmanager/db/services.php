<?php

$functions = [
 
    'local_groupmanager_create_groups' => [
        'classname'   => 'local_groupmanager\external\create_groups',
        'description' => 'Create new groups.',
        'type'        => 'write',
        'ajax'        => true,
        'services' => [
            // A standard Moodle install includes one default service:
            // - MOODLE_OFFICIAL_MOBILE_SERVICE.
            // Specifying this service means that your function will be available for
            // use in the Moodle Mobile App.
            MOODLE_OFFICIAL_MOBILE_SERVICE,
        ],
        // Lista de permisos usados/necesarios en esta funcion
        'capabilities' => 'moodle/course:creategroups,moodle/course:managegroups',
    ],
    'local_groupmanager_delete_group' => [
        'classname'   => 'local_groupmanager\external\delete_group',
        'description' => 'Delete group.',
        'type'        => 'write',
        'ajax'        => true,
        'services' => [ MOODLE_OFFICIAL_MOBILE_SERVICE ],
        // Lista de permisos usados/necesarios en esta funcion
        'capabilities' => 'moodle/course:creategroups,moodle/course:managegroups',
    ],
    'local_groupmanager_update_group' => [
        'classname'   => 'local_groupmanager\external\update_group',
        'description' => 'Update group.',
        'type'        => 'write',
        'ajax'        => true,
        'services' => [ MOODLE_OFFICIAL_MOBILE_SERVICE ],
        // Lista de permisos usados/necesarios en esta funcion
        'capabilities' => 'moodle/course:creategroups,moodle/course:managegroups',
    ],
    'local_groupmanager_get_group' => [
        'classname'   => 'local_groupmanager\external\get_group',
        'description' => 'get info group by id.',
        'type'        => 'read',
        'ajax'        => true,
        'services' => [ MOODLE_OFFICIAL_MOBILE_SERVICE ],
        // Lista de permisos usados/necesarios en esta funcion
        'capabilities' => 'moodle/course:creategroups,moodle/course:managegroups',
    ],
];

$services = array(
   'ws_groupmanager'  => array(
        'functions' => [ 'local_groupmanager_create_groups', 'local_groupmanager_delete_group', 'local_groupmanager_update_group', 'local_groupmanager_get_group'], // Unused as we add the service in each function definition, third party services would use this.
        'enabled' => 1,
        'restrictedusers' => 1,
        'shortname' => 'ws_groupmanager',
        'downloadfiles' => 0,
        'uploadfiles' => 0
    ),
);