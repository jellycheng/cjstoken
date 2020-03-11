<?php
require_once __DIR__ . '/common.php';

$dbConfig = include __DIR__ . '/config/db.php';

$userTokenPdo = \CjsToken\MysqlPdo::getInstance($dbConfig['db_user_token_1']);
$tables = $userTokenPdo->getTables();
var_export($tables);

//插入记录
$time = time();
$ext = [
    'user_id'=>1234567890,
    'user_token'=>uniqid(),
    'active_time'=>$time,
    'create_time'=>$time,
    'update_time'=>$time,
];
$insertSql = \CjsToken\Util::getInsertSql('t_user_token_1', $ext);
echo $insertSql . PHP_EOL;
$insertid = $userTokenPdo->insert($insertSql);
echo "插入ID：" . $insertid . PHP_EOL;

//更新记录
$updateSql = "update t_user_token_1 set active_time=" . $time . ",device_id='" . uniqid("", true) . "' where user_id=" . $ext['user_id'];
$num = $userTokenPdo->exec($updateSql);
echo "影响记录数：" . $num . PHP_EOL;

//查询记录
$selectSql = "select * from t_user_token_1 where user_id = " . $ext['user_id'];
$dataOne = $userTokenPdo->getOne($selectSql);
var_export($dataOne);

$selectSql = "select * from t_user_token_1 where user_id = " . $ext['user_id'] . " limit 0,3";
$dataMore = $userTokenPdo->get($selectSql);
var_export($dataMore);



