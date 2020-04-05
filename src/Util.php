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
        if($page<1) {
            $page = 1;
        }
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

    public static function getUpdateSql($table, $updateData = [], $param=[]) {
        $setFiled = '';
        foreach($updateData as $k=>$v) {
            if($setFiled) {
                $setFiled .= "," . static::parseKey($k) . "=" . static::parseValue($v);
            } else {
                $setFiled .= static::parseKey($k) . "=" . static::parseValue($v);
            }

        }
        if(!$setFiled) {
            return '';
        }
        $where = '';
        if(isset($param['where'])) {
            if(is_array($param['where'])) {
                foreach ($param['where'] as $whereK=>$whereV) {
                    if($where) {
                        $where .= " and " . static::parseKey($whereK) . "=" . static::parseValue($whereV);
                    } else {
                        $where .= static::parseKey($whereK) . "=" . static::parseValue($whereV);
                    }
                }
            } else {
                $where .= $param['where'];
            }
        }
        if(!$where) {
            $where = '1=1';
        }
        $limit = '';
        if(isset($param['limit']) && is_numeric($param['limit'])) {
            $limit = "limit " . $param['limit'];
        }

        $sql = sprintf('UPDATE `%s` set %s where %s %s;',
                            $table,$setFiled,$where,$limit
                        );
        return $sql;
    }

    public static function getDeleteSql($table, $param=[]) {
        $where = '';
        if(isset($param['where'])) {
            if(is_array($param['where'])) {
                foreach ($param['where'] as $whereK=>$whereV) {
                    if($where) {
                        $where .= " and " . static::parseKey($whereK) . "=" . static::parseValue($whereV);
                    } else {
                        $where .= static::parseKey($whereK) . "=" . static::parseValue($whereV);
                    }
                }
            } else {
                $where .= $param['where'];
            }
        }
        if($where) {
            $where = 'where ' . $where;
        } else {//为了安全不支持无条件删除
            return '';
        }
        $limit = '';
        if(isset($param['limit']) && is_numeric($param['limit'])) {
            $limit = "limit " . $param['limit'];
        }
        $sql = sprintf('DELETE FROM `%s` %s %s;',
                        $table,$where,$limit
                    );
        return $sql;
    }


    public static function getSelectSql($table, $param=[]) {
        $selectField = '';
        if(isset($param['field'])) {
            if(is_array($param['field'])) {
                foreach ($param['field'] as $fieldK=>$fieldV) {
                    if($selectField) {
                        $selectField .= "," . static::parseKey($fieldV);
                    } else {
                        $selectField .= static::parseKey($fieldV);
                    }
                }
            } else {
                $selectField = $param['field'];
            }
        }
        if(!$selectField) {
            $selectField = '*';
        }
        $where = '';
        if(isset($param['where'])) {
            if(is_array($param['where'])) {
                foreach ($param['where'] as $whereK=>$whereV) {
                    if($where) {
                        $where .= " and " . static::parseKey($whereK) . "=" . static::parseValue($whereV);
                    } else {
                        $where .= static::parseKey($whereK) . "=" . static::parseValue($whereV);
                    }
                }
            } else {
                $where .= $param['where'];
            }
        }
        if($where) {
            $where = 'where ' . $where;
        }
        $limit = '';
        if(isset($param['limit']) && is_numeric($param['limit'])) {
            $limit = "limit " . $param['limit'];
        }
        $order = isset($param['order'])?$param['order']:"";
        if($order) {
            $order = "order by " . $order;
        }
        $group = isset($param['group'])?$param['group']:"";
        if($group) {
            $group = "group by " . $group;
        }
        $page = isset($param['page'])?intval($param['page']):1; //页码
        $page_size = isset($param['page_size'])?intval($param['page_size']):10; //每页记录数
        if(!$limit) {
            $limit  = Util::getLimit($page, $page_size);
        }
        //select 字段 from 表 where条件 group by分组 order by排序 limit限制
        $sql = sprintf('SELECT %s FROM `%s` %s %s %s %s',
                            $selectField,$table,$where,$group,$order,$limit
                        );
        $sql = trim($sql) . ";";
        return $sql;
    }

    public static function getUserTokenKey($token) {
        return sprintf("usertoken:%s", $token);
    }

    public static function getUserActiveTimeKey($userid) {
        return sprintf("useractivet:%s", $userid);
    }

    public static function getDbCfgKey($dbSn) {
        if(UsertokenServer::getInstance()->getFixDbname()) {
            return UsertokenServer::getInstance()->getFixDbname();
        }
        if(!is_numeric($dbSn) || $dbSn>UsertokenServer::getInstance()->getDbNum() || $dbSn<1) {
            $dbSn = 1;
        }
        $dbNamePrefix = UsertokenServer::getInstance()->getDbnamePart();
        if($dbNamePrefix) {
            return $dbNamePrefix . $dbSn;
        }
        return 'db_user_token_' . $dbSn;
    }

    public static function getDbTableName($tblSn) {
        if(UsertokenServer::getInstance()->getFixTblname()) {
            return UsertokenServer::getInstance()->getFixTblname();
        }
        if(!is_numeric($tblSn) || $tblSn>UsertokenServer::getInstance()->getTableNum() || $tblSn<1) {
            $tblSn = 1;
        }
        $tblNamePrefix = UsertokenServer::getInstance()->getTblnamePart();
        if($tblNamePrefix) {
            return $tblNamePrefix . $tblSn;
        }
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