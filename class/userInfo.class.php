<?php

/**
 * 使用者裝置資訊function 
 */
namespace MTsung{

	class userInfo{

		/**
		 * 取得作業系統
		 * @return [type] [description]
		 */
		static function getSystem(){
		    $sys = $_SERVER['HTTP_USER_AGENT'];
		    $array = [
		    	["NT 10.0","Windows 10"],
				["Windows 8.1","NT 6.3"],
				["Windows 8","NT 6.2"],
				["Windows 7","NT 6.1"],
				["Windows Vista","NT 6.0"],
				["Windows XP","NT 5.1"],
				["Windows Server 2003","NT 5.2"],
				["Windows 2000","NT 5"],
				["Windows ME","NT 4.9"],
				["Windows NT 4.0","NT 4"],
				["Unix","Unix"],
				["FreeBSD","FreeBSD"],
				["SunOS","SunOS"],
				["BeOS","BeOS"],
				["OS/2","OS/2"],
				["Macintosh","PC"],
				["AIX","AIX"],
				["Android","Android"],
				["iOS","iPhone"],
				["Windows 98","98"],
				["Windows 95","95"],
				["Mac","Mac"],
		    ];
		    foreach ($array as $value) {
		    	if(stripos($sys, $value[0]) !== false){
			        return $value[1];
			    }
		    }		    
		    return "Other";
		}

		/**
		 * 取得裝置
		 * @return [type]     Tablet=平板，Mobile=手機，Desktop=電腦
		 */
		static function getDevice(){
		    $ua = $_SERVER['HTTP_USER_AGENT'];
		    $iphone = strstr(strtolower($ua), 'mobile'); //Search for 'mobile' in user-agent (iPhone have that)
		    $android = strstr(strtolower($ua), 'android'); //Search for 'android' in user-agent
		    $windowsPhone = strstr(strtolower($ua), 'phone'); //Search for 'phone' in user-agent (Windows Phone uses that)
		 
		    $androidTablet = false;
		    if(strstr(strtolower($ua), 'android') ){//Search for android in user-agent
	            if(!strstr(strtolower($ua), 'mobile')){ //If there is no ''mobile' in user-agent (Android have that on their phones, but not tablets)
	                $androidTablet = ture;
	            }
	        }
		    $ipad = strstr(strtolower($ua), 'ipad'); //Search for iPad in user-agent
		 
		    if($androidTablet || $ipad){ //If it's a tablet (iPad / Android)
		        return 'Tablet';
		    }
		    else if($iphone && !$ipad || $android && !$androidTablet || $windowsPhone){ //If it's a phone and NOT a tablet
		        return 'Mobile';
		    }
		    else{ //If it's not a mobile device
		        return 'Desktop';
		    }
		}


		/**
		 * 取得使用者IP
		 * @return [type] [description]
		 */
		static function getIP(){
			$array = [
				"HTTP_CLIENT_IP",
				"HTTP_X_FORWARDED_FOR",
				"HTTP_X_FORWARDED",
				"HTTP_X_CLUSTER_CLIENT_IP",
				"HTTP_FORWARDED_FOR",
				"HTTP_FORWARDED",
				"REMOTE_ADDR",
			];
			foreach ($array as $value) {
				if (filter_var($_SERVER[$value], FILTER_VALIDATE_IP)){
					return $_SERVER[$value];
				}
			}
			return false;
		}

		/**
		 * 取得來源網域
		 * @return [type] [description]
		 */
		static function getReferer(){
		    if(!$temp = explode("/",str_replace(array("https://","http://"),"",$_SERVER["HTTP_REFERER"]))[0]){
		    	return "";
		    }

		    $array = [
		    	[$_SERVER["HTTP_HOST"],""],
				["google","google"],
				["yahoo","yahoo"],
				["bing","bing"],
				["facebook","facebook"],
				["instagram","instagram"],
				["pchome","pchome"],
				["wikipedia","wikipedia"],
				["twitter","twitter"],
		    ];
		    foreach ($array as $value) {
		    	if(stripos($temp, $value[0]) !== false){
			        return $value[1];
			    }
		    }
		    return "other";
		}
	}
}
