<?php

require_once __DIR__ . '/common.php';
$time = time();

//参与签名的参数
$param = ['client_id'=>'hanwei_app',
            'redirect_uri'=>'http://www.baidu.com/abc/123?a=1',
            'secret'=>'hanwei_app',
            'timestamp'=>$time,
            'user_id'=>'userid123',
        ];
$signAry = \CjsToken\Oauth\SignUtil::sign01($param);
echo 'sign=' . $signAry['sign'] . PHP_EOL;
echo 'sign_src=' . $signAry['sign_src'] . PHP_EOL;

//根据参与签名的参数及签名拼接oauth url
$param2 = $param;
$param2['sign'] = $signAry['sign'];
$param2['server_url'] = 'https://tgls-test.towngasvcc.com/oauth/third/authorize';
$server_url = \CjsToken\Oauth\SignUtil::oauthServerUrl01($param2);
echo $server_url . PHP_EOL;


echo \CjsToken\Oauth\OauthUtil::getInstance()->setClientId("cid")->setSecret('sid')->getAuthorizationHeaderFormat() . PHP_EOL;
