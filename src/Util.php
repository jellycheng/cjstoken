<?php
/**
 * Created by PhpStorm.
 * User: jelly
 * Date: 2020-03-11
 * Time: 16:31
 */

namespace CjsToken;


class Util
{
    public static function htmlspecialchars($val, $flags = ENT_QUOTES, $encoding = 'utf-8') {
        if(!$val || !is_string($val)) {
            return $val;
        }
        return htmlspecialchars($val, $flags, $encoding);
    }


    //去除输入有争议的字符
    public static function randStr($length = 6, $type = 0) {
        //l、o、L、O、数字0、数字1
        if($type == 1) {
            $chars = "ABCDEFGHIJKMNPQRSTUVWXYZ23456789";
        } else {
            $chars = "abcdefghijkmnpqrstuvwxyzABCDEFGHIJKMNPQRSTUVWXYZ23456789";
        }
        $str = "";
        for($i = 0; $i < $length; $i++) {
            $str .= $chars[mt_rand(0, strlen($chars) - 1)];
        }
        return $str;
    }

    /**
     * 返回0～127
     * @param $str
     * @return int
     */
    public static function getHashOrd($str){
        $n=0;
        if(is_numeric($str)) { //是数值型，则后面直接取模
            $n = intval($str);
        } else {
            $str = trim($str . '');
            $len = mb_strlen($str);
            for($i=0;$i<$len;$i++){
                $n+=ord($str[$i]);
            }
        }
        $res = $n%128;
        return $res;
    }

}