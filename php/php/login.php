<?php
	
	//接收数据
	$username = $_POST["username"];
	$password = $_POST["password"];

	$loginObj = new Login($username, $password);
	if ($loginObj->res != null) {
		echo json_encode($loginObj->res);
	}else{
		$loginObj->login();
	}

	
class Login{
	public $res;
	private $handleMysqlObj;
	private $username;
	private $password;
	
	/**
	 * 构造函数,初始化值
	 * @param [type] $username [description]
	 * @param [type] $password [description]
	 * 200 ------ 查询到数据(验证成功)
	 * 201 ------ 查询到数据(验证失败)
	 * 110 ------ 数据库操作执行异常(查询失败或未查找到)
	 * 101 ------ 传入参数不全
	 * token为登陆成功之后返回的密码的MD5值
	 */
	function __construct($username, $password){
		$this->res = array();
		if ($username != null && $password != null) {
			//引入配置文件,初始化数据库连接
			require_once("../config/mysqlConfig.php");
			require_once("../class/HandleMysql.class.php");

			$this->handleMysqlObj = new HandleMysql(HOST, USERNAME, PASSWORD, DBNAME, DBCODING);
			$this->username = $username;
			$this->password = $password;
		}else{
			//传入参数不全
			$this->res['status'] = 101;
		}
	}

	function login(){
		if ($this->handleMysqlObj != null) {
			$sql_u = "select password from users where username='" . $this->username . "'";
			$sql_t = "select password from users where telnum='" . $this->username . "'";
			//分别用电话号码和用户名去数据库查询
			$flag_u = $this->handleMysqlObj->getOne($sql_u);
			$flag_t = $this->handleMysqlObj->getOne($sql_t);
			
			if ($flag_u != null || $flag_t != null) {
				//临时记录查询到的密码
				$pwd = null;
				if ($flag_u != null) {
					$pwd = $flag_u['password'];
				}else{
					$pwd = $flag_t['password'];
				}
				if ($pwd == $this->password) {
					$this->res['status'] = 200;
					//$this->res['token'] = md5($this->password);
					echo json_encode($this->res);
				}else{
					$this->res['status'] = 201;
					echo json_encode($this->res);
				}
			}else{
				$this->res['status'] = 110;
				echo json_encode($this->res);
			}
		}
	}
}

?>