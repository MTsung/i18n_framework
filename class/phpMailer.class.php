<?php


/**
 * 郵件模組
 */	
namespace MTsung{
	
	include_once(APP_PATH.'include/PHPMailer/PHPMailerAutoload.php');

	class phpMailer extends \PHPMailer{
		var $console;
		var $config = config::SMTP;

		/**
		 * PHPMailer設定
		 * @param [type] $console [description]
		 */
		function __construct($console,$config=[]){
			$this->setConsole($console);

			// $this->SMTPDebug = 4;
			
			$this->IsSMTP(); 
			$this->SMTPAuth = true;
			$this->CharSet = "utf-8"; 
			$this->SMTPOptions = array(
			    'ssl' => array(
			        'verify_peer' => false,
			        'verify_peer_name' => false,
			        'allow_self_signed' => true
			    )
			);

			if($config) $this->config = $config;

			$this->setMailSMTP($this->config);
		}

		/**
		 * SMTP設定
		 * @param array $data 
		 */
		public function setMailSMTP($data){
			$this->SMTPSecure = $data["SMTPSecure"];
			$this->Host = $data["Host"];
			$this->Port = $data["Port"]*1;
			$this->Username = $data["Username"];
			$this->Password = $data["Password"];
			$this->From = $this->config["senderEmail"];
			$this->FromName = $this->config["senderName"];
		}

		/**
		 * 設定信件標題
		 * @param String $value 信件標題
		 */
		public function setMailTitle($value){
			$this->Subject = $value; 
		}

		/**
		 * 新增檔案
		 * @param [type] $file  檔案路徑
		 * @param [type] $name  顯示的檔案名稱
		 */
		public function setMailFile($file,$name){
			if(is_file($file)){
				$this->AddAttachment($file,$name);
			}
		}

		/**
		 * 收件者郵件及(名稱)
		 * @param Array $data 收件者郵件，複數使用','區隔
		 */
		public function setMailAddress($data){
			$data = explode(',', $data);
			foreach ($data as $key => $value){
				$this->AddAddress($value);
			}
		}

		/**
		 * 設定樣板Smarty
		 * @param String $value 樣板名稱 e.g.,mail.html
		 * @param Array $data  要傳送的資料
		 */
		public function setMailBody($value,$data=array()){
			ob_start();

			$tpl = new \Smarty();
			$tpl->left_delimiter = '({';
			$tpl->right_delimiter = '})';
			$tpl->template_dir = DATA_PATH . "view/mail";
			$tpl->compile_dir = DATA_PATH . "temp/templates_c/";
			$tpl->config_dir = DATA_PATH . "temp/configs/";
			$tpl->cache_dir = DATA_PATH . "temp/cache/"; 


			$tpl->assign("console",$this->console);

			if(is_array($data)){
				foreach ($data as $k => $v) {
					$tpl->assign($k,$v);
				}
			}
			if(is_file($tpl->getTemplateDir(0).$value)){
				$tpl->display($value);
			}else{
				echo $this->console->getMessage('DISPLAY_NULL',array($value));
				exit;
			}

			$this->Body = ob_get_contents();
			ob_end_clean();
		}

		/**
		 * 寄送郵件
		 * @param  string $back 轉跳頁
		 * @return [type]       [description]
		 */
		public function sendMail(){
			$this->IsHTML(true); //郵件內容為html

			if(!$this->Send()){
				error_log("Send mail error: " . $this->ErrorInfo);
				return false;
			}
			return true;
		}

	    /**
	     * 設定console
	     * @param Mtsung/main $console 
	     */
	    public function setConsole($console){
	    	$this->console = $console;
	    }
	}
}