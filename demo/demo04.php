<?php
require_once __DIR__ . '/common.php';
$dbConfig = include __DIR__ . '/config/db.php';
\CjsToken\MysqlDbConfig::getInstance()->setDbConfig($dbConfig);
$time = time();


//分页查询
$param = [//查询条件
    'tbl_sn'=>2, //表号
    'where'=>'is_delete=0 and user_id>100',
    'page'=>1, //页码
    'page_size'=>30, //每页记录数
    'order'=>'active_time desc ', //排序
];
$resData = \CjsToken\UsertokenServer::getInstance()->selectUserTokenList($param);
var_dump($resData);
echo PHP_EOL . PHP_EOL;

