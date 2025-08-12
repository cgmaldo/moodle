<?php 

$functions = [
    // The name of your web service function, as discussed above.
    'local_groupmanager_create_groups' => [
        'classname'   => 'local_groupmanager\external\create_groups',
        'methodname'  => 'execute',
        'classpath'   => 'local/groupmanager/classes/external/create_groups.php',
        'description' => 'Creates new groups.(v2)',
        'type'        => 'write',
        'ajax'        => true,
        'services' => [ MOODLE_OFFICIAL_MOBILE_SERVICE ],
        'capabilities' => 'moodle/course:creategroups,moodle/course:managegroups',
    ],
    'local_groupmanager_delete_groups' => [
        'classname'   => 'local_groupmanager\external\delete_groups',
        'methodname'  => 'execute',
        'classpath'   => 'local/groupmanager/classes/external/delete_groups.php',
        'description' => 'Delete group of groups',
        'type'        => 'write',
        'ajax'        => true,
        'services' => [ MOODLE_OFFICIAL_MOBILE_SERVICE ],
        'capabilities' => 'moodle/course:creategroups,moodle/course:managegroups',
    ],
    'local_groupmanager_get_group' => [
        'classname'   => 'local_groupmanager\external\get_group',
        'methodname'  => 'execute',
        'classpath'   => 'local/groupmanager/classes/external/get_group.php',
        'description' => 'Get info group by id group',
        'type'        => 'write',
        'ajax'        => true,
        'services' => [ MOODLE_OFFICIAL_MOBILE_SERVICE ],
        'capabilities' => 'moodle/course:creategroups,moodle/course:managegroups',
    ],
    'local_groupmanager_update_group' => [
        'classname'   => 'local_groupmanager\external\update_group',
        'methodname'  => 'execute',
        'classpath'   => 'local/groupmanager/classes/external/update_group.php',
        'description' => 'Update name/description of group',
        'type'        => 'write',
        'ajax'        => true,
        'services' => [ MOODLE_OFFICIAL_MOBILE_SERVICE ],
        'capabilities' => 'moodle/course:creategroups,moodle/course:managegroups',
    ],
];

$services = [
    'ws_group_manager' => [
        'functions' => [
            'local_groupmanager_create_groups',
            'local_groupmanager_delete_groups',
            'local_groupmanager_get_group',
            'local_groupmanager_update_group',
        ],
        'requiredcapability' => '',
        'restrictedusers' => 1,
        'enabled' => 1,
        'shortname' =>  'ws_group_manager',
        'downloadfiles' => 0,
        'uploadfiles'  => 0,
    ]
];