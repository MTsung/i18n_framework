# 簡易路由框架

### 伺服器環境

* 建議 PHP >= 7.0
* OpenSSL PHP Extension
* Apache
* mysql/mariadb
* 確認 Apache 伺服器已啟用 mod_rewrite 模組，否則 .htaccess 設定值將無法使用。

---

### 目錄權限

	public、view資料夾必須給伺服器有寫入權限，否則程式將無法運作。

---

### 使用套件

* [adodb 5](https://adodb.org) 資料庫套件
* [PHPMailer](https://github.com/PHPMailer/PHPMailer) SMTP發信
* [smarty](https://www.smarty.net/) 模板引擎

---

### 規則/用法

#### 網址

* 控制器(controller)內對應網址參數(不含語系)，例如
1. http://localhost/about/1/3 => controller/about.php
2. http://localhost/ => controller/index.php
3. http://localhost/admin/login => controller/admin/login.php ; 無admin資料夾 => controller/admin.php
4. http://localhost/zh-tw/admin/login => controller/admin/login.php
樣板也自動連接至對應view/web內的html。

如需取得網址參數，使用
````php
$console->path[0] //about
$console->path[1] //1
$console->path[2] //3
````

#### 安全性

* CSRF防禦，在&lt;form&gt;&lt;/form&gt;內放置token。
````
({$console->getToken()})
````

#### 多語系

* 使用 $console->getLabel()、$console->getMessage()取得label

#### 使用gmail SMTP發信設定
1. gmail帳密到 `後台相關 > 系統管理 > 系統設定 > SMTP郵件設定` 設定  
2. 將「安全性較低的應用程式存取權限」設為「啟用」  
[https://myaccount.google.com/lesssecureapps](https://myaccount.google.com/lesssecureapps)
3. 解除人機驗證鎖定  
[https://accounts.google.com/b/0/DisplayUnlockCaptcha](https://accounts.google.com/b/0/DisplayUnlockCaptcha)

---

### 常用函式介紹

#### $console

##### getLanguage()
取得目前的語言
````php
echo $console->getLanguage(); // zh-tw
````

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
| url | 轉跳網址 -1:上一頁 |

顯示alert。若為ajax，則回傳json格式

---

##### getToken()
取得CSRF token input

---

##### HTTPStatusCode(int $num,string $url)
HTTP狀態碼+跳到指定頁面

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
├── backup.class.php
├── design.class.php
└── phpMailer.class.php
````

| 檔案 | 簡介 |
| ------ | ------ |
| backup.class.php | 網站備份 |
| design.class.php | 樣板 |
| phpMailer.class.php | SMTP發信 |

#### include目錄

````
├── header.php
└── main.php
````

| 檔案 | 簡介 |
| ------ | ------ |
| header.php | 一些設定 |
| main.php | 路由核心 |
