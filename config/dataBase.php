<?php
	include_once(APP_PATH.'include/adodb5/adodb.inc.php');// 文件 http://adodb.org/dokuwiki/doku.php
	

	//是否持續連線 
	$isPConnect = MTsung\config::DB_IS_PCONNECT;

	//資料庫資訊
	$dbHost = MTsung\config::DB_HOST;
	$dbUser = MTsung\config::DB_USER;
	$dbPass = MTsung\config::DB_PASSWORD;
	$dbData = MTsung\config::DB_NAME;
	
	//網站資料夾權限判斷
	if(!is_writeable(APP_PATH)){
		echo "unable to write file";
		exit;
	}


	$conn = ADONewConnection("mysqli");

	if(!$connect_check = $isPConnect ? $conn->PConnect($dbHost,$dbUser,$dbPass,$dbData) : $conn->Connect($dbHost,$dbUser,$dbPass,$dbData)){
		echo "Database connection failed.";
		error_log("Database connection failed.".$conn->errorMsg());
		exit;
	}

	//關閉嚴格模式
	$conn->Execute("SET sql_mode = 'NO_AUTO_CREATE_USER,NO_ENGINE_SUBSTITUTION';");

	//設定utf8mb4編碼
	$conn->Execute("SET NAMES utf8mb4;");
	$conn->Execute("SET CHARACTER_SET_CLIENT=utf8mb4;");
	$conn->Execute("SET CHARACTER_SET_RESULTS=utf8mb4;");
	$conn->Execute("SET CHARACTER_SET_CONNECTION=utf8mb4;");

	//時區
	$conn->Execute("SET GLOBAL time_zone = '".MTsung\config::TIME_ZONE."';");
	$conn->Execute("SET time_zone = '".MTsung\config::TIME_ZONE."';");
	
	
	
	//定義網站預設controller
	define('INDEX_PATH',"index");

	//定義網站預設語言
	define('LANG',"zh-tw");

	define('DATA_PATH',APP_PATH.'data/10000/');
	define('UPLOAD_PATH',DATA_PATH.'upload/');
	define('OUTPUT_PATH',DATA_PATH.'output/');
	define('LANGUAGE_PATH',DATA_PATH.'language/');

	if(!is_dir(DATA_PATH)) mkdir(DATA_PATH);
	if(!is_dir(UPLOAD_PATH)) mkdir(UPLOAD_PATH);
	if(!is_dir(OUTPUT_PATH)) mkdir(OUTPUT_PATH);

	define('DATA_WEB_PATH',str_replace(APP_PATH,"",WEB_PATH."/".DATA_PATH));