<?php
	
	//接收数据
	$username = $_POST["username"];
	$password = $_POST["password"];
	$telnum = $_POST["telnum"];

	$registerObj = new Register($username, $password, $telnum);
	if ($registerObj->res != null) {
		echo json_encode($res);
	}else{
		$registerObj->register();
	}

	
class Register{
	public $res;
	private $handleMysqlObj;
	private $username;
	private $password;
	private $telnum;

	
	/**
	 * 构造函数,初始化值
	 * @param [type] $username [description]
	 * @param [type] $password [description]
	 * @param [type] $telnum   [description]
	 * 101 ------ 传入参数不全
	 * 100 ------ 插入数据成功
	 * 110 ------ 数据库操作执行异常
	 */
	function __construct($username, $password, $telnum){
		$this->res = array();
		if ($username != null && $password != null && $telnum != null) {
			//引入配置文件,初始化数据库连接
			require_once("../config/mysqlConfig.php");
			require_once("../class/HandleMysql.class.php");

			$this->handleMysqlObj = new HandleMysql(HOST, USERNAME, PASSWORD, DBNAME, DBCODING);
			$this->username = $username;
			$this->password = $password;
			$this->telnum = $telnum;
		}else{
			//参数不全
			$res['status'] = 101;
			echo json_encode($res);
		}
	}

	function register(){
		//拼接为数组
		$user = array(
			'username' => $this->username,
			'password' => $this->password,
			'telnum' => $this->telnum
		);

		if ($this->handleMysqlObj != null) {
			$flag = $this->handleMysqlObj->insert("users", $user);
			if ($flag) {
				$res['status'] = 100;
				echo json_encode($res);
			}else{
				$res['status'] = 110;
				echo json_encode($res);
			}
		}
	}
}

?>