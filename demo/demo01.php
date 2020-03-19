<?php
/**
 * Created by PhpStorm.
 * User: jelly
 * Date: 2020-03-11
 * Time: 14:18
 * 本文件示例仅在token service调用
 */
require_once __DIR__ . '/common.php';
$dbConfig = include __DIR__ . '/config/db.php';
\CjsToken\MysqlDbConfig::getInstance()->setDbConfig($dbConfig);
$time = time();

$userTokenPdo = \CjsToken\MysqlPdo::getInstance(\CjsToken\MysqlDbConfig::getInstance()->getDbConfig('db_user_token_1'));
$tables = $userTokenPdo->getTables();
echo var_export($tables, true) . PHP_EOL;

//通过用户ID仅生成uuid，不会有redis和db操作
$userid = "2";
$uuid = \CjsToken\Uuid::generate4help($userid);
echo "uuid: " . $uuid . PHP_EOL;

//生成token,$ext数组的key是字段名
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

//通过token退出登录
$logoutToken = "66f02427dc57d9be3c92a2d178fb54f9";
\CjsToken\UsertokenServer::getInstance()->logoutToken($logoutToken);



