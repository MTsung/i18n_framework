<?php

/**
 * config
 */
namespace MTsung{

	abstract class config{
		const DB_HOST = "localhost";				//sql host
		const DB_USER = "root";						//sql帳號
		const DB_PASSWORD = "74512345";				//sql密碼
		const DB_NAME = "system";					//DB名
		const DB_IS_PCONNECT = true;				//DB是否持續連接

		const TIME_ZONE = "+08:00";					//時區
		
		const TABLE_PREFIX = "database_";			//網站資料表前墜

		
		const SMTP = [								//預設smtp設定
			'SMTPSecure' => "ssl",
			'Host' => "smtp.gmail.com",
			'Port' => "465",
			'Username' => "",
			'Password' => "",
			'senderEmail' => "",
			'senderName' => "",
		];

		const CSRF_WHITELIST = [					//csrf白名單
		];
	}

}
