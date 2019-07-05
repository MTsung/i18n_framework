# 網動專案框架

### 注意事項
如非網動廣告科技公司(統編28484688) 人員，本框架模組僅供參考用途，如引用任何代碼進行商業銷售行為，網動廣告科技公司將保留相關法律追訴權力。

---

### 伺服器環境

* 建議 PHP >= 7.0
* OpenSSL PHP Extension
* Apache
* mysql/mariadb
* 確認 Apache 伺服器已啟用 mod_rewrite 模組，否則 .htaccess 設定值將無法使用。

---

### 目錄權限

data、view、sessionTemp資料夾必須給伺服器有寫入權限，否則程式將無法運作。

---

### 使用套件

* [Ace editor](https://ace.c9.io/) 樣板/檔案等編輯使用
* [bootstrap 4.0](https://getbootstrap.com/docs/4.0/) 後台使用
* [adodb 5](https://adodb.org) 資料庫套件
* [tinymce 4](https://www.tiny.cloud/) 後台html編輯器
* [ckfinder 3](https://ckeditor.com/ckfinder/) html編輯器上傳檔案用
* PclZip 壓縮/解壓縮用
* [綠界SDK](https://github.com/ECPay/ECPayAIO_PHP) 綠界金流物流
* [PHPMailer](https://github.com/PHPMailer/PHPMailer) SMTP發信
* [smarty](https://www.smarty.net/) 模板引擎

---

### 規則/用法

#### 網址

* 控制器(controller)內的檔案名稱對應網址後第一個參數，例如 http://localhost/about/1/3 會自動導向至 about.php(無檔案時預設會導向至index.php)，樣板也自動連接至對應的html。如需取得網址參數，使用
````php
$console->path[0] //about
$console->path[1] //1
$console->path[2] //3
````
* query盡量只有在搜尋、分頁時用

#### 功能擴充
* 無特別操作基本上皆可用後台開功能前台輸出就好
* 預設功能無法做到時再加寫功能，basic、basicOne、class內設定，模組設定範例：
```php
switch ($console->path[1]) {
	case '範例':
		$module["tinemceEditor"][0]["name"] = 'detail';

		$module["uploadImg"][0]["name"] = "picture";//欄位名稱
		$module["uploadImg"][0]["max"] = 10;//限制數量
		$module["uploadImg"][0]["watermark"] = '';//浮水印
		$module["uploadImg"][0]["textOther"] = array("Title","Alt","Href");//欄位名稱
		$module["uploadImg"][0]["textOtherText"] = array($console->getLabel("TITLE"),$console->getLabel("ALT"),$console->getLabel("URL"));//提示字
		$module["uploadImg"][0]["textareaOther"] = array("Detail");//欄位名稱
		$module["uploadImg"][0]["textareaOtherText"] = array($console->getLabel("DETAIL"));//提示字
		$module["uploadImg"][0]["suggestText"] = "1920x576";//建議尺寸


		$module["uploadFile"][0]["name"] = "file";//欄位名稱
		$module["uploadFile"][0]["max"] = 1.5;//限制數量
		$module["uploadFile"][0]["suggestText"] = "限制";//建議尺寸
		$module["uploadFile"][0]["extension"] = array("jpg");//限制附檔名
}
```

#### 安全性
* XSS防禦，非html編輯器資料請htmlspecialchars
* 注碼攻擊防禦，請使用AutoExecute或Prepare防範。
````php
$this->conn->AutoExecute($this->table,$data,"INSERT"); //效能較差

$sqlArray = ["1"];
$this->conn->GetArray($this->conn->Prepare("select * from ".$this->table." where id=?"),$sqlArray);
````
* CSRF防禦，在&lt;form&gt;&lt;/form&gt;內放置token。
````
({$console->getToken()})
````
* 重要操作使用POST

#### 多語系

* 使用 $console->getLabel()、$console->getMessage()取得label

#### 新增金物流步驟
1. systemMenuFront.html 新增開關欄位
2. systemSetting.html 新增api key欄位
3. payment_setting.html 金流新增開關欄位
4. shipment_setting.html 物流新增開關欄位
5. shoppingCart.class.php func修改  
getShipmentMethodArray()  
getShipmentTitle()  
getShipmentText()  
getPaymentTitle()  
getPaymentText()  
getPaymentMethodArray()  
payment()  
shipment()  


#### 其他功能使用
* QRcode.php。query設定，d為data
* barcode.php。query設定，barcode為條碼號碼
* mathcode.php。query設定，bgcolor為背景顏色、noSql設定1為只使用cookie做計算
* verifycode.php。無須query設定，直接顯示驗證碼

#### 使用gmail SMTP發信設定
1. gmail帳密到 `後台相關 > 系統管理 > 系統設定 > SMTP郵件設定` 設定  
2. 將「安全性較低的應用程式存取權限」設為「啟用」  
[https://myaccount.google.com/lesssecureapps](https://myaccount.google.com/lesssecureapps)
3. 解除人機驗證鎖定  
[https://accounts.google.com/b/0/DisplayUnlockCaptcha](https://accounts.google.com/b/0/DisplayUnlockCaptcha)

---

### 常用函式介紹

#### $console

##### getLanguageArray()
輸出語言列表

---

##### getLanguage()
取得目前的語言
````php
echo $console->getLanguage(); // zh-tw
````

---

##### getHreflang()
多國語標籤 hreflang 給Search Console看的    
[https://support.google.com/webmasters/answer/189077?hl=zh-Hant](https://support.google.com/webmasters/answer/189077?hl=zh-Hant)

---

##### getMessage(string $value,array $data)

| 參數名稱 | 說明 |
| ------ | ------ |
| value | 訊息代碼 |
| data | 訊息參數 |

*Example:*

zh-tw.ini

````
ERROR_PRODUCT_STOCK = "失敗，{1} 庫存不足 {2}"
````

index.php

````php
echo $console->getMessage("ERROR_PRODUCT_STOCK",["商品",1]); // 失敗，商品 庫存不足 1
````

---

##### getLabel(string $vlaue)
取得label，找不到會直接輸出value

zh-tw.ini

````
INDEX = "首頁"
````

index.php
````php
echo $console->getLabel("INDEX"); // 首頁
echo $console->getLabel("首頁"); // 首頁 (zt-tw.ini內無設定此key，所以直接輸出)
````

---

##### alert(string $message,string $url)

| 參數名稱 | 說明 |
| ------ | ------ |
| message | 訊息 |
| url | 轉跳網址 -1:上一頁  NULL,"":reload  NO:不轉跳 CLOSE:關閉|

顯示alert。若為ajax，則回傳json格式


---

##### getToken(string $return)
取得CSRF token  
設定text的話只回傳token碼，不設定回傳input


---

##### getTokenName()
取得token使用的鍵值


---

##### getQRCodeInGoogle(string $data,int $widthHeight ='150',char $EC_level='L',int $margin='2',string $choe='UTF-8')
使用google的API產生QRCode，回傳img tag

| 參數名稱 | 說明 |
| ------ | ------ |
| data | 訊息 |
| widthHeight | 預設 150<br>寬高 |
| EC_level | L - Allows recovery of up to 7% data loss (預設)<br>M - Allows recovery of up to 15% data loss<br>Q - Allows recovery of up to 25% data loss<br>H - Allows recovery of up to 30% data loss|
| margin | 預設 2 |
| choe | 編碼 |

*Example:*

````php
echo $console->getQRCodeInGoogle("zxc"); 
// <a href="http://chart.apis.google.com/chart?cht=qr&chs=150x150&chld=L|2&chl=zxc&choe=UTF-8"  target="_blank"><img src="http://chart.apis.google.com/chart?cht=qr&chs=150x150&chld=L|2&chl=zxc&choe=UTF-8" alt="QR code" width="150" height="150" /></a>
````

---

##### checkreCAPTCHA()
##### checkVerifyCode()
驗證碼驗證


---

##### getDatabaseSize(string $database)
取得指定資料庫大小


---

##### getSqlSize(string $table)
計算資料庫/資料表大小


---

##### getDirSize(string $path)
計算資料夾/檔案大小


---

##### formatSize(float $size,int $depth=0)
自動單位轉換，最多轉換到TB


---

##### linkTo(string $url)
使用javascript轉跳


---

##### HTTPStatusCode(int $num,string $url)
HTTP狀態碼+跳到指定頁面


---

##### addQuery()
新增Query參數至url


---

##### to404()
404


---

##### urlKey(array $data)
urlKey轉換  
若urlKey為空值用id代替


---

##### isTables(string $tabel)
是否有資料表


---

##### youtubeLink($url)
輸入yt連結/編號取得iframe可用的url


---

##### youtubeImg($url)
輸入yt連結/編號取得縮圖


---

##### isAjax
是否為ajax

---

### 目錄結構

#### 根結構

````
├── class/
├── config/
├── controller/
├── css/
├── data/
├── example/
├── fonts/
├── images/
├── include/
├── js/
├── module/
├── view/
├── .htaccess
├── 404.html
├── QRcode.php
├── ajax.php
├── barcode.php
├── favicon.ico
├── firebase-messaging-sw.js
├── index.php
├── mathcode.php
├── robots.txt
├── upload.php
└── verifycode.php
````

| 目錄 | 簡介 |
| ------ | ------ |
| class | 類別放置區域 |
| config | 設定檔放置位置 |
| controller | 控制器 |
| css | 預設css檔案位置 |
| data | 切版檔案放置位置，預設為data/10000 |
| example | 一些套板smarty範例 |
| fonts | 預設字型位置 |
| images | 預設圖片位置 |
| include | 使用的套件放置位置 |
| js | 預設javascript檔案位置 |
| module | 預設樣板 |
| view | 樣板 |

| 檔案 | 簡介 |
| ------ | ------ |
| .htaccess | apache 設定檔 |
| 404.html | 預設404頁面樣板 |
| QRcode.php | QRcode產生 |
| ajax.php | 一些ajax |
| barcode.php | 條碼產生 |
| favicon.ico | 網站圖示 |
| firebase-messaging-sw.js | fcm用 |
| index.php | 程式的進入點 |
| mathcode.php | 人次計數器顯示 |
| robots.txt | robots.txt |
| upload.php | 上傳檔案程式 |
| verifycode.php | 驗證碼產生 |

---

#### class目錄

````
├── ECPay.class.php
├── ECPayLog.class.php
├── analytics.class.php
├── backup.class.php
├── cPanel.class.php
├── center.class.php
├── csv.class.php
├── dataClass.class.php
├── dataList.class.php
├── design.class.php
├── fcm.class.php
├── fileTemplate.class.php
├── form.class.php
├── imgCompress.class.php
├── member.class.php
├── memberGroup.class.php
├── menu.class.php
├── pageNumber.class.php
├── pay.class.php
├── payFiscPay.class.php
├── payLog.class.php
├── phpMailer.class.php
├── product.class.php
├── setting.class.php
├── shoppingCart.class.php
├── systemLog.class.php
├── tree.class.php
├── typeConst.const.php
├── uploadFile.class.php
├── userDeviceInfomation.trait.php
├── validation.class.php
├── watermark.class.php
└── webSetting.class.php
````

| 檔案 | 簡介 |
| ------ | ------ |
| ECPay.class.php | 綠界金流物流 |
| ECPayLog.class.php | 綠界回傳log |
| analytics.class.php | 流量分析 |
| backup.class.php | 網站備份 |
| cPanel.class.php | cPanel API串接 |
| center.class.php | CRUD核心 |
| csv.class.php | 輸出CSV |
| dataClass.class.php | 一般分類 |
| dataList.class.php | 一般資料 |
| design.class.php | 樣板 |
| fcm.class.php | fcm推播 |
| fileTemplate.class.php | 樣板檔案 |
| form.class.php | 表單 |
| imgCompress.class.php | 圖片壓縮 |
| member.class.php | 會員 |
| memberGroup.class.php | 會員群組 |
| menu.class.php | 後台選單 |
| pageNumber.class.php | 頁碼產生 |
| pay.class.php | 金流核心 |
| payFiscPay.class.php | 第一銀行金流串接 |
| payLog.class.php | 金流log |
| phpMailer.class.php | SMTP發信 |
| product.class.php | 商品 |
| setting.class.php | 系統設定類 |
| shoppingCart.class.php | 購物車 |
| systemLog.class.php | 系統操作紀錄 |
| tree.class.php | 分類樹核心 |
| typeConst.const.php | 一些const |
| uploadFile.class.php | 上傳檔案 |
| userDeviceInfomation.trait.php | 使用者資訊trait |
| validation.class.php | 驗證 |
| watermark.class.php | 浮水印 |
| webSetting.class.php | 網站設定類 |

---

#### config目錄

````
├── dataBase.php
├── define.php
└── setup.sql
````

| 檔案 | 簡介 |
| ------ | ------ |
| dataBase.php | 資料庫設定，子網功能 |
| define.php | 一些設定 |
| setup.sql | 安裝資料庫檔案 |

---

#### controller目錄

````
├── serback
	├── __about.php
	├── __menu.php
	├── admin.php
	├── adminGroup.php
	├── analytics.php
	├── basic.php
	├── basicOne.php
	├── class.php
	├── errorLog.php
	├── file.php
	├── forget.php
	├── form.php
	├── header.php
	├── index.php
	├── language.php
	├── languageCopy.php
	├── login.php
	├── member.php
	├── memberField.php
	├── memberGroup.php
	├── memberLog.php
	├── order.php
	├── orderField.php
	├── phpinfo.php
	├── profile.php
	├── setting.php
	├── subsidiary.php
	├── systemLog.php
	├── systemMenu.php
	├── systemMenuFront.php
	├── systemSetting.php
	└── template.php
├── 404.php
├── __backup__.php
├── __otherData__.php
├── __session.php
├── ECPayResponse.php
├── fcm.php
├── header.php
├── index.php
├── member.php
├── payResponse.php
├── serback.php
├── shopping.php
└── sitemap.xml.php
````

##### controller/serback

| 檔案 | 簡介 |
| ------ | ------ |
| __about.php | 使用開出來的功能 |
| __menu.php | 開功能 |
| admin.php | 後台管理員管理 |
| adminGroup.php | 後台管理員群組管理 |
| analytics.php | 前台分析資料 |
| basic.php | 一般資料(多筆) |
| basicOne.php | 一般資料(單筆) |
| class.php | 一般分類 |
| errorLog.php | 程式error_log |
| file.php | 網站檔案管理 |
| forget.php | 後台忘記密碼 |
| form.php | 表單資料輸出 |
| header.php | 上板 |
| index.php | 公司最新消息/網站空間使用量 |
| language.php | 語系管理 |
| languageCopy.php | 語系複製 |
| login.php | 登入頁面 |
| member.php | 前台會員管理 |
| memberField.php | 前台會員自訂欄位管理 |
| memberGroup.php | 前台會員群組管理 |
| memberLog.php | 前台會員紀錄 |
| order.php | 訂單管理 |
| orderField.php | 訂單自訂欄位管理 |
| phpinfo.php | phpinfo |
| profile.php | 個人中心 |
| setting.php | 系統設定 |
| subsidiary.php | 子網 |
| systemLog.php | 操作紀錄 |
| systemMenu.php | 後台目錄管理 |
| systemMenuFront.php | 前台目錄管理(開功能) |
| systemSetting.php | 網站設定 |
| template.php | 樣板管理 |

##### controller

| 檔案 | 簡介 |
| ------ | ------ |
| 404.php | 404頁面 |
| \_\_backup\_\_.php | 備份 |
| \_\_otherData\_\_.php | 其他使用資料讀取 |
| __session.php | session顯示(json) |
| ECPayResponse.php | 綠界回傳路徑 |
| fcm.php | fcm |
| header.php | 上板 |
| index.php | 一般資料輸出 |
| member.php | 會員 |
| payResponse.php | 一般金流回傳 |
| serback.php | 後台轉換 |
| shopping.php | 購物車 |
| sitemap.xml.php | 自動生成sitemap |

---

#### include目錄

````
├── foor.php
├── header.php
└── main.php
````

| 檔案 | 簡介 |
| ------ | ------ |
| foor.php | 下版，資料整理後載入樣板 |
| header.php | 一些設定 |
| main.php | 核心class |
