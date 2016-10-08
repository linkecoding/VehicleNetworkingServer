<?php
	require_once('../config/carbrand.php');
	$url = "http://www.xinliba.cn/io/php/img/";
	//接收数据
	$username = $_POST["username"];
	$car_brand = $_POST["car_brand"];
	$car_mark = $url . $carbrand[$car_brand] . ".png";
	$car_type = $_POST["car_type"];
	$car_num = $_POST["car_num"];
	$car_engine_num = $_POST["car_engine_num"];
	$car_level = $_POST["car_level"];

	$addCarInfoObj = new AddCarInfo1($username, $car_brand, $car_mark, $car_type, $car_num,
	 $car_engine_num, $car_level);
	if ($addCarInfoObj->res != null) {
		echo json_encode($res);
	}else{
		$addCarInfoObj->add();
	}

	
class AddCarInfo1{
	public $res;
	private $handleMysqlObj;

	private $username;
	private $car_brand;
	private $car_mark; 
	private $car_type;
	private $car_num;
	private $car_engine_num;
	private $car_level;
	
	/**
	 * 构造函数,初始化值
	 * 101 ------ 传入参数不全
	 * 100 ------ 插入数据成功
	 * 110 ------ 数据库操作执行异常
	 */
	function __construct($username, $car_brand, $car_mark, $car_type, $car_num, $car_engine_num, $car_level){
		$this->res = array();
		if ($username != null && $car_brand != null && $car_mark != null && $car_type != null
			&& $car_num != null && $car_engine_num != null && $car_level != null) {
			//引入配置文件,初始化数据库连接
			require_once("../config/mysqlConfig.php");
			require_once("../class/HandleMysql.class.php");

			$this->handleMysqlObj = new HandleMysql(HOST, USERNAME, PASSWORD, DBNAME, DBCODING);
			$this->username = $username;
			$this->car_brand = $car_brand;
			$this->car_mark = $car_mark;
			$this->car_type = $car_type;
			$this->car_num = $car_num;
			$this->car_engine_num = $car_engine_num;
			$this->car_level = $car_level;
		}else{
			//参数不全
			$res['status'] = 101;
			echo json_encode($res);
		}
	}

	function add(){
		//拼接为数组
		$carinfo1 = array(
			'username' => $this->username,
			'car_brand' => $this->car_brand,
			'car_mark' => $this->car_mark,
			'car_type' => $this->car_type,
			'car_num' => $this->car_num,
			'car_engine_num' => $this->car_engine_num,
			'car_level' => $this->car_level
		);
		if ($this->handleMysqlObj != null) {
			$flag1 = $this->handleMysqlObj->insert("carinfo1", $carinfo1);
			$insert_id = $this->handleMysqlObj->insertId();
			$car_id = array(
				'car_id' => $insert_id
				);
			$flag2 = $this->handleMysqlObj->insert("carinfo2", $car_id);
			if ($flag1 && $flag2) {
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