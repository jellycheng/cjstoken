<?php
/**
 * Created by PhpStorm.
 * User: jelly
 * Date: 2020-04-02
 * Time: 09:06
 */
require_once __DIR__ . '/common.php';
use CjsToken\Util;
use CjsToken\MysqlDbConfig;
use CjsToken\MysqlPdo;

//全局初始化db
MysqlDbConfig::getInstance()->setDbConfig(include __DIR__ . '/config/db.php');

//业务逻辑作业 =========
//1.拼接insert sql
$time = time();
$ext = [
    'user_id'=>1234567890,
    'user_token'=>uniqid(),
    'active_time'=>$time,
    'create_time'=>$time,
    'update_time'=>$time,
];
$insertSql = Util::getInsertSql('t_user_token_1', $ext);
echo $insertSql . PHP_EOL;
//执行插入语句，并返回自增ID
$myPdo = MysqlPdo::getInstance(MysqlDbConfig::getInstance()->getDbConfig('db_user_token_1'));
//$insertid = $myPdo->insert($insertSql);
//echo "插入记录成功，返回ID：" . $insertid . PHP_EOL;

//2. 拼接update sql
$updateData =[
        'user_id'=>123,
        'update_time'=>time(),
];
$param = [
        'where'=>['user_token'=>'5e853cb24accc','user_id'=>123], //更新条件,数组方式，可选
        'limit'=>'10',//最多更新记录数，可选
];
$updateSql = Util::getUpdateSql('t_user_token_1', $updateData, $param);
echo $updateSql . PHP_EOL;
//执行更新
$myPdo = MysqlPdo::getInstance(MysqlDbConfig::getInstance()->getDbConfig('db_user_token_1'));
if($updateSql) {
    $affectNum = $myPdo->exec($updateSql);
    echo "更新影响记录数：" . $affectNum . PHP_EOL;
}

$updateData2 =[
    'user_id'=>123,
    'update_time'=>time()+10,
];
$param2 = [
    'where'=>"user_token='5e853cb24accc' and `user_id`='123'", //更新条件,字符串方式，可选
];
$updateSql2 = Util::getUpdateSql('t_user_token_1', $updateData2, $param2);
echo $updateSql2 . PHP_EOL;
//执行更新
$myPdo = MysqlPdo::getInstance(MysqlDbConfig::getInstance()->getDbConfig('db_user_token_1'));
if($updateSql2) {
    $affectNum2 = $myPdo->exec($updateSql2);
    echo "更新影响记录数：" . $affectNum2 . PHP_EOL;
}

//3. 拼接delete sql
$delParam = [
    'where'=>['user_token'=>'5e853cb24accc','user_id'=>123], //删除条件,数组方式，可选
    'limit'=>'10',//最多更新记录数，可选
];
$delSql = Util::getDeleteSql('t_user_token_1', $delParam);
echo $delSql . PHP_EOL;
//执行删除
$myPdo = MysqlPdo::getInstance(MysqlDbConfig::getInstance()->getDbConfig('db_user_token_1'));
if($delSql) {
    $affectNum = $myPdo->exec($delSql);
    echo "删除影响记录数：" . $affectNum . PHP_EOL;
}

$delParam2 = [
    'where'=>"user_token='5e853cb24accc' and `user_id`='123'", //删除条件,字符串方式，可选
];
$delSql2 = Util::getDeleteSql('t_user_token_1', $delParam2);
echo $delSql2 . PHP_EOL;
//执行更新
$myPdo = MysqlPdo::getInstance(MysqlDbConfig::getInstance()->getDbConfig('db_user_token_1'));
if($delSql2) {
    $affectNum2 = $myPdo->exec($delSql2);
    echo "删除影响记录数：" . $affectNum2 . PHP_EOL;
}

//4. 拼接select sql
$selectParam = [
    'field'=>'user_token,user_id', //要查询的字段，默认*，可选
    'where'=>['user_token'=>'26bd489f-cc96-c724-247b-b5cf0ce70177-1-1',
                'user_id'=>'9006923516854095536',
                'is_delete'=>0,
            ], //查询条件,数组方式，可选
    'page'=>1, //页码，可选
    'page_size'=>10, //每页记录数，可选
    'order'=>'active_time desc ', //排序，可选
    'group'=>'user_id', //分组，可选
];
$selectSql = Util::getSelectSql('t_user_token_1', $selectParam);
echo $selectSql . PHP_EOL;
//查询一条记录
$myPdo = MysqlPdo::getInstance(MysqlDbConfig::getInstance()->getDbConfig('db_user_token_1'));
$dataOne = $myPdo->getOne($selectSql);
var_export($dataOne);
//查询多条记录
$dataRes = $myPdo->get($selectSql);
var_export($dataRes);

echo PHP_EOL;
