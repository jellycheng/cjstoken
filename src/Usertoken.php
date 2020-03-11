<?php
/**
 * Created by PhpStorm.
 * User: jelly
 * Date: 2020-03-11
 * Time: 14:37
 */

namespace CjsToken;

class Usertoken
{

    protected $db_num = 1;  //总共分多少库数
    protected $table_num = 16;  //一个库总共分多少表数
    protected $active_time = 10 * 60 * 60; //活动时间阀值，10分钟
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


    public function generate4userid($userid, $ext = []) {
        $uuid = Uuid::generate4help($userid);
        if(empty($ext)) {
            return $uuid;
        }
        //插入db

        return $uuid;
    }

    public function getTokenInfo($token) {

    }

    //通过token退出
    public function logoutToken($token) {

    }
    //通过用户ID退出
    public function logoutToken4userid($userid, $ext = []) {

    }

    //设置活动时间，每隔一定阀值（暂定10分钟）更新一次db
    public function setActiveTime($token) {

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
        $limit  = " limit ";
        $selectSql = sprintf("select * from t_user_token_%s %s %s;",
                            $tblnum,$where,$limit
                            );

    }

}
