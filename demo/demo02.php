<?php
/**
 * 本文件示例仅在token service调用
 * php demo02.php token值
 */

require_once __DIR__ . '/common.php';
$dbConfig = include __DIR__ . '/config/db.php';
\CjsToken\MysqlDbConfig::getInstance()->setDbConfig($dbConfig);
$time = time();

$userToken = isset($_SERVER['argv'][1]) ? $_SERVER['argv'][1] : "27b4e055-465d-f579-e04a-65ceab36a5a8-1-3";
//通过token获取token信息，先查redis-》再查db
$tokenInfo = \CjsToken\UsertokenServer::getInstance()->getTokenInfo($userToken);
var_export($tokenInfo);
echo PHP_EOL . PHP_EOL;
/**
array (
    'app_type' => 'mqj',
    'app_platform' => 'a',
    'expire_at' => 1600135295,
    'device_id' => 'app设备号',
    'user_token' => 'f0bcf783-ab2b-3000-2694-e02d335b8187-1-3',
    'user_id' => '2',
    'active_time' => 1584583295,
    'create_time' => 1584583295,
    'update_time' => 1584583295,
    'out_system' => '',
)
 */
//直接查db
$tokenInfo4Db = \CjsToken\UsertokenServer::getInstance()->getTokenInfo4Db($userToken);
var_export($tokenInfo4Db);

echo PHP_EOL;
