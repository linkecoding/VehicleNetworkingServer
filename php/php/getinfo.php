<?php
	
	//接收数据
	$username = $_POST["username"];
	$password = $_POST["password"];
	$action = $_POST["action"];

	$getInfoObj = new GetInfo($username, $password);
	if ($action != null && @$getInfoObj->res['status'] != 101) {
		$getInfoObj->judge($action);
	}else{
		$getInfoObj->res['status'] = 101;
	}

	if (@$getInfoObj->res['status'] != null) {
		if ($getInfoObj->res['status'] == 202) {
			$getInfoObj->getCar();
		}else{
			echo json_encode($getInfoObj->res);
		}
	}else{
		@$getInfoObj->res['status'] = 000;
		echo json_encode($getInfoObj->res);
	}

	
class GetInfo{
	public $res;
	private $handleMysqlObj;
	private $username;
	private $password;
	private $tablename;
	
	/**
	 * 构造函数,初始化值
	 * @param [type] $username [description]
	 * @param [type] $password [description]
	 * 000 ------ 未知错误
	 * 110 ------ 数据库操作执行异常(查询失败或未查找到)
	 * 101 ------ 传入参数不全
	 * 102 ------ 传入参数错误
	 * 200 ------ 查询到数据
	 * 201 ------ 查询到数据(验证失败)
	 * 202 ------ 用户名密码验证通过
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

	function getCar(){
		$result= array();
		if ($this->handleMysqlObj != null && $this->tablename != null) {
			$sql = "select * from " . $this->tablename . " where username='" . $this->username . "'";
			$result = $this->handleMysqlObj->query($sql);
		
			if ($result) {
				$i = 0;
				while($r = $this->handleMysqlObj->fetchArray($result)){
					$this->res['data'][$i] = $r;
					$i++;
				}
			}
			
			if ($result && @$this->res['data'] != null) {
				$this->res['status'] = 200;
				echo json_encode($this->res);
			}else{
				$this->res['status'] = 110;
				echo json_encode($this->res);
			}
		}
	}

	/**
	 * 验证action 用户名  密码
	 * @param  [type] $action [description]
	 * @return [type]         [description]
	 */
	function judge($action){
		if ($action == 'carinfo1' || $action == 'carinfo2' || $action == 'orderinfo') {
			//设置表名
			$this->tablename = $action;
			$sql_u = "select password from users where username='" . $this->username . "'";
			$flag_u = $this->handleMysqlObj->getOne($sql_u);
			
			if ($flag_u != null) {
				//临时记录查询到的密码
				$pwd = $flag_u['password'];
				if ($pwd == $this->password) {
					$this->res['status'] = 202;
				}else{
					$this->res['status'] = 201;
				}
			}else{
				$this->res['status'] = 110;
			}
		}else{
			$this->res['status'] = 102;
		}
	}
}

?>