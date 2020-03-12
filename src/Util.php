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

    public static function getLimit($page, $page_size = 10) {
        $start = ($page-1)*$page_size;
        $limit = " limit " . $start . " ," . $page_size;
        return $limit;
    }

    public static function parseKey($key) {
        $key   =  trim($key);
        if(!preg_match('/[,\'\"\*\(\)`.\s]/', $key)) {
            $key = '`'.$key.'`';
        }
        return $key;
    }


    public static function parseValue($value){
        $value = addslashes(stripslashes($value));//重新加斜线，防止从数据库直接读取出错
        return "'".$value."'";
    }

    public static function getInsertSql($table, $insertData = []) {
        $fields = [];
        $values = [];
        foreach($insertData as $k=>$v) {
            $fields[] = static::parseKey($k);
            $values[] = static::parseValue($v);
        }
        $sql = sprintf('INSERT INTO `%s` (%s) VALUES(%s);',
                        $table,implode(',',$fields),implode(',',$values)
                );
        return $sql;
    }

    public static function getUserTokenKey($token) {
        return sprintf("usertoken:%s", $token);
    }

    public static function getUserActiveTimeKey($userid) {
        return sprintf("useractivet:%s", $userid);
    }

    public static function getDbCfgKey($dbSn) {
        return 'db_user_token_' . $dbSn;
    }

    public static function getDbTableName($tblSn) {
        return 't_user_token_' . $tblSn;
    }

    public static function getDbTblCfg4Token($token) {
        $ret = [];
        $tmp = explode('-', $token);
        if(count($tmp) < 4) {
            return $ret;
        }
        $tbl_sn = array_pop($tmp);
        $db_sn = array_pop($tmp);
        if(!is_numeric($db_sn)) {
            $db_sn = 1;
        }
        if(!is_numeric($tbl_sn) || !is_numeric($db_sn)) {
            return $ret;
        }
        $ret['dbcfgkey'] = self::getDbCfgKey($db_sn);
        $ret['table_name'] = self::getDbTableName($tbl_sn);
        return $ret;
    }

}