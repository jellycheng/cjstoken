<?php
/**
 * Created by PhpStorm.
 * User: jelly
 * Date: 2020-03-12
 * Time: 07:48
 * token服务端：该类仅用于token服务代码中调用
 */

namespace CjsToken;

class UsertokenServer extends Usertoken
{

    //生成token
    public function createToken4userid($userid, $ext = []) {
        return $this->generate4userid($userid, $ext);
    }

    public function generate4userid($userid, $ext = []) {
        $time = time();
        $uuid = Uuid::formatUuid(Uuid::generate4help($userid));
        if(empty($ext)) {
            return $uuid;
        }
        $partDb = $this->getDBBaseByUserid($userid);
        $dbCfgKey = Util::getDbCfgKey($partDb['db_sn']);
        $tableName = Util::getDbTableName($partDb['tbl_sn']);
        $uuid .= "-" . $partDb['db_sn'] . '-' . $partDb['tbl_sn'];
        //插入db
        $ext['user_token'] = $uuid;
        $ext['user_id'] = $userid;
        $ext['active_time'] = $time;
        $ext['create_time'] = $time;
        $ext['update_time'] = $time;
        if(!isset($ext['expire_at'])) {
            $ext['expire_at'] = 0;
        }
        if(!isset($ext['out_system'])) {
            $ext['out_system'] = '';
        }
        $insertSql = Util::getInsertSql($tableName, $ext);
        $userTokenPdo = \CjsToken\MysqlPdo::getInstance(\CjsToken\MysqlDbConfig::getInstance()->getDbConfig($dbCfgKey));
        $insertid = $userTokenPdo->insert($insertSql);
        if(!$insertid) {
            return '';
        }
        //写缓存
        $this->writeToken2Redis($uuid, $ext);

        return $uuid;
    }

    //通过token获取token信息
    public function getTokenInfo($token) {
        $ret = [];
        $redisTokenInfo = $this->getTokenInfo4Redis($token);
        if($redisTokenInfo) {
            $isExpire = $this->checkExpireAt($redisTokenInfo);
            if($isExpire) {//过期
                return $ret;
            }
            $ret = $redisTokenInfo;
        } else { //查db
            $tokenInfo = $this->getTokenInfo4Db($token);
            if(!$tokenInfo) {//无记录
                return $ret;
            }
            $isExpire = $this->checkExpireAt($tokenInfo);
            if($isExpire) {//过期
                return $ret;
            }
            $ret = $tokenInfo;
            $this->writeToken2Redis($token, $ret);
        }
        //回写db，方法自动判断是否写回db
        $this->setActiveTime($ret);

        return $ret;
    }
    //仅查询有效的记录，不错过期判断
    public function getTokenInfo4Db($token) {
        $ret = [];
        if(!$token) {
            return $ret;
        }

        $tmp = Util::getDbTblCfg4Token($token);
        if(empty($tmp)) {
            return $ret;
        }
        $dbCfgKey = $tmp['dbcfgkey'];
        $tableName = $tmp['table_name'];
        $userTokenPdo = \CjsToken\MysqlPdo::getInstance(\CjsToken\MysqlDbConfig::getInstance()->getDbConfig($dbCfgKey));
        $selectSql = sprintf("select * from %s where user_token ='%s' and is_delete=0 limit 1; ",
                                $tableName, Util::htmlspecialchars($token));
        $dataOne = $userTokenPdo->getOne($selectSql);
        if(!empty($dataOne)) {
            $ret  = $dataOne;
        }
        return $ret;
    }

    public function writeToken2Redis($token, $tokenInfo) {
        $bool = \CjsRedis\Redis::set(self::USER_TOKEN_REDIS_GROUP, Util::getUserTokenKey($token),  json_encode($tokenInfo));
        if($bool) {
            \CjsRedis\Redis::EXPIRE(self::USER_TOKEN_REDIS_GROUP, Util::getUserTokenKey($token), 1*86400);
        }
        return $bool;
    }

    public function checkExpireAt($tokenInfo) {
        $flag = false; //标记是否过期，false未过期，true已过期
        $time = time();
        $expire_at = isset($tokenInfo['expire_at'])?$tokenInfo['expire_at']:0;
        if(!$expire_at) {
            return $flag;
        }
        if($expire_at<0) {
            $flag = true;
        } else if($expire_at<=$time) {
            $flag = true;
        }
        $user_token = isset($tokenInfo['user_token'])?$tokenInfo['user_token']:'';
        if(!$user_token) {
            $flag = true;
            return $flag;
        }
        if($flag) {//过期，更新db 软删除，同时清除redis
            $tmp = Util::getDbTblCfg4Token($user_token);
            if(empty($tmp)) {
                return $flag;
            }
            $dbCfgKey = $tmp['dbcfgkey'];
            $tableName = $tmp['table_name'];
            $userTokenPdo = \CjsToken\MysqlPdo::getInstance(\CjsToken\MysqlDbConfig::getInstance()->getDbConfig($dbCfgKey));
            $updateSql = sprintf("update %s set invalid_type=1,is_delete=1,update_time='%s',delete_time='%s' where user_token ='%s' and is_delete=0 limit 1;",
                                $tableName, $time, $time, Util::htmlspecialchars($user_token));
            $affectNum = $userTokenPdo->exec($updateSql);
            //清redis
            \CjsRedis\Redis::DEL(self::USER_TOKEN_REDIS_GROUP, Util::getUserTokenKey($user_token));
        }
        return $flag;
    }

    //通过token退出
    public function logoutToken($token) {
        $flag = false;
        $time = time();
        //软删除db
        $tmp = Util::getDbTblCfg4Token($token);
        if(empty($tmp)) {
            return $flag;
        }
        $dbCfgKey = $tmp['dbcfgkey'];
        $tableName = $tmp['table_name'];
        $userTokenPdo = \CjsToken\MysqlPdo::getInstance(\CjsToken\MysqlDbConfig::getInstance()->getDbConfig($dbCfgKey));
        $updateSql = sprintf("update %s set invalid_type=3, is_delete=1,active_time='%s',update_time='%s',delete_time='%s' where user_token ='%s' and is_delete=0 limit 1;",
            $tableName, $time,$time, $time, Util::htmlspecialchars($token));
        $affectNum = $userTokenPdo->exec($updateSql);

        //清缓存
        \CjsRedis\Redis::DEL(self::USER_TOKEN_REDIS_GROUP, Util::getUserTokenKey($token));
        $flag = true;
        return $flag;
    }

    //通过用户ID退出
    public function logoutToken4userid($userid, $ext = []) {
        //查询最近20条 未退出的记录

        //软删除db

        //清缓存

    }

    //设置活动时间，每隔一定阀值（暂定10分钟）更新一次db
    public function setActiveTime($token) {
        if(is_array($token)) {
            $redisTokenInfo = $token;
        } else {
            $redisTokenInfo = $this->getTokenInfo4Redis($token);
        }

        if($redisTokenInfo) {
            $user_token = isset($redisTokenInfo['user_token'])?$redisTokenInfo['user_token']:'';
            //判断是否会回写db
            $isOk = $this->checkActiveTime($redisTokenInfo, true);
            if($isOk) {//更新db
                $time = time();
                $tmp = Util::getDbTblCfg4Token($user_token);
                if(empty($tmp)) {
                    return false;
                }
                $dbCfgKey = $tmp['dbcfgkey'];
                $tableName = $tmp['table_name'];
                $userTokenPdo = \CjsToken\MysqlPdo::getInstance(\CjsToken\MysqlDbConfig::getInstance()->getDbConfig($dbCfgKey));
                $updateSql = sprintf("update %s set active_time=%s,update_time='%s' where user_token ='%s' and is_delete=0 limit 1;",
                    $tableName, $time, $time, Util::htmlspecialchars($user_token));
                $userTokenPdo->exec($updateSql);//$affectNum
            }

            return true;
        } else {
            return false;
        }
    }

    //通过条件分页查询t_user_token_*表记录
    public function selectUserTokenList($param = []) {
        $dbnum = isset($param['db_sn'])?intval($param['db_sn']):1;
        $tblnum = isset($param['tbl_sn'])?intval($param['tbl_sn']):1;
        $where = isset($param['where'])?intval($param['where']):"";
        if($where) {
            $where = " where " . $where;
        }
        $page = isset($param['page'])?intval($param['page']):1; //页码
        $page_size = isset($param['page_size'])?intval($param['page_size']):10; //每页记录数
        $limit  = Util::getLimit($page, $page_size);
        $selectSql = sprintf("select * from t_user_token_%s %s %s;",
            $tblnum,$where,$limit
        );

    }

}