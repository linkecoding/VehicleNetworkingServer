<?php
	
	//接收数据
	$username = $_POST["username"];
	$order_time = $_POST["order_time"];
	$station = $_POST["station"];
	$gas_type = $_POST["gas_type"];
	$gas_num = $_POST["gas_num"];
	$gas_fee = $_POST["gas_fee"];
	$is_finished = $_POST["is_finished"];

	$addOrderObj = new AddOrder($username, $order_time, $station, $gas_type, $gas_num,
	 $gas_fee, $is_finished);
	if ($addOrderObj->res != null) {
		echo json_encode($res);
	}else{
		$addOrderObj->add();
	}

	
class AddOrder{
	public $res;
	private $handleMysqlObj;

	private $username;
	private $order_time;
	private $station; 
	private $gas_type;
	private $gas_num;
	private $gas_fee;
	private $is_finished;
	
	/**
	 * 构造函数,初始化值
	 * 101 ------ 传入参数不全
	 * 100 ------ 插入数据成功
	 * 110 ------ 数据库操作执行异常
	 */
	function __construct($username, $order_time, $station, $gas_type, $gas_num,
	 $gas_fee, $is_finished){
		$this->res = array();
		if ($username != null && $order_time != null && $station != null && $gas_type != null
			&& $gas_num != null && $gas_fee != null && $is_finished != null) {
			//引入配置文件,初始化数据库连接
			require_once("../config/mysqlConfig.php");
			require_once("../class/HandleMysql.class.php");

			$this->handleMysqlObj = new HandleMysql(HOST, USERNAME, PASSWORD, DBNAME, DBCODING);
			$this->username = $username;
			$this->order_time = $order_time;
			$this->station = $station;
			$this->gas_type = $gas_type;
			$this->gas_num = $gas_num;
			$this->gas_fee = $gas_fee;
			$this->is_finished = $is_finished;
		}else{
			//参数不全
			$res['status'] = 101;
			echo json_encode($res);
		}
	}

	function add(){
		//拼接为数组
		$orderinfo = array(
			'username' => $this->username,
			'order_time' => $this->order_time,
			'station' => $this->station,
			'gas_type' => $this->gas_type,
			'gas_num' => $this->gas_num,
			'gas_fee' => $this->gas_fee,
			'is_finished' => $this->is_finished
		);
		if ($this->handleMysqlObj != null) {
			$flag = $this->handleMysqlObj->insert("orderinfo", $orderinfo);
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