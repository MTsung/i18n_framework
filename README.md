# 多語系簡易路由框架

### 伺服器環境

* 建議 PHP >= 7.0
* Apache
* Apache mod_rewrite 模組

---

### 使用套件

* [smarty](https://www.smarty.net/) 模板引擎

---

### 規則/用法

#### 網址

* 控制器(controller)內對應網址參數(不含語系)，例如
1. http://localhost/about/1/3 => controller/about.php
2. http://localhost/ => controller/index.php
3. http://localhost/admin/login => controller/admin/login.php ; 無admin資料夾 => controller/admin.php
4. http://localhost/zh-tw/admin/login => controller/admin/login.php  
* 樣板也自動連接至對應templates/內的html。

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
ERROR_PRODUCT_STOCK = "失敗，{0} 庫存不足 {1}"
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

##### getToken(string $type='')
取得CSRF token
不填參數回傳整個token input
name 回傳token 欄位name
text 回傳token 值
json 回傳token json格式

---

### 目錄結構

#### 根結構

````
├── class/
├── config/
├── controller/
├── include/
├── public/
├── temp/
├── view/
├── .htaccess
├── index.php
└── robots.txt
````

| 目錄 | 簡介 |
| ------ | ------ |
| class | 類別放置區域 |
| config | 設定檔放置位置 |
| controller | 控制器 |
| public | 語言檔、上傳、輸出等公開檔案放置位置 |
| temp | 暫存資料夾 |
| include | 使用的套件、路由核心放置位置 |
| view | 樣板 |

| 檔案 | 簡介 |
| ------ | ------ |
| .htaccess | apache 設定檔 |
| index.php | 程式的進入點 |
| robots.txt | robots.txt |
