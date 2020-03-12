<?php
/**
 * Created by PhpStorm.
 * User: jelly
 * Date: 2020-03-11
 * Time: 14:37
 * token客户端和服务端复用代码
 */

namespace CjsToken;

abstract class Usertoken
{

    protected $db_num = 1;  //总共分多少库数
    protected $table_num = 16;  //一个库总共分多少表数
    protected $active_time = 10 * 60 * 60; //单位秒，活动时间阀值，10分钟
    protected $db_charset = 'utf8mb4';
    protected $db_collation = 'utf8mb4_general_ci';
    protected $dbname_part = "db_user_token_";
    protected $tblname_part = "t_user_token_";
    protected $redis_config = [
                                'host'     => '127.0.0.1',
                                'port'     => 6379,
                                'database' => 9,
                                'password' => '',
                                'prefix'   => 'base_user:user:',
                                'desc'     => '登录态专用配置'
                            ];

    protected $db_config = [


                            ];
    const USER_TOKEN_REDIS_GROUP = 'user_token';

    public static function getInstance() {
        static $instance;
        if(!$instance) {
            $instance = new static();
        }
        return $instance;
    }

    public function init($config) {
        static $isinit;
        if($isinit) {
            return $this;
        }
        if(isset($config['db_num'])) {
            $this->db_num = $config['db_num'];
        }
        if(isset($config['table_num'])) {
            $this->table_num = $config['table_num'];
        }
        if(isset($config['active_time'])) {
            $this->active_time = $config['active_time'];
        }
        if(isset($config['db_charset'])) {
            $this->db_charset = $config['db_charset'];
        }
        if(isset($config['db_collation'])) {
            $this->db_collation = $config['db_collation'];
        }
        if(isset($config['dbname_part'])) {
            $this->dbname_part = $config['dbname_part'];
        }
        if(isset($config['tblname_part'])) {
            $this->tblname_part = $config['tblname_part'];
        }
        if(isset($config['redis_config'])) {
            $this->setRedisConfig($config['redis_config']);
        }
        return $this;
    }

    public function getRedisConfig() {
        return $this->redis_config;
    }

    public function setRedisConfig($config = []) {
        $this->redis_config = array_merge($this->redis_config, $config);
        return $this;
    }

    public function getActiveTime()
    {
        return $this->active_time;
    }

    public function setActiveTime($active_time)
    {
        $this->active_time = $active_time;
        return $this;
    }

    //通过用户id返回用户所在库号、表号
    public function getDBBaseByUserid($userid){
        if(!$userid){
            $userid = 1;
        }
        $num = Util::getHashOrd($userid); //0~127
        $database = intval($num/$this->db_num)%$this->db_num+1;
        $table = $num%$this->table_num+1;
        return ['db_sn'=>$database,
                'tbl_sn'=>$table
                ];
    }

    //仅从redis获取token信息，没有就为空
    public function getTokenInfo4Redis($token) {
        static $data = [];
        if(isset($data[$token])) {
            return $data[$token];
        }
        $ret = [];
        $redisTokenInfo = \CjsRedis\Redis::get(self::USER_TOKEN_REDIS_GROUP, Util::getUserTokenKey($token));
        if($redisTokenInfo) {
            $ret = json_decode($redisTokenInfo, true);
            $data[$token] = $ret;
        }
        return $ret;
    }

    //返回是否达到可以更新活动时间，false否、true是
    public function checkActiveTime($tokenInfo, $isAutoUpdateRedis = true) {
        $ret = false;
        $curTime = time();
        $user_id = isset($tokenInfo['user_id'])?$tokenInfo['user_id']:0;
        $activeTime = \CjsRedis\Redis::get(self::USER_TOKEN_REDIS_GROUP, Util::getUserActiveTimeKey($user_id));
        if(!$activeTime) {
            $ret = true;
        } else {
            $lastActiveTime = $activeTime + $this->getActiveTime();
            if($curTime >= $lastActiveTime) {
                $ret = true;
            }
        }

        if($isAutoUpdateRedis && $ret) {
            \CjsRedis\Redis::set(self::USER_TOKEN_REDIS_GROUP, Util::getUserActiveTimeKey($user_id), $curTime, 7*86400);
        }
        return $ret;
    }

}
