<?php
namespace CjsToken;

class MysqlPdo
{
    protected $dbConfig = [
                        'host'=>'localhost',
                        'port'=> 3306,
                        'database'=>'',
                        'username'=>'root',
                        'password'=>'',
                        'charset' => 'utf8mb4',
                        'collation' => 'utf8mb4_general_ci',
                    ];

    public static function getInstance($dbConfig =[]) {
        return new static($dbConfig);
    }

    protected function __construct($dbConfig = []) {
        $this->setConfig($dbConfig);
    }

    public function setConfig($dbConfig = []) {
        $this->dbConfig = array_merge($this->dbConfig, $dbConfig);
        return $this;
    }

    public function getConfig() {
        return $this->dbConfig;
    }

    public function connection() {
        static $conn;
        $dbConfig = $this->dbConfig;
        $dbname = $dbConfig['database'];
        $host = $dbConfig['host'];
        if($dbConfig['port']) {
            $port = $dbConfig['port'];
        } else {
            $port = 3306;
        }

        if(!isset($conn[$dbname])) {
            if($dbConfig['charset']) {
                $charset = $dbConfig['charset'];
            } else {
                $charset = "utf8";
            }
            $charsetSql = "SET NAMES " . $charset;
            $dsn = sprintf("mysql:dbname=%s;host=%s;port=%s",
                            $dbname,$host,$port
                        );
            try{
                $pdo = new \PDO($dsn,
                    $dbConfig['username'],
                    $dbConfig['password'],
                    [
                        \PDO::MYSQL_ATTR_INIT_COMMAND => $charsetSql,
                        \PDO::ATTR_ERRMODE => \PDO::ERRMODE_EXCEPTION
                    ]
                );
                //$pdo->query('set names utf8');
                $conn[$dbname] = $pdo;
            } catch(\Exception $e) {
                echo "db 连接失败，" . $e->getMessage() . PHP_EOL;
                return '';
            }
        }
        return $conn[$dbname];

    }

    //获取所有表名
    public function getTables() {
        $sql = "show tables;";
        $ret = [];
        $pdo = $this->connection();
        if(!$pdo) {
            return $ret;
        }
        $pdoQuery = $pdo->query($sql);
        $dataRes = $pdoQuery->fetchAll(\PDO::FETCH_ASSOC);
        if($dataRes) {
            foreach ($dataRes as $tblTmp) {
                $tblnameAry = array_values($tblTmp);
                $ret[] = $tblnameAry[0];
            }
        }
        return $ret;

    }

    //获取表结构
    public function getDbTableSchema($table) {
        $ret = '';
        $pdo = $this->connection();
        if(!$pdo) {
            return $ret;
        }
        $sql = "show create table {$table};";
        $pdoQuery = $pdo->query($sql);
        $dataRes = $pdoQuery->fetchAll(\PDO::FETCH_ASSOC);
        if(!$dataRes) {
            return $ret;
        }
        $ret = $dataRes[0]['Create Table'];
        return $ret;
    }

    /**
     * 获取表字段
     * @param $table
     * @return array
     */
    public function getDbTableFields($table) {
        $pdo = $this->connection();
        if(!$pdo) {
            return [];
        }
        $sql = "show full fields from {$table};";
        $pdoQuery = $pdo->query($sql);
        $dataRes = $pdoQuery->fetchAll(\PDO::FETCH_ASSOC);
        if(!$dataRes) {
            $dataRes = [];
        }
        return $dataRes;
    }

    /**
     * 解析出注释内容
     * @param $content
     * @return string
     */
    public function getComment($content) {
        $ret = '';
        if($content) {
            preg_match("/COMMENT='(.*)'/i", $content, $tmp);
            if(isset($tmp[1])) {
                $ret = $tmp[1];
            }
        }
        return $ret;
    }

    /**
     * 查询数据，多条
     * @param $sql
     * @return array|string
     */
    public function get($sql) {
        $ret = [];
        $pdo = $this->connection();
        if(!$pdo) {
            return $ret;
        }
        $pdoQuery = $pdo->query($sql);
        $dataRes = $pdoQuery->fetchAll(\PDO::FETCH_ASSOC);
        if($dataRes) {
            foreach ($dataRes as $tblTmp) {
                $ret[] = $tblTmp;
            }
        }
        return $ret;
    }

    /**
     * 查询数据，一条
     * @param $sql
     * @return array|string
     */
    public function getOne($sql) {
        $ret = [];
        $pdo = $this->connection();
        if(!$pdo) {
            return $ret;
        }
        $pdoQuery = $pdo->query($sql);
        $dataRes = $pdoQuery->fetch(\PDO::FETCH_ASSOC);
        if($dataRes) {
            $ret = $dataRes;
        }
        return $ret;
    }

    /**
     * 执行插入sql
     * @param $sql
     * @return string
     */
    public function insert($sql) {
        $pdo = $this->connection();
        if(!$pdo) {
            return 0;
        }
        $pdo->query($sql);
        $insertId = $pdo->lastInsertId();
        return $insertId;
    }

    /**
     * 更新sql和delete sql，返回影响的记录数
     * @param $sql
     * @return string
     */
    public function exec($sql) {
        $pdo = $this->connection();
        if(!$pdo) {
            return 0;
        }
        $ret = $pdo->exec($sql);
        return $ret;
    }

    public function getPdo() {
        $ret = '';
        $pdo = $this->connection();
        if(!$pdo) {
            return $ret;
        }
        return $pdo;
    }

}
