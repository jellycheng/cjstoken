<?php
return [
    'db_user_token_local' => [
        'driver'    => 'mysql',
        'host'     => 'localhost',
        'database' => 'db_user_token_1',
        'username' => 'root',
        'password' => '888888',
        'port'     => 3306,
        'sticky'    => true,//必须为true
        'charset'   => 'utf8mb4',
        'collation' => 'utf8mb4_general_ci',
        'prefix'    => ''
    ],
    'db_user_token_1' => [
        'driver'    => 'mysql',
        'host'     => '',
        'database' => '',
        'username' => 'root',
        'password' => '',
        'port'     => 3306,
        'sticky'    => true,//必须为true
        'charset'   => 'utf8mb4',
        'collation' => 'utf8mb4_general_ci',
        'prefix'    => ''
    ]
];
