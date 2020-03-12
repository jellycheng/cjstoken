<?php
namespace CjsToken;


class MysqlDbConfig
{
    protected $db_config = [];

    public function getDbConfig($key = null)
    {
        if(is_null($key)) {
            return $this->db_config;
        } else if(isset($this->db_config[$key])) {
            return $this->db_config[$key];
        }
        return [];
    }

    public function setDbConfig($db_config)
    {
        $this->db_config = $db_config;
        return $this;
    }

    public static function getInstance() {
        static $instance;
        if(!$instance) {
            $instance = new static();
        }
        return $instance;
    }

}