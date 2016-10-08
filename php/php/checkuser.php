<?php
	
	//接收数据
	$username = $_POST["username"];
	$telnum = $_POST["telnum"];

	$checkUserObj = new CkeckUser($username, $telnum);
	if ($checkUserObj->res != null) {
		echo json_encode($checkUserObj->res);
	}else{
		$checkUserObj->check();
	}

	
class CkeckUser{
	public $res;
	private $handleMysqlObj;
	private $username;
	private $telnum;
	
	/**
	 * 构造函数,初始化值
	 * @param [type] $username [description]
	 * @param [type] $telnum [description]
	 * 001 ------ 用户名存在
	 * 110 ------ 数据库操作执行异常(查询失败或未查找到)
	 * 101 ------ 传入参数不全
	 */
	function __construct($username, $telnum){
		$this->res = array();
		if ($username != null && $telnum != null) {
			//引入配置文件,初始化数据库连接
			require_once("../config/mysqlConfig.php");
			require_once("../class/HandleMysql.class.php");

			$this->handleMysqlObj = new HandleMysql(HOST, USERNAME, PASSWORD, DBNAME, DBCODING);
			$this->username = $username;
			$this->telnum = $telnum;
		}else{
			//传入参数不全
			$this->res['status'] = 101;
		}
	}

	function check(){
		if ($this->handleMysqlObj != null) {
			$sql_u = "select telnum from users where username='" . $this->username . "'";
			$sql_t = "select telnum from users where telnum='" . $this->telnum . "'";
			//分别用电话号码和用户名去数据库查询
			$flag_u = $this->handleMysqlObj->getOne($sql_u);
			$flag_t = $this->handleMysqlObj->getOne($sql_t);
			
			if ($flag_u != null || $flag_t != null) {
				$this->res['status'] = 001;
				echo json_encode($this->res);
			}else{
				$this->res['status'] = 110;
				echo json_encode($this->res);
			}
		}
	}
}

?>