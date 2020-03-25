<?php
require_once __DIR__ . '/common.php';
//php demo/democlient.php token值

//获取token信息 - 方式1
$token = isset($_SERVER['argv'][1]) ? $_SERVER['argv'][1] : "3e1abc511968973c4ff4ad6fc4cb2f94";
$res = \CjsToken\UsertokenClient::getInstance()->getTokenInfo($token, '', '');

var_export($res);
echo PHP_EOL . " ================ " . PHP_EOL;

$abc = 123;
//获取token信息 - 方式2，考虑token取不到值时再次回调token服务器check token
$res = \CjsToken\UsertokenClient::getInstance()
        ->getTokenInfo($token,
                function ($token, $redisInfo) use($abc){
                    echo "进入redis信息回调：token=" . $token . "，token信息为：" . var_export($redisInfo, true) . PHP_EOL;
                    /**
                    $redisInfo格式如下：
                    array (
                        'app_type' => 'mqj',
                        'app_platform' => 'a',
                        'expire_at' => 1600135861,
                        'device_id' => 'app设备号',
                        'user_token' => '88d7a7a2-5076-8816-a826-8594926f615c-1-3',
                        'user_id' => '2',
                        'active_time' => 1584583861,
                        'create_time' => 1584583861,
                        'update_time' => 1584583861,
                        'out_system' => '',
                    )
                     */
                    echo 'abc=' . $abc . PHP_EOL;
                    if(!$redisInfo) { //请求token服务获取token信息 todo
                        echo "请求token服务获取token信息" . PHP_EOL;

                    }

                    if($redisInfo) {
                        $redisInfo['xyz'] = "今天星期一，别让昨天的悲伤浪费今天的眼泪"; //追加信息返回
                    }
                    //必须返回token信息，可为空，空说明不认这token，即token无效
                    return $redisInfo;
                },
                function ($token, $redisInfo, $newRedisInfo) {
                    //$redisInfo从redis中获取到的值
                    //$newRedisInfo值是上一个回调方法返回的值
                    echo "满足redis时间更新回调：token=" . $token . "，token信息为：" . var_export($redisInfo, true)
                        . "newRedisInfo:" . var_export($newRedisInfo, true) . PHP_EOL;
                    //这里请求token服务, 通过token值调用用户token服务接口重新获取一下token信息即可。token服务器会自动token值判断是否更新db
                    //本方法不要抛任何异常

                }
           );

echo "加工之后的token信息：" . var_export($res, true) . PHP_EOL;



