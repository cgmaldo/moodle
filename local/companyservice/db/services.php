<?php 

$functions = [
    'local_companyservice_get_infouser' => [
        'classname'    => 'local_companyservice\external\get_infouser',
        'methodname'   => 'execute',
        'classpath'    => 'local/companyservice/classes/external/get_infouser.php',
        'description'  => 'Get user information by username',
        'type'         => 'read',
        'ajax'         => true,
        'services'     => [ MOODLE_OFFICIAL_MOBILE_SERVICE ],
        'capabilities' => 'moodle/user:viewalldetails',
    ],
    'local_companyservice_get_infocourses' => [
        'classname'   => 'local_companyservice\external\get_infocourses',
        'methodname'  => 'execute',
        'classpath'   => 'local/companyservice/classes/external/get_infocourses.php',
        'description' => 'Get courses information with completion status of user by courseid and username',
        'type'        => 'read',
        'ajax'        => true,
        'services' => [ MOODLE_OFFICIAL_MOBILE_SERVICE ],
        'capabilities' => 'gradereport/user:view',
    ],
    'local_companyservice_get_courseprogress' => [
        'classname'   => 'local_companyservice\external\get_courseprogress',
        'methodname'  => 'execute',
        'classpath'   => 'local/companyservice/classes/external/get_courseprogress.php',
        'description' => 'Get user progress in a course by userid and courseid',
        'type'        => 'read',
        'ajax'        => true,
        'services' => [ MOODLE_OFFICIAL_MOBILE_SERVICE ],
        'capabilities' => 'report/progress:view',
    ],
];

$services = [
    'ws_company' => [
        'functions' => [
            'local_companyservice_get_infouser',
            'local_companyservice_get_infocourses',
            'local_companyservice_get_courseprogress',
        ],
        'requiredcapability' => '',
        'restrictedusers' => 1,
        'enabled' => 1,
        'shortname' =>  'ws_company',
        'downloadfiles' => 0,
        'uploadfiles'  => 0,
    ]
];