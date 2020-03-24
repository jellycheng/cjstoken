<?php
namespace CjsToken\Oauth;
/**
 * Created by PhpStorm.
 * User: jelly
 * Date: 2020-03-24
 * Time: 12:57
 * 签名算法定了之后，不会修改，只会新增算法方法
 */

class SignUtil
{

    //签名
    public static function sign01($param=[]) {
        $client_id = isset($param['client_id'])?$param['client_id']:'';
        $redirect_uri = isset($param['redirect_uri'])?$param['redirect_uri']:'';
        $secret = isset($param['secret'])?$param['secret']:'';
        $timestamp = isset($param['timestamp'])?$param['timestamp']:'';
        $user_id = isset($param['user_id'])?$param['user_id']:'';
        $sign_src = $client_id.$redirect_uri.$secret.$timestamp.$user_id;
        $sign = strtoupper(md5($sign_src));
        return ['sign'=>$sign, 'sign_src'=>$sign_src];
    }

    //拼接oauth url
    public static function oauthServerUrl01($param=[]) {
        $server_url = isset($param['server_url'])?$param['server_url']:'';
        $client_id = isset($param['client_id'])?$param['client_id']:'';
        $redirect_uri = isset($param['redirect_uri'])?$param['redirect_uri']:'';
        $timestamp = isset($param['timestamp'])?$param['timestamp']:'';
        $user_id = isset($param['user_id'])?$param['user_id']:'';
        $sign = isset($param['sign'])?$param['sign']:'';
        $url = sprintf("%s?userId=%s&timestamp=%s&redirect_uri=%s&clientId=%s&sign=%s",
                            $server_url,$user_id,$timestamp,urlencode($redirect_uri),$client_id,$sign);
        return $url;
    }

}
