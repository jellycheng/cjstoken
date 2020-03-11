<?php
/**
 * Created by PhpStorm.
 * User: jelly
 * Date: 2020-03-11
 * Time: 14:18
 */
require_once __DIR__ . '/common.php';

$userid = "123456";
$uuid = \CjsToken\Uuid::generate4help($userid);
echo "uuid: " . $uuid . PHP_EOL;

$ext = [];
$userToken = \CjsToken\Usertoken::getInstance()->generate4userid($userid, $ext);
echo 'user token: ' . $userToken . PHP_EOL;

