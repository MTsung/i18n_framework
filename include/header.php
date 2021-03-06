<?php
    //跨網域ajax
    // header("Access-Control-Allow-Origin: *");

	//設定時間
	date_default_timezone_set("Asia/Taipei");

	//禁止js取得cookie
	ini_set("session.cookie_httponly", 1);

	//快取關閉
	header("Cache-control:no-cache");

	//設定UTF-8
	header("Content-Type:text/html; charset=utf-8");

	//錯誤訊息直接顯示
    ini_set("display_errors","1");

	//錯誤訊息
	error_reporting(E_ALL & ~E_NOTICE);

	//錯誤訊息全關
	// error_reporting(0);

	//檔案根目錄
	define('APP_PATH',str_replace('\\', '/',substr(__FILE__ , 0 , strlen(__DIR__)-strlen('include'))));

	//errorlog路徑
	ini_set("error_log", APP_PATH."error.log");
	
	//開啟session
	session_start();

	//自動載入calss
	function __autoloadClass($file){
		$file = str_replace("MTsung\\", "", $file);
	    $filename = APP_PATH."/class/".$file.".class.php";
	    if(is_readable($filename)){
	        require $filename;
	    }
	}
	spl_autoload_register('__autoloadClass');
	
	include_once(APP_PATH.'config/define.php');
	include_once(APP_PATH.'include/main.php');//核心

	$design = new MTsung\design();
	$console = new MTsung\main($design);