<?php
class HandleMysql{
    /*对数据库增删改查*/
	private $dbHost;          //数据库地址
    private $dbUser;          //用户名
    private $dbPassword;      //密码
    private $dbName;         //数据库
    private $dbConn;          //数据库连接
    private $result;          //结果集
    private $sql;             //sql语句
    private $coding;          //编码方式
    private $queryNum;        //查询次数

    /**
     * 
     * @param [type] $dbHost     [数据库地址]
     * @param [type] $dbUser     [用户名]
     * @param [type] $dbPassword [密码]
     * @param [type] $dbName    [数据表]
     */
    public function __construct($dbHost, $dbUser, $dbPassword, $dbName, $coding) {
        $this->dbHost = $dbHost;
        $this->dbUser = $dbUser;
        $this->dbPassword = $dbPassword;
        $this->dbName = $dbName;
        $this->coding = $coding;
        $this->connect();
        $this->select_db($dbName);
    }


    /**
     * 连接数据库
     * @return [type] [数据库连接]
     */
    public function connect() {
       
        $this->dbConn = mysql_connect($this->dbHost, $this->dbUser, $this->dbPassword);
        if (!$this->dbConn) {
            $this->halt("不能连接数据库", $this->sql);
            return false;
        }

        if ($this->version() > '4.1') {
            $serverset = $this->coding ? "character_set_connection='$this->coding',character_set_results='$this->coding',character_set_client=binary" : '';
            $serverset .= $this->version() > '5.0.1' ? ((empty($serverset) ? '' : ',') . " sql_mode='' ") : '';
            $serverset && mysql_query("SET $serverset", $this->dbConn);
        }

        return $this->dbConn;
    }

    /**
     * 选择数据库
     * @param  [type] $dbName [选择到的数据库]
     * @return [type]          [是否选择成功]
     */
    public function select_db($dbName) {
        if (!@mysql_select_db($dbName, $this->dbConn)) {
            $this->halt("没有" . $dbName . "这个数据库");
            return false;
        } else {
            $this->dbName = $dbName;
            return true;
        }
    }


    /**
     * 查询数据库
     * @param  [type] $sql  [查询语句]
     * @return [type]       [结果集]
     */
     public function query($sql) {

        if ($query = mysql_query($sql, $this->dbConn)) {
            $this->queryNum++;
            return $query;
        } else {
            $this->halt("Mysql 查询出错", $sql);
            return false;
        }
    }

    /**
     * 插入一组或一条数据
     * @param  [type] $tableName [数据表名]
     * @param  [type] $info      [数据]
     * @return [type]            [description]
     */
    public function insert($tableName, $info) {

        $this->checkFields($tableName, $info);
        $insert_sql = "INSERT INTO `$tableName`(`" . implode('`,`', array_keys($info)) . "`) VALUES('" . implode("','", $info) . "')";
        return $this->query("$insert_sql");
    }

    /**
     * 更新数据表中的信息
     * @param  [type] $tableName [数据表名]
     * @param  [type] $info      [数据]
     * @param  string $where     [插入位置]
     * @return [type]            [description]
     */
    public function update($tableName, $info, $where = '') {
        $this->checkFields($tableName, $info);
        if ($where) {
            $sql = '';

            foreach ($info as $k => $v) {
                $sql .= ", `$k`='$v'";
            }
            $sql = substr($sql, 1);

            $sql = "UPDATE `$tableName` SET $sql WHERE $where";
        } else {
            $sql = "REPLACE INTO `$tableName`(`" . implode('`,`', array_keys($info)) . "`) VALUES('" . implode("','", $info) . "')";
        }
        return $this->query($sql);
    }

    /**
     * 检查一个字段是否在这张表中存在
     *
     * @param string $tableName
     * @param array $array
     * @return message
     */
    public function checkFields($tableName, $array) {

        $fields = $this->getFields($tableName);

        foreach ($array as $key => $val) {
            if (!in_array($key, $fields)) {
                $this->halt("Mysql 错误", "找不到" . $key . "这个字段在" . $tableName . "里面");
                return false;
            }
        }
    }


     /**
      * 获取一张表中的所有字段
      * @param  [type] $tableName [description]
      * @return [type]            [description]
      */
    public function getFields($tableName) {
        $fields = array();
        $result = $this->query("SHOW COLUMNS FROM `$tableName`");
        while ($list = $this->fetchArray($result)) {
            $fields[] = $list['Field'];
        }
        $this->freeResult($result);
        return $fields;
    }


    /**
     * 
     * 释放当前数据库结果集的内存
     * @param  [type] $result [description]
     * @return [type]         [description]
     */
    public function freeResult($result) {
        return @mysql_free_result($result);
    }

    /**
     * 使用while 可以迭代输出 一个结果集中的所有数据,转化为关联数组
     * @param  [type] $query       [description]
     * @param  [type] $result_type [description]
     * @return [type]              [description]
     */
    public function fetchArray($query, $result_type = MYSQL_ASSOC) {
        return mysql_fetch_array($query, $result_type);
    }

    /**
     * 返回一个结果集中的一条数据
     * @param  [type]  $sql     [description]
     * @param  string  $type    [description]
     * @param  integer $expires [description]
     * @return [type]           [description]
     */
    public function getOne($sql, $expires = 3600) {
        $query = $this->query($sql, $expires);
        if (!is_bool($query)) {
            $rs = $this->fetchArray($query);
            $this->freeResult($rs);
            return $rs;
        }else{
            return false;
        }
    }

    /**
     * 返回插入数据的insertid
     * @return [type] [description]
     */
    public function insertId() {
        return mysql_insert_id($this->dbConn);
    }

    /**
     * 获取当前的结果集中存在多少条数据
     * @param  [type] $query [description]
     * @return [type]        [description]
     */
    public function numRows($query) {
        return @mysql_numrows($query);
    }

    /**
     * 获取当前的结果集中，有多少个字段
     *
     * @param Resouce $query
     * @return int fields nums
     */
    public function numFields($query) {
        return @mysql_num_fields($query);
    }

    /**
     * 获取当前执行的sql总条数
     *
     * @return queryNum
     */
    public function getQueryNum() {
        return $this->queryNum;
    }

    /**
     * 获取当前文件中的函数,传入一个当前类存在函数，单例调用
     *
     * @param unknown_type $funcname
     * @param unknown_type $params
     * @return unknown
     */
    public function getFunc($funcname, $params = '') {
        if (empty($params)) {
            return $this->$funcname();
        } else {
            return $this->$funcname($this->getFuncParams($params));
        }
    }

    /**
     * 如果是一个数组，那么拼接起来，处理返回一个参数集合
     *
     * @param array,string $params
     * @return string a,d,3
     */
    public function getFuncParams($params) {
        $returnStr = "";
        if (is_array($params)) {
            foreach ($params as $key => $val) {
                $returnStr .= $val . ",";
            }
            return rtrim($returnStr, ",");
        } else {
            return $params;
        }
    }

    /**
     * 获取当前数据库的版本信息
     *
     * @return version
     */
    public function version() {
        return mysql_get_server_info($this->dbConn);
    }

    
    /**
     * 获取当前mysql数据的报错号
     * @return [type] [description]
     */
    public function errno() {
        return intval(@mysql_errno($this->dbConn));
    }

    /**
     * 获取当前数据库的 提示信息
     * @return [type] [description]
     */
    public function error() {
        return @mysql_error($this->dbConn);
    }

    /**
     * 操作数据库出错的提示信息
     * @param  string $message [description]
     * @param  string $sql     [description]
     * @return [type]          [description]
     */
    function halt($message = '', $sql = '') {
        $this->errormsg = "<b>MySQL Query : </b>$sql <br /><b> MySQL Error : </b>" . $this->error() . " <br /> <b>MySQL Errno : </b>" . $this->errno() . " <br /><b> Message : </b> $message";
        exit($this->errormsg);
        return false;
    }

    /**
     * 展示所有表
     * @return [type] [description]
     */
    function showTable() {
        $tables = array();
        $result = $this->query("SHOW TABLES");
        while ($list = $this->fetchArray($result)) {
            $tables[] = $list['Tables_in_' . $this->dbName];
        }
        $this->freeResult($result);
        return $tables;
    }

}
?>