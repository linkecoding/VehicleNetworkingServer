<?php
	//接收数据
	@$id = $_POST['id'];
	@$username = $_POST["username"];
	@$car_mileage = $_POST["car_mileage"];
	@$car_gasnum = $_POST["car_gasnum"];
	@$car_engine_ok = $_POST["car_engine_ok"];
	@$car_transmission_ok = $_POST["car_transmission_ok"];
	@$car_light_ok = $_POST["car_light_ok"];

	$addCarInfoObj = new AddCarInfo2($id, $username, $car_mileage, $car_gasnum, $car_engine_ok, 
		$car_transmission_ok, $car_light_ok);
	if ($addCarInfoObj->res != null) {
		echo json_encode($res);
	}else{
		$addCarInfoObj->add();
		echo 'INININ';
		if ($addCarInfoObj->setMessage() != "") {
			$username = "小红";
			$notification_content = $addCarInfoObj->setMessage();
			$notification_title = "车辆异常提醒";
			$jpushObj = new MyJpush($username, $notification_content, $notification_title);
			$res = $jpushObj->sendNotice();
			if ($res != null) {
				echo "推送成功";
			}else{
				echo "推送失败";
			}
		}
	}

	
class AddCarInfo2{
	public $res;
	private $handleMysqlObj;

	private $id;
	private $username;
	private $car_mileage;
	private $car_gasnum;
	private $car_engine_ok;
	private $car_transmission_ok;
	private $car_light_ok;

	/**
	 * 构造函数,初始化值
	 * 101 ------ 传入参数不全
	 * 100 ------ 插入数据成功
	 * 110 ------ 数据库操作执行异常
	 */
	function __construct($id, $username, $car_mileage, $car_gasnum, $car_engine_ok, 
		$car_transmission_ok, $car_light_ok){
		$this->res = array();
		if ($id != null && $username != null && $car_mileage != null && $car_gasnum != null && $car_engine_ok != null
		 && $car_transmission_ok != null && $car_light_ok != null){
			//引入配置文件,初始化数据库连接
			require_once("../config/mysqlConfig.php");
			require_once("../class/HandleMysql.class.php");
			require_once("../jpush/jpush.php");

			$this->handleMysqlObj = new HandleMysql(HOST, USERNAME, PASSWORD, DBNAME, DBCODING);

			$this->id = $id;
			$this->username = $username;
			$this->car_mileage = $car_mileage;
			$this->car_gasnum = $car_gasnum;
			$this->car_engine_ok = $car_engine_ok;
			$this->car_transmission_ok = $car_transmission_ok;
			$this->car_light_ok = $car_light_ok;
		}else{
			//参数不全
			$res['status'] = 101;
			echo json_encode($res);
		}
	}

	function add(){
		//拼接为数组
		$carinfo2 = array(
			'username' => $this->username,
			'car_mileage' => $this->car_mileage,
			'car_gasnum' => $this->car_gasnum,
			'car_engine_ok' => $this->car_engine_ok,
			'car_transmission_ok' => $this->car_transmission_ok,
			'car_light_ok' => $this->car_light_ok
		);
		$where = "car_id = $this->id";
		if ($this->handleMysqlObj != null) {
			$flag = $this->handleMysqlObj->update("carinfo2", $carinfo2, $where);
			if ($flag) {
				$res['status'] = 100;
				echo json_encode($res);
			}else{
				$res['status'] = 110;
				echo json_encode($res);
			}
		}
	}

	function setMessage(){
		echo $this->car_mileage;
		$message = "";
		if ($this->car_mileage > 15000) {
			$message .= "汽车里程数已超15000公里,请及时去维修";
		}else if ($this->car_gasnum < 0.2) {
			$message .= "汽油量已不足20%,请及时去加油站加油";
		}else if ($this->car_engine_ok == "0") {
			$message .= "发动机异常";
		}else if ($this->car_transmission_ok == "0") {
			$message .= "变速器异常";
		}else if ($this->car_light_ok == "0") {
			$message .= "车灯异常";
		}
		return $message;
	}
}

?>