<?php

namespace MTsung{

	class main{
		var $design;

		var $message;
		var $label;

		var $path;

		var $config = [
			"CSRFKey" => "MTsung",							//token金鑰
			"CSRFType" => "md5",							//token加密方式
			"csrfWhitelist" => [

			]												//csrf白名單
		];
		var $languageArray = [];

		/**
		 * @param design 	$design  
		 */
		function __construct($design){
			$this->design = $design;

			//網址處理
			$url = "";
			if($_SERVER["REQUEST_URI"]!=$_SERVER["SCRIPT_NAME"]){
				$url = $_SERVER["REQUEST_URI"];
			}
			$url = str_replace("?".$_SERVER["QUERY_STRING"], "", $url);
			$url = substr($url, strlen(WEB_PATH) + 1,strlen($url)); ///AAA/BBB

			if (empty($url)){
				$this->path[0] = DEFAULT_CONTROLLER;
			}else{
				$this->path = explode("/", urldecode($url));
			}
			foreach ($this->path as $key => $value) {
				if($value == ""){
					unset($this->path[$key]);
				}
			}

			
			$this->loadLanguageFile();//語言檔案

			$lang = $this->getUseLanguage();

			define("HTTP_URL",HTTP_PATH.$lang);

			if(is_string($lang) && array_key_exists($lang, $this->languageArray)){
				$this->setLanguage($lang);
			}else{
				setcookie("lang" , "" , time() - 157680000, "/");
				$this->setLanguage();
			}

			$this->CSRFVerifty();
		}

		/**
		 * 取得目前使用的語言
		 * 優先度:
		 * path > cookie > 瀏覽器語系 > 程式預設
		 */
		private function getUseLanguage(){
			$temp = LANG;
			if(array_key_exists($this->path[0], $this->languageArray)){
				$temp = $this->path[0];
				unset($this->path[0]);
				$this->path = array_values($this->path);
				if(!isset($this->path[0])){
					$this->path[0] = DEFAULT_CONTROLLER;
				}
			 	setcookie("lang", $temp, time() + 157680000, "/");
			}else if(isset($_COOKIE["lang"])){
			 	$temp = $_COOKIE["lang"];
			}else{
				if(isset($_SERVER["HTTP_ACCEPT_LANGUAGE"])) {
					$temp = explode(",", strtolower($_SERVER["HTTP_ACCEPT_LANGUAGE"]));//瀏覽器語系
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
			$file = APP_PATH."language/".$this->getLanguage().".ini";
			if(!is_file($file)){
				echo "語言檔讀取失敗";
				error_log("語言檔讀取失敗 : ".$file);
				exit;
			}
			$tmpe = @parse_ini_file($file,true);
			$this->message = @$tmpe["message"];
			$this->label = @$tmpe["label"];
		}

		/**
		 * 讀取language有哪些語言 Array ( [zh-tw] => 繁體中文 )
		 */
		private function loadLanguageFile(){
			$dir = dir(APP_PATH."language/");
			while($file = $dir->read()) {
			   	if (!is_dir($file) && strpos($file,".ini")){
			   		$temp = @parse_ini_file(APP_PATH."language/".$file,true);
			   		if($temp["value"]["STATUS"]){
				   		if(isset($temp["value"]["LANGUAGE_NAME"])){
				   			$temp = htmlspecialchars($temp["value"]["LANGUAGE_NAME"]);
				   		}else{
				   			$temp = str_replace(".ini","",$file);
				   		}
				   		$this->languageArray[str_replace(".ini","",$file)] = $temp;
				   	}
			   	}
			}
			$dir->close();
		}

		/**
		 * 設定語言
		 * @param string $value 語言
		 */
		private function setLanguage($value=LANG){
			$_SESSION[SESSION_NAME]["language"] = $value;
			$this->loadLanguageini();
		}

		/**
		 * 取得語言
		 * @return [type] [description]
		 */
		function getLanguage(){
			return $_SESSION[SESSION_NAME]["language"];
		}

		/**
		 * 顯示訊息
		 * @param  string $value 訊息代碼
		 * @param  array  $data  訊息參數
		 * @return string        訊息
		 */
		function getMessage($value,$data=[]){
			if(isset($this->message[$value])){
				$temp = $this->message[$value];
				if(is_array($data) && (count($data) > 0)){
					foreach ($data as $k => $v) {
						$temp = str_replace("{".$k."}",$v,$temp);
					}							
				}
				return $temp;
			}
			return $value;
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
		 * 加載controller
		 */
		function loadController(){
			$console = $this;
			$tempPath = APP_PATH."controller/".$this->path[0];
			foreach ($this->path as $key => $value) {
				if($key == 0) continue;
				if(!is_dir($tempPath)) break;
				$tempPath .= ("/".$value);
			}
			if(is_dir($tempPath)){
				$tempPath .= ("/".DEFAULT_CONTROLLER);
			}
			$__file = $tempPath.".php";
			if(!is_file($__file)){
				$t = explode("/", $tempPath);
				$t[count($t)-1] = DEFAULT_CONTROLLER;
				$__file = implode("/", $t).".php";
			}
			if(!is_file($__file)){
			    http_response_code(404);
			    exit;
			}

			include_once($__file);
			
			$_GET = $this->MDFunc($_GET,"htmlspecialchars");
			$_POST = $this->MDFunc($_POST,"htmlspecialchars");

			$this->design->setData("_GET", $_GET);
			$this->design->setData("_POST", $_POST);
			$this->design->setData("path", $this->path);
			$this->design->setData("data", $data);
			$this->design->setData("lang", $this->getLanguage());
			$this->design->setData("console", $console);

			$templateDir = current($this->design->tpl->getTemplateDir());
			$tempPath = $templateDir.$this->path[0];
			foreach ($this->path as $key => $value) {
				if($key == 0) continue;
				if(!is_dir($tempPath)) break;
				$tempPath .= ("/".$value);
			}
			if(is_dir($tempPath)){
				$tempPath .= ((($templateDir!=$tempPath)?"/":"").DEFAULT_CONTROLLER);
			}

			$__file = $tempPath.'.html';
			// echo $__file;exit;
			// if(!is_file($__file)){
			// 	$t = explode("/", $tempPath);
			// 	$t[count($t)-1] = DEFAULT_CONTROLLER;
			// 	$__file = implode("/", $t).".html";
			// }

			$__file = str_replace(str_replace("\\", "/", $templateDir),"",$__file);
			$__file = str_replace(str_replace("/", "\\", $templateDir),"",$__file);
			$this->design->loadDisplay($__file);
		}


		/**
		 * 多維陣列處理
		 * @param [type] $data     資料
		 * @param [type] $funcName 要執行的函式 htmlspecialchars htmlspecialchars_decode trim之類的
		 */
		function MDFunc($data,$funcName){
			if(!function_exists($funcName)){
				$msg = "error : [".$funcName."] is not define.";
				echo $msg;
				error_log($msg);
				exit;
			}
			if(is_array($data)){
				foreach ($data as $key => $value){
					$data[$key] = $this->MDFunc($value,$funcName);
				}
			}else{
				$data = $funcName($data);
			}
			return $data;
	    }

		/**
		 * 防止CSRF跨站攻擊
		 */
		function CSRFVerifty(){
			if(!in_array($this->path[0], $this->config["csrfWhitelist"]) && $_POST){
				if(isset($_SESSION[SESSION_NAME]["CSRF_TOKEN"]) && ($_POST[TOKEN_NAME] == $_SESSION[SESSION_NAME]["CSRF_TOKEN"])){
					unset($_POST[TOKEN_NAME]);
					return true;
				}
				$this->outputJson(false,$this->getMessage("CSRF_TOKEN_NOT_TRUE"));
			}
			
		}

		/**
		 * 取得token
		 * @return [type] [description]
		 */
		function getToken($type=""){
			if(!$_SESSION[SESSION_NAME]["CSRF_TOKEN"] || is_array($_SESSION[SESSION_NAME]["CSRF_TOKEN"])){
				$_SESSION[SESSION_NAME]["CSRF_TOKEN"] = hash_hmac($this->config["CSRFType"] ,rand(),$this->config["CSRFKey"]);
			}
			switch ($type) {
				case "text":
					return $_SESSION[SESSION_NAME]["CSRF_TOKEN"];
					break;
				case "name":
					return TOKEN_NAME;
					break;
				case "json":
					return $this->outputJson(true,"token",[
						"name" => TOKEN_NAME,
						"value" => $_SESSION[SESSION_NAME]["CSRF_TOKEN"]
					]);
					break;
			}
			return '<input type="hidden" name="'.TOKEN_NAME.'" value="'.$_SESSION[SESSION_NAME]["CSRF_TOKEN"].'">';

		}

		/**
		 * 輸出json
		 * @param  [type] $response [description]
		 * @param  [type] $message  [description]
		 * @param  [type] $data  	[description]
		 * @return [type]           [description]
		 */
		function outputJson($response,$message="",$data=""){
			header("Content-Type: application/json; charset=utf-8");
			ob_clean();
			echo json_encode([
				"response" => $response,
				"message" => $message,
				"data" => $data
			]);
			exit;
		}

	}
}
