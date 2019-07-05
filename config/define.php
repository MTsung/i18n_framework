<?php
	//網站token名字
	define('TOKEN_NAME','MTsung_token');

	//網站SESSION陣列名稱
	define('FRAME_NAME','MTsung');
	
	//定義網站根目錄
	define('WEB_PATH',str_replace(str_replace("\\","/",$_SERVER['DOCUMENT_ROOT']),"",str_replace("\\","/",dirname(dirname(__FILE__)))));

	//定義網站預設controller
	define('INDEX_PATH',MTsung\config::INDEX_PATH);
	
	//定義網站預設語言
	define('LANG',MTsung\config::LANG);

	//資料表前墜
	define('PREFIX',MTsung\config::TABLE_PREFIX);

	//現在時間
	define('DATE',date("Y-m-d H:i:s"));

	define('HTTP',(isset($_SERVER['HTTPS']) && $_SERVER['HTTPS']) ? "https://" : "http://");
	if(php_sapi_name() != "cli"){
		define('HTTP_PATH',HTTP.$_SERVER['HTTP_HOST'].WEB_PATH.'/');
	}
	define('CONTROLLER_PATH',APP_PATH.'controller/');
	define('INCLUDE_PATH',APP_PATH.'include/');
	define('DATA_PATH',APP_PATH.'public/');
	define('UPLOAD_PATH',DATA_PATH.'upload/');
	define('OUTPUT_PATH',DATA_PATH.'output/');
	define('LANGUAGE_PATH',DATA_PATH.'language/');

	if(!is_dir(DATA_PATH)) mkdir(DATA_PATH);
	if(!is_dir(UPLOAD_PATH)) mkdir(UPLOAD_PATH);
	if(!is_dir(OUTPUT_PATH)) mkdir(OUTPUT_PATH);