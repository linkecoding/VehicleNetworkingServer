<?php
	/**
	* JPush服务器端推送
	*/
	class MyJpush{
		private $username;
		private $notification_content;
		private $notification_title;
		private $client;

		function __construct($username, $notification_content, $notification_title){
			echo "MMM" + $username + $notification_content + $notification_title;
			if ($username != null && $notification_content != null && $notification_title != null) {
				$this->username = $username;
				$this->notification_content = $notification_content;
				$this->notification_title = $notification_title;

				require_once('JPush/JPush.php');
				$app_key = '714d3a11f0af619bf14a719a';
				$master_secret = 'f17826095e952c88a089c546';
				// 初始化
				$this->client = new JPush($app_key, $master_secret);
			}else{
				echo "传入参数不全或参数为空";
			}

		}

		function sendNotice(){
			// 完整的推送示例,包含指定Platform,指定Alias,Tag,指定iOS,Android notification,指定Message等
			$result = $this->client->push()
			    ->setPlatform('android')
			    ->addAllAudience()
			    //->addAlias('alias1')
			    ->addAndroidNotification($this->notification_content, $this->notification_title, 1, null)
			    ->setOptions(100000, 3600, null, false)
			    ->send();
			return $result;
		}

	}