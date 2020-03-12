<?php
require_once __DIR__ . '/common.php';
$dbConfig = include __DIR__ . '/config/db.php';
\CjsToken\MysqlDbConfig::getInstance()->setDbConfig($dbConfig);
$time = time();

$userToken = isset($_SERVER['argv'][1]) ? $_SERVER['argv'][1] : "27b4e055-465d-f579-e04a-65ceab36a5a8-1-3";

//通过token退出
$isOk = \CjsToken\UsertokenServer::getInstance()->logoutToken($userToken);
var_dump($isOk);
echo PHP_EOL . PHP_EOL;

//通过用户ID，下线所有地方的登录
$userid = 2;
$ext = [//可控制踢部分终端
    //'app_type'=>'a',
];
$isOk = \CjsToken\UsertokenServer::getInstance()->logoutToken4userid($userid, $ext);
var_dump($isOk);
echo PHP_EOL . PHP_EOL;

echo PHP_EOL;
