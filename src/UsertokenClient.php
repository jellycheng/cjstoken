<?php
/**
 * Created by PhpStorm.
 * User: jelly
 * Date: 2020-03-12
 * Time: 07:42
 * token客户端：该类仅用于token服务接入方调用
 */

namespace CjsToken;


class UsertokenClient extends Usertoken
{

    public function getTokenInfo($token, $redisTokenInfoCallback, $activeTimeCallback) {
        $ret = [];
        $redisTokenInfo = $this->getTokenInfo4Redis($token);
        if(!$redisTokenInfo) {
            $redisTokenInfo = [];
        }
        if (is_callable($redisTokenInfoCallback)) {
            $ret = call_user_func($redisTokenInfoCallback, $token, $redisTokenInfo);
        } else {
            $ret = $redisTokenInfo;
        }

        if($ret) {
            //判断是否会回写db
            $isCall = is_callable($activeTimeCallback);
            if($this->checkActiveTime($redisTokenInfo, $isCall) && $isCall) {
                call_user_func($activeTimeCallback, $token, $redisTokenInfo, $ret);
            }
        }

        return $ret;
    }

}