<?php
require_once __DIR__ . '/common.php';
$dbConfig = include __DIR__ . '/config/db.php';
\CjsToken\MysqlDbConfig::getInstance()->setDbConfig($dbConfig);
$time = time();

$userToken = isset($_SERVER['argv'][1]) ? $_SERVER['argv'][1] : "27b4e055-465d-f579-e04a-65ceab36a5a8-1-3";
//通过token获取token信息
$tokenInfo = \CjsToken\UsertokenServer::getInstance()->getTokenInfo($userToken);
var_export($tokenInfo);
echo PHP_EOL . PHP_EOL;

//直接查db
$tokenInfo4Db = \CjsToken\UsertokenServer::getInstance()->getTokenInfo4Db($userToken);
var_export($tokenInfo4Db);

echo PHP_EOL;
