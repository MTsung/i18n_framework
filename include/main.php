<?php

namespace MTsung{

	class main{
		var $conn;
		var $design;

		var $message;
		var $label;

		var $path;

		var $config = [
			"CSRFKey" => "MTsung",							//token金鑰
			"CSRFType" => "md5",							//token加密方式
			"POSTTime" => 0.5,								//連續POST最小時間
			"csrfWhitelist" => config::CSRF_WHITELIST		//csrf白名單
		];
		var $languageArray = [];

		private $templateName = "";

		/**
		 * @param ADO 		$conn 
		 * @param design 	$design  
		 */
		function __construct($conn,$design){
			$this->conn = $conn;
			$this->design = $design;

			//網址處理
			$url = "";
			if($_SERVER['REQUEST_URI']!=$_SERVER['SCRIPT_NAME']){
				$url = $_SERVER['REQUEST_URI'];
			}
			$url = str_replace('?'.$_SERVER['QUERY_STRING'], '', $url);
			$url = substr($url, strlen(WEB_PATH)+1,strlen($url)); ///AAA/BBB

			if (empty($url)){
				$this->path[0] = INDEX_PATH;
			}else{
				$this->path = explode('/', urldecode($url));
			}
			foreach ($this->path as $key => $value) {
				if($value==""){
					unset($this->path[$key]);
				}
			}

			
			$this->setLanguageArray();//語言檔案

			$lang = $this->getUseLanguage();

			if(!is_array($lang) && array_key_exists($lang, $this->languageArray)){
				$this->setLanguage($lang);
			}else{
				setcookie("lang" , '' , time()-157680000, '/');
				$this->setLanguage();
			}

			$this->POSTVerifty();
			$this->CSRFVerifty();
			$this->loadLanguageini();
		}

		/**
		 * 取得目前使用的語言
		 * path優先並存cookie
		 * 沒path先看cookie有沒有，沒有去找瀏覽器語系，從權重高開始找，如果都沒有就預設
		 */
		private function getUseLanguage(){
			$temp = LANG;
			if(array_key_exists($this->path[0], $this->languageArray)){
				$temp = $this->path[0];
				unset($this->path[0]);
				$this->path = array_values($this->path);
				if(!isset($this->path[0])){
					$this->path[0] = INDEX_PATH;
				}
			 	setcookie("lang", $temp, time()+157680000, '/');
			}else if(isset($_COOKIE["lang"])){
			 	$temp = $_COOKIE["lang"];
			}else{
				if(isset($_SERVER['HTTP_ACCEPT_LANGUAGE'])) {
					$temp = explode(",", strtolower($_SERVER['HTTP_ACCEPT_LANGUAGE']));//瀏覽器語系
					foreach ($temp as $key => $value) {
						$temp[$key] = explode(";", $value)[0];
						if(array_key_exists($temp[$key], $this->languageArray)){
							$temp = $value;
							break;
						}
					}
				}
			}
			return $temp;
		}

		/**
		 * 讀取語言訊息ini
		 */
		private function loadLanguageini(){
			$file = LANGUAGE_PATH.$_SESSION[FRAME_NAME]['language'].'.ini';
			if(!is_file($file)){
				$this->alert($this->getLabel('語言檔抓取失敗'),-1);
				exit;
			}else{
				$tmpe = @parse_ini_file($file,true);
				$this->message = @$tmpe['message'];
				$this->label = @$tmpe['label'];
			}
		}

		/**
		 * 讀取language有哪些語言 Array ( [zh-tw] => 繁體中文 )
		 */
		private function setLanguageArray(){
			$dir = dir(LANGUAGE_PATH);
			while($file = $dir->read()) {
			   	if (!is_dir($file) && strpos($file,'.ini')){
			   		$temp = @parse_ini_file(LANGUAGE_PATH.$file,true);
			   		if(isset($temp['value']['LANGUAGE_NAME'])){
			   			$temp = htmlspecialchars($temp['value']['LANGUAGE_NAME']);
			   		}else{
			   			$temp = str_replace('.ini','',$file);
			   		}
			   		$this->languageArray[str_replace('.ini','',$file)] = $temp;
			   	}
			}
			$dir->close();
		}

		/**
		 * 設定語言
		 * @param string $value 語言
		 */
		function setLanguage($value=LANG){
			$file = LANGUAGE_PATH.$value.'.ini';
			if(!is_file($file)){
				echo $this->getMessage('語言檔抓取失敗',$value);
				exit;
			}else{
				$_SESSION[FRAME_NAME]['language'] = $value;
				$this->loadLanguageini();
			}
		}

		/**
		 * 取得語言
		 * @return [type] [description]
		 */
		function getLanguage(){
			return $_SESSION[FRAME_NAME]['language'];
		}

		/**
		 * 顯示訊息
		 * @param  string $value 訊息代碼
		 * @param  array  $data  訊息參數
		 * @return string        訊息
		 */
		function getMessage($value='',$data=array()){
			$str = '';
			if(isset($this->message[$value])){
				$temp = $this->message[$value];
				if (is_array($data) && count($data)>0){
					ksort($data);
					foreach ($data as $k => $v) {
						$temp = str_replace('{'.($k+1).'}',$v,$temp);
					}							
				}
				$str = $temp;
			}else{
				$str = $value;
			}

			return $str;
		}

		/**
		 * 取得label
		 * @param  string $value 代碼
		 * @return string        label
		 */
		function getLabel($value){
			if(isset($this->label[$value])){
				return $this->label[$value];
			}
			return $value;
		}

		/**
		 * 顯示alert
		 * @param  string $message alert訊息
		 * @param  string $url     轉跳網址 -1:上一頁
		 */
		function alert($message,$url=NULL){

			//ajax
			if($this->isAjax()){
				$this->outputJson($url!='-1',$message);
			}

		    $message = str_replace(array("\r", "\n", "\r\n", "\n\r"), '', $message);
			echo "<meta http-equiv=Content-Type content=text/html; charset=utf-8>";
			echo "<script>window.addEventListener('load',function(){";
			if($message){
				echo "alert(\"$message\");";
			}

			if(trim($url) == "-1"){
				echo "javascript:history.back(-1);});</script>";exit;
			}
			echo "location.href='$url';});</script>";exit;
		}

		/**
		 * 加載controller
		 */
		function loadController(){
			$console = $this;
			$tempPath = APP_PATH.'controller/'.$this->path[0];
			foreach ($this->path as $key => $value) {
				if($key == 0) continue;
				if(!is_dir($tempPath)) break;
				$tempPath .= ("/".$value);
			}
			if(is_dir($tempPath)){
				$tempPath .= ("/".INDEX_PATH);
			}
			$__file = $tempPath.'.php';
			if (!is_file($__file)){
				$t = explode("/", $tempPath);
				$t[count($t)-1] = INDEX_PATH;
				$__file = implode("/", $t).'.php';
			}
			if (!is_file($__file)){
				$this->HTTPStatusCode(404);
			}

			if($_GET) $_GET = $this->trimData($_GET);
			if($_POST) $_POST = $this->trimData($_POST);
			
			include_once($__file);
			

			$this->design->setData("_GET", @$this->XXSDataVerifty($_GET));
			$this->design->setData("_POST", @$this->XXSDataVerifty($_POST));
			$this->design->setData("path", @$this->path);
			$this->design->setData("data", @$data);
			$this->design->setData("console", $this);

			if($this->templateName){//自訂樣板名稱
				$this->design->loadDisplay($this->templateName);
			}

			
			$templateDir = current($this->design->tpl->getTemplateDir());
			$tempPath = $templateDir.$this->path[0];
			foreach ($this->path as $key => $value) {
				if($key == 0) continue;
				if(!is_dir($tempPath)) break;
				$tempPath .= ("/".$value);
			}
			if(is_dir($tempPath)){
				$tempPath .= ((($templateDir!=$tempPath)?"/":"").INDEX_PATH);
			}
			$__file = $tempPath.'.html';

			if (!is_file($__file)){
				$t = explode("/", $tempPath);
				$t[count($t)-1] = INDEX_PATH;
				$__file = implode("/", $t).'.html';
			}
			$this->design->loadDisplay(str_replace(str_replace("/", "\\", APP_PATH),"",$__file));
		}


		/**
		 * 設定自訂樣板名稱
		 * @param  string $name 樣板名稱/路徑
		 */
		function setTemplateName($name){
			$this->templateName = $name;
		}

		/**
		 * 防止一直傳送表單防止、重複傳送
		 */
		function POSTVerifty(){
			$nowTime = microtime(true);
			if($_POST){
				if(isset($_SESSION[FRAME_NAME]["POST_TIME"]) && ($nowTime-$_SESSION[FRAME_NAME]["POST_TIME"]<$this->config["POSTTime"])){
					$this->alert($this->getMessage('POST_TOO_FAST',array(round($nowTime-$_SESSION[FRAME_NAME]["POST_TIME"],2))),-1);
					$_SESSION[FRAME_NAME]["POST_TIME"] = $nowTime;
					exit;
				}else{
					$_SESSION[FRAME_NAME]["POST_TIME"] = $nowTime;
				}
			}
		}

		/**
		 * XSS資料處理
		 */
		function XXSDataVerifty($data){
			if($data){
				foreach ($data as $key=>$value){
					$data[$key] = (!is_array($value)) ? /*addslashes*/(htmlspecialchars(/*strip_tags*/($value))) : $this->XXSDataVerifty($value);
				}
			}
			return $data;
	    }

		/**
		 * 頭尾空白處理
		 */
		function trimData($data){
			foreach ($data as $key=>$value){
				$data[$key] = (!is_array($value)) ? trim($value) : $this->trimData($value);
			}
			return $data;
	    }

		/**
		 * 防止CSRF跨站攻擊
		 */
		function CSRFVerifty(){
			if(!in_array($this->path[0], $this->config["csrfWhitelist"]) && $_POST){
				if(isset($_SESSION[FRAME_NAME]['CSRF_TOKEN']) && ($_POST[TOKEN_NAME] == $_SESSION[FRAME_NAME]['CSRF_TOKEN'])){
					unset($_POST[TOKEN_NAME]);
				}else{
					$this->alert($this->getMessage('CSRF_TOKEN_NOT_TRUE'),-1);
					exit;
				}
			}
			
		}

		/**
		 * 取得token
		 * @return [type] [description]
		 */
		function getToken($type=""){
			if(!$_SESSION[FRAME_NAME]['CSRF_TOKEN'] || is_array($_SESSION[FRAME_NAME]['CSRF_TOKEN'])){
				$_SESSION[FRAME_NAME]['CSRF_TOKEN'] = hash_hmac($this->config["CSRFType"] ,rand(),$this->config["CSRFKey"]);
			}
			switch ($type) {
				case 'text':
					return $_SESSION[FRAME_NAME]['CSRF_TOKEN'];
					break;
				case 'name':
					return TOKEN_NAME;
					break;
			}
			return '<input type="hidden" name="'.TOKEN_NAME.'" value="'.$_SESSION[FRAME_NAME]['CSRF_TOKEN'].'">';

		}

		/**
		 * HTTP狀態碼+跳到指定頁面
		 * @param  [type] $num HTTP狀態碼
		 * @param  [type] $url 跳到指定頁面
		 */
		function HTTPStatusCode($num,$url=""){
			static $http = array (
				100 => "HTTP/1.1 100 Continue",
				101 => "HTTP/1.1 101 Switching Protocols",
				200 => "HTTP/1.1 200 OK",
				201 => "HTTP/1.1 201 Created",
				202 => "HTTP/1.1 202 Accepted",
				203 => "HTTP/1.1 203 Non-Authoritative Information",
				204 => "HTTP/1.1 204 No Content",
				205 => "HTTP/1.1 205 Reset Content",
				206 => "HTTP/1.1 206 Partial Content",
				300 => "HTTP/1.1 300 Multiple Choices",
				301 => "HTTP/1.1 301 Moved Permanently",
				302 => "HTTP/1.1 302 Found",
				303 => "HTTP/1.1 303 See Other",
				304 => "HTTP/1.1 304 Not Modified",
				305 => "HTTP/1.1 305 Use Proxy",
				307 => "HTTP/1.1 307 Temporary Redirect",
				400 => "HTTP/1.1 400 Bad Request",
				401 => "HTTP/1.1 401 Unauthorized",
				402 => "HTTP/1.1 402 Payment Required",
				403 => "HTTP/1.1 403 Forbidden",
				404 => "HTTP/1.1 404 Not Found",
				405 => "HTTP/1.1 405 Method Not Allowed",
				406 => "HTTP/1.1 406 Not Acceptable",
				407 => "HTTP/1.1 407 Proxy Authentication Required",
				408 => "HTTP/1.1 408 Request Time-out",
				409 => "HTTP/1.1 409 Conflict",
				410 => "HTTP/1.1 410 Gone",
				411 => "HTTP/1.1 411 Length Required",
				412 => "HTTP/1.1 412 Precondition Failed",
				413 => "HTTP/1.1 413 Request Entity Too Large",
				414 => "HTTP/1.1 414 Request-URI Too Large",
				415 => "HTTP/1.1 415 Unsupported Media Type",
				416 => "HTTP/1.1 416 Requested range not satisfiable",
				417 => "HTTP/1.1 417 Expectation Failed",
				500 => "HTTP/1.1 500 Internal Server Error",
				501 => "HTTP/1.1 501 Not Implemented",
				502 => "HTTP/1.1 502 Bad Gateway",
				503 => "HTTP/1.1 503 Service Unavailable",
				504 => "HTTP/1.1 504 Gateway Time-out"
			);
			header($http[$num]);
			if($url){
				header("Location: $url");
			}
			exit;
		}

		/**
		 * 輸出json
		 * @param  [type] $response [description]
		 * @param  [type] $message  [description]
		 * @param  [type] $data  	[description]
		 * @return [type]           [description]
		 */
		function outputJson($response,$message="",$data=""){
			header('Content-Type: application/json; charset=utf-8');
			ob_clean();
			echo json_encode(array(
				"response" => $response,
				"message" => $message,
				"data" => $data
			));
			exit;
		}

		/**
		 * 判斷是否為ajax
		 * @return boolean [description]
		 */
		function isAjax(){
			return isset($_SERVER['HTTP_X_REQUESTED_WITH']) && (strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest');
		}
	}
}
