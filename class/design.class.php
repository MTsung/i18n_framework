<?php


/**
 * 樣板輸出
 */
namespace MTsung{
    
    include_once(APP_PATH.'include/smarty/libs/Smarty.class.php');// 文件 https://www.smarty.net/docsv2/en/

    class design{
    	var $console;
    	var $tpl;

    	/**
    	 * 基本Smarty設定
    	 */
        function __construct(){
        	$this->tpl = new \Smarty();
    		$this->tpl->left_delimiter = '({';
    		$this->tpl->right_delimiter = '})';
            $this->tpl->template_dir = APP_PATH."templates";
    		$this->tpl->compile_dir = APP_PATH."temp/templates_c";
    		$this->tpl->config_dir = APP_PATH."temp/configs/";
    		$this->tpl->cache_dir = APP_PATH."temp/cache/";
        }

        /**
         * 載入樣板
         * @param  String $value 樣板檔名 e.g.,index.html
         */
        public function loadDisplay($value){
            // echo $this->tpl->template_dir[0].$value;exit;
        	if(!is_file($this->tpl->template_dir[0].$value)){
                ob_clean();
                echo "design is null.";
                error_log("design is null.".$value);
                exit;
            }

            $this->tpl->loadFilter('output','trimwhitespace');
            $this->tpl->display($value);
        }

        /**
         * assign
         * @param [type] $name 變數名稱
         * @param [type] $data 資料
         */
        public function setData($name,$data){
    		$this->tpl->assign($name, $data);
        }
    }
}