<?php
/**
 * 本文件示例仅在token service调用
 * 退出示例
 * php demo03.php token值
 */
require_once __DIR__ . '/common.php';
$dbConfig = include __DIR__ . '/config/db.php';
\CjsToken\MysqlDbConfig::getInstance()->setDbConfig($dbConfig);
$time = time();

$userToken = isset($_SERVER['argv'][1]) ? $_SERVER['argv'][1] : "27b4e055-465d-f579-e04a-65ceab36a5a8-1-3";

//通过token退出
$isOk = \CjsToken\UsertokenServer::getInstance()->logoutToken($userToken);
var_dump($isOk);
echo PHP_EOL . PHP_EOL;

//通过用户ID退出登录，$ext为空数组则退出所有终端、否则按$ext配置条件来退出
$userid = 2;
$ext = [//可控制踢部分终端退出，支持的条件：device_id, app_platform, app_type, out_system
    //'app_type'=>'a',
];
$isOk = \CjsToken\UsertokenServer::getInstance()->logoutToken4userid($userid, $ext);
var_dump($isOk);
echo PHP_EOL . PHP_EOL;

echo PHP_EOL;
