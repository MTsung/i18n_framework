<?php
	include_once(APP_PATH.'include/adodb5/adodb.inc.php');// 文件 http://adodb.org/dokuwiki/doku.php
	
	//資料表前墜
	define('PREFIX','database_');

	//資料庫前墜
	define('DB_PREFIX','');

	//主網server name 
	define('MAIN_SERVER_NAME','');

	//是否持續連線 
	$isPConnect = true;

	//資料庫資訊
	$dbHost = "localhost";
	$dbUser = "root";
	$dbPass = "74512345";
	$dbData = DB_PREFIX."system";
	
	//網站資料夾權限判斷
	if(!is_writeable(APP_PATH)){
		echo "unable to write file";
		exit;
	}


	$conn = ADONewConnection("mysqli");
	$connect_check = $isPConnect ? $conn->PConnect($dbHost,$dbUser,$dbPass,$dbData) : $conn->Connect($dbHost,$dbUser,$dbPass,$dbData);

	if(!$connect_check){
		echo "Database connection failed.";
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
	$conn->Execute("SET GLOBAL time_zone = '+08:00';");
	$conn->Execute("SET time_zone = '+08:00';");
	