<?php
	//網站token名字
	define('TOKEN_NAME','MTsung_token');

	//網站SESSION陣列名稱
	define('SESSION_NAME','MTsung');
	
	//定義網站根目錄
	define('WEB_PATH',str_replace(str_replace("\\","/",$_SERVER['DOCUMENT_ROOT']),"",str_replace("\\","/",dirname(dirname(__FILE__)))));

	//現在時間
	define('DATE',date("Y-m-d H:i:s"));

	//是否ssl
	define('HTTP',(isset($_SERVER['HTTPS']) && $_SERVER['HTTPS']) ? "https://" : "http://");

	//網站網址
	define('HTTP_PATH',HTTP.$_SERVER['HTTP_HOST'].WEB_PATH.'/');

	//定義網站預設controller
	define('DEFAULT_CONTROLLER',"index");

	//定義網站預設語言
	define('LANG',"zh-tw");
