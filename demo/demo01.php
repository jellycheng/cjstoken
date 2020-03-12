<?php
/**
 * Created by PhpStorm.
 * User: jelly
 * Date: 2020-03-11
 * Time: 14:18
 */
require_once __DIR__ . '/common.php';
$dbConfig = include __DIR__ . '/config/db.php';
\CjsToken\MysqlDbConfig::getInstance()->setDbConfig($dbConfig);
$time = time();

$userTokenPdo = \CjsToken\MysqlPdo::getInstance(\CjsToken\MysqlDbConfig::getInstance()->getDbConfig('db_user_token_dev'));
$tables = $userTokenPdo->getTables();
var_export($tables);

$userid = "2";
$uuid = \CjsToken\Uuid::generate4help($userid);
echo "uuid: " . $uuid . PHP_EOL;

//生成token
$ext = [
    'app_type'=>'mqj',
    'app_platform'=>'a',
    'expire_at'=>$time + 86400 * 30 * 6,
    'device_id'=>'app设备号',
];
$userToken = \CjsToken\UsertokenServer::getInstance()->generate4userid($userid, $ext);
echo 'user token: ' . $userToken . PHP_EOL;

//通过token获取token信息
$tokenInfo = \CjsToken\UsertokenServer::getInstance()->getTokenInfo($userToken);
var_export($tokenInfo);

//通过token退出
$logoutToken = "66f02427dc57d9be3c92a2d178fb54f9";
\CjsToken\UsertokenServer::getInstance()->logoutToken($logoutToken);

//通过用户ID，下线所有地方的登录


