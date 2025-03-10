# 使用过程记录
其目標是建立一個 名為 "rsvp list" 的完整功能模組。這個模組的核心目的是讓網站使用者能夠在行銷團隊推廣的活動內容節點上選擇是否參加（RSVP），並且讓行銷團隊可以取得與會者名單及其電子郵件地址。為了實現這個功能，開發過程將會深入探討 Drupal API 的多個關鍵部分，例如建立表單、與資料庫互動、建立控制器以顯示報表、以及建立後台管理設定頁面，同時也會修改內容編輯表單，讓編輯者能選擇是否啟用 RSVP 功能。

记录youtube 模块开发教程 16-35课

## 安装过程
### 先git项目
```
git clone https://github.com/dghabc/d11.git rsvplist
//用vscode 修改.ddev中的项目名称
ddev composer install
ddev drush site:install  --site-name=rsvplist --account-name=admin --account-pass=admin -y
ddev drush  sql:cli <database.sql
ddev drush  cr
//删除.git
rm -rf .git
//配置。.gitignore
建立 资源库

```
## 构建模块

### 第 16 課：RSVP 模組介绍

其目標是建立一個 名為 "rsvp list" 的完整功能模組。這個模組的核心目的是讓網站使用者能夠在行銷團隊推廣的活動內容節點上選擇是否參加（RSVP），並且讓行銷團隊可以取得與會者名單及其電子郵件地址。為了實現這個功能，開發過程將會深入探討 Drupal API 的多個關鍵部分，例如建立表單、與資料庫互動、建立控制器以顯示報表、以及建立後台管理設定頁面，同時也會修改內容編輯表單，讓編輯者能選擇是否啟用 RSVP 功能。



### 第 17 課：建立 RSVP 列表模組

```
//生成框架
ddev drush generate module --destination=modules/custom
//还没有配置configure 鍵值
configure: rsvplist.admin_settings
```
[模块yml文件的具体含义](https://www.drupal.org/docs/develop/creating-modules/let-drupal-know-about-your-module-with-an-infoyml-file#s-complete-example])

我們開始建立名為 rsvp_list 的自訂模組，並在 modules/custom 目錄下建立 rsvp_list 資料夾。

接著，我們在 rsvp_list 資料夾內建立 rsvplist.info.yml 檔案，這個檔案包含了模組的基本資訊，例如名稱、類型、核心版本需求、描述和套件。

影片中也介紹了 dependencies 和 configure 這兩個新的鍵，以及 YAML 檔案中字串的處理方式，說明技術上字串是純量，除非開頭或包含特殊字元，否則不需要使用引號。dependencies 鍵用於宣告模組所依賴的其他模組（使用 project_name:module_name 的命名空間格式，例如 drupal:block），而 configure 鍵則用於指定模組設定表單的路徑。


最後，我們在 Drupal 管理介面的擴充功能頁面啟用了這個模組。


### 第 18 課：Form API 與在 Drupal 中建立表單

[元素链接](https://api.drupal.org/api/drupal/elements/11.x)


本課介紹了 Drupal 的 Form API，它包含三個循序執行的部分：顯示 HTML 表單、驗證使用者輸入的資料，以及處理表單的提交。

Form API 提供了一個框架來建立表單，抽象化了 HTML 表單的處理，目的是提高表單處理和呈現的一致性，並減少需要手動編寫的 HTML 程式碼。

表單是使用**多維陣列（render array）**來描述的。

Drupal 中常用的三種表單類型是：

* FormBase：最通用的基底類別，用於建立一般表單. 我們的 RSVP 詳細資訊收集表單將使用它.

* ConfigFormBase：用於建立系統設定表單. 管理員設定哪些內容類型啟用 RSVP 功能的表單將使用它.

* ConfirmFormBase：用於建立確認操作的表單.
```
drush generate form:simple
drush generate form:config
drush generate form:confirm
```

每種表單類型都有三個必要的的方法：getFormId()、buildForm() 和 submitForm()。buildForm() 方法是宣告表單 render array 的正確位置。

### 第 19 課：建立電子郵件提交表單


```
//生成表单
ddev drush generate form:simple


```
我們開始建立收集 RSVP 詳細資訊的表單。首先在 rsvp_list/src 目錄下建立 Form 資料夾，並在其中建立 RSVPForm.php 檔案。

RSVPForm 類別繼承自 FormBase 並實作了 FormInterface。

我們實作了 getFormId() 方法，將表單 ID 設定為 rsvp_list_email_form。

在 buildForm() 方法中，我們建立了一個包含電子郵件文字欄位、提交按鈕和一個隱藏欄位（用於儲存節點 ID nid）的表單 render array。我們也學習到如何使用 \Drupal::routeMatch()->getParameter('node') 來取得當前頁面的節點物件，並从中獲取節點 ID。

submitForm() 方法目前只會顯示一個訊息，表明表單正在運作，並顯示提交的電子郵件。

### 第 20 課：建立具有表單路徑的路由

我們在 rsvp_list 模組目錄下建立 rsvplist.routing.yaml 檔案來定義路由。

我們建立了一個暫時的路徑 /rsvp-list-demo，將 _form 控制器指向 \Drupal\rsvp_list\Form\RSVPForm 類別，並設定了標題。

透過這個路徑，我們可以在瀏覽器中看到並測試我們建立的表單。

### 第 21 課：驗證表單提交：新增驗證處理器

我們在 RSVPForm.php 中新增了 validateForm() 方法，用於驗證使用者輸入的電子郵件地址是否有效。

我們使用 \Drupal::service('email.validator')->isValid($value) 來檢查電子郵件格式。

如果電子郵件無效，我們使用 $form_state->setErrorByName('email', $this->t('It appears that %mail is not a valid email.', ['%mail' => $value])) 來設定錯誤訊息。

### 第 22 課：資料庫 API 簡介
[数据库抽象层](https://api.drupal.org/api/drupal/core%21lib%21Drupal%21Core%21Database%21database.api.php/group/database/11.x)

本課概述了 Drupal 的 Database API，它提供了一個與底層資料庫無關的抽象層，允許開發人員編寫一次查詢，即可在多種資料庫上運作。

我們學習到如何取得資料庫連線物件 (\Drupal::database())。

介紹了兩種查詢風格：靜態查詢（用於簡單的 SELECT 查詢）和動態查詢（用於更複雜的查詢，以及 INSERT、UPDATE、DELETE 和 MERGE 操作）。動態查詢是透過逐步建立查詢物件來完成的。

我們也學習到如何執行查詢 (execute()) 和獲取結果 (fetchAll(), fetchAssoc(), fetchCol())。

強調了使用 try/catch 區塊來處理資料庫操作可能發生的異常。

### 第 23 課：安裝檔案與使用資料庫結構描述

本課介紹了 Schema API，它允許模組透過結構化的陣列來宣告其資料庫表。

我們在 rsvp_list 模組的根目錄下建立 rsvplist.install 檔案。

在 hook_schema() 函數中，我們定義了兩個資料庫表：rsvp_list（用於記錄 RSVP 詳細資訊，包含 id、uid、nid、mail 和 created 欄位，以及主鍵和索引）和 rsvp_list_enabled（用於追蹤哪些節點啟用了 RSVP 功能，只有 nid 欄位作為主鍵）。

定義在 hook_schema() 中的資料庫表會在模組安裝時自動建立，並在解除安裝時移除。

### 第 24 課：動態插入查詢 - 以程式設計方式插入資料庫

我們回到 RSVPForm.php，並修改 submitForm() 方法，以將使用者輸入的電子郵件地址和相關的節點 ID 插入到 rsvp_list 資料庫表中。

我們使用動態 INSERT 查詢來完成此操作，並使用 \Drupal::currentUser()->id() 取得目前使用者的 UID，使用 $form_state->getValue('email') 取得電子郵件，並使用 \Drupal::time()->getRequestTime() 取得目前的時間戳記。

程式碼被包裹在 try/catch 區塊中以處理潛在的資料庫異常。

成功插入資料後，會向使用者顯示一個成功訊息。

### 第 25 課：定義自訂權限 - 彈性存取控制

我們在 rsvp_list 模組的根目錄下建立 rsvplist.permissions.yaml 檔案，來定義三個自訂權限：view rsvp list、access rsvp list report 和 administer rsvp list。

這些權限可以讓網站管理員更精細地控制誰可以 RSVP、存取報表和管理設定。

我們將 /rsvp-list-demo 路由的權限從 access content 更改為 view rsvp list。

最後，我們在 Drupal 管理介面的權限頁面，將這些新權限授予不同的使用者角色。

### 第 26 課：區塊外掛程式與建立 RSVP 列表區塊

我們開始將之前建立的表單整合到一個區塊中。首先，在 rsvp_list/src/Plugin/Block 目錄下建立 RSVPBlock.php 檔案。

我們建立了一個名為 RSVPBlock 的類別，它繼承自 BlockBase，並使用 @Block 註解來宣告它是一個區塊外掛程式，設定了它的 ID (rsvp_block) 和管理標籤 (RSVP block)。

build() 方法目前只回傳一些基本的標記文字。

我們在 Drupal 管理介面的區塊版面配置中，將這個新的 RSVP 區塊放置到側邊欄。

### 第 27 課：在區塊中顯示 RSVPForm 並應用存取控制

我們修改了 RSVPBlock.php 的 build() 方法，使用 \Drupal::formBuilder()->getForm('\Drupal\rsvp_list\Form\RSVPForm') 來回傳 RSVP 表單的 render array，使其顯示在區塊中。

我們也實作了 blockAccess() 方法，使用 AccessResult::allowedIfHasPermission('view rsvp list') 來基於 view rsvp list 權限控制區塊的顯示。這個區塊目前只會在節點頁面上顯示。

### 第 28 課：設定 API 簡介

本課介紹了 Drupal 的 Configuration API，它提供了一個統一的方法來儲存和管理網站的設定資料，這些資料在不同的環境之間保持一致。

設定被劃分為個別的設定物件，每個物件都有一個唯一的名稱，並以 YAML 格式儲存在檔案中。

預設的設定檔案位於模組的 config/install 目錄下。

影片中也說明了設定與狀態變數的區別。

### 第 29 課：建立 RSVPSettingsForm 和設定

我們開始建立管理介面，讓網站管理員可以設定哪些內容類型可以啟用 RSVP 功能。我們在 src/Form 目錄下建立 RSVPSettingsForm.php 檔案，並繼承自 ConfigFormBase。

getFormId() 回傳表單 ID，getEditableConfigNames() 回傳包含設定檔案名稱 (rsvplist.settings) 的陣列。

buildForm() 方法建立了一個包含核取方塊的表單，列出所有可用的內容類型。它從 rsvplist.settings 設定物件中取得預設的允許類型。ConfigFormBase 會自動加入「儲存設定」按鈕。

submitForm() 方法儲存使用者在表單中選擇的內容類型到 rsvplist.settings 設定物件中。

我們在 config/install 目錄下建立 rsvplist.settings.yaml 檔案，定義了預設的允許內容類型（文章）。

我們也在 config/schema 目錄下建立 rsvplist.schema.yaml 檔案，定義了 rsvplist.settings 設定的結構。

為了讓新的設定生效，我們需要解除安裝並重新安裝 rsvp_list 模組。

### 第 30 課：為 RSVPSettings 新增路由和選單連結

我們在 rsvplist.routing.yaml 檔案中為設定表單新增了一個路由 /admin/config/content/rsvp-list，並將 _form 指向 \Drupal\rsvp_list\Form\RSVPSettingsForm，權限設定為 administer rsvp list。

我們也在 rsvplist.links.menu.yaml 檔案中新增了一個選單連結，將其放置在管理選單的「設定」下的「內容製作」區塊中，指向我們剛建立的路由。影片中也示範了如何尋找系統路由名稱。

### 第 31 課：建立 RSVP 列表報表頁面

我們建立了一個控制器來顯示所有 RSVP 登錄的表格。在 src/Controller 目錄下建立 ReportController.php 檔案，並繼承自 ControllerBase。

load() 方法使用資料庫 API 查詢 rsvp_list 表格，並聯結使用者和節點表格以取得使用者名稱和節點標題。查詢結果以關聯陣列的形式回傳。此方法包含 try/catch 區塊以處理資料庫異常。

report() 方法建立一個 render array，用於顯示包含標題、使用者名稱、活動和電子郵件的 HTML 表格。它呼叫 load() 方法取得資料。為了確保顯示最新的資料，我們將這個 render array 的 cache max age 設定為 0。

### 第 32 課：RSVP 報表頁面的路由和選單連結

我們在 rsvplist.routing.yaml 檔案中為報表頁面新增了一個路由 /admin/reports/rsvp-submissions，並將 _controller 指向 \Drupal\rsvp_list\Controller\ReportController::report，權限設定為 access rsvp list report。

我們也在 rsvplist.links.menu.yaml 檔案中新增了一個選單連結，將其放置在管理選單的「報表」區塊中，指向我們剛建立的路由。

### 第 33 課：建立 RSVP Enabler 服務和依賴注入

我們建立了一個名為 RSVP Enabler 的自訂服務，用於判斷一個節點是否已啟用 RSVP 功能。首先在 rsvplist.services.yaml 檔案中宣告了這個服務 rsvplist.enabler，並定義了它的類別 \Drupal\rsvp_list\EnablerService，同時依賴注入了 database 服務。

接著，我們在 src 目錄下建立 EnablerService.php 檔案，並定義了 EnablerService 類別。在建構子中，我們接收並儲存了注入的資料庫連線物件。

我們實作了 is_enabled() 方法來檢查給定的節點 ID 是否存在於 rsvp_list_enabled 表格中。

我們也實作了 set_enabled() 方法，用於將給定的節點 ID 插入到 rsvp_list_enabled 表格中。

以及 delete_enabled() 方法，用於從 rsvp_list_enabled 表格中刪除給定的節點 ID。

### 第 34 課：變更啟用 RSVP 功能的內容類型的節點編輯表單

我們在 rsvp_list.module 檔案中實作了 hook_form_node_form_alter() 這個 hook，用於修改節點編輯表單。

針對在 RSVP 設定中啟用的內容類型，我們在節點編輯表單中新增了一個名為「RSVP 收集」的垂直分頁，其中包含一個核取方塊，讓內容編輯者可以個別啟用或停用該節點的 RSVP 收集功能。我們使用設定 API 載入 RSVP 設定，並使用我們建立的 RSVP Enabler 服務來判斷預設的核取方塊狀態。

我們也加入了一個表單提交處理器 rsvp_list_form_node_form_submit()，當節點儲存時，它會根據核取方塊的狀態，使用 RSVP Enabler 服務來啟用或停用該節點的 RSVP 功能，更新 rsvp_list_enabled 表格。

為了讓 hook 生效，我們需要再次解除安裝並重新安裝 rsvp_list 模組。

### 第 35 課：有條件地顯示 RSVP 表單以完成 RSVP 列表模組

我們回到 RSVPBlock.php，修改了 blockAccess() 方法中的邏輯，使用 RSVP Enabler 服務的 is_enabled() 方法來判斷目前的節點是否已啟用 RSVP 收集功能。只有當目前頁面是節點，且該節點已啟用 RSVP 收集，並且使用者具有 view rsvp list 權限時，才會顯示 RSVP 表單區塊。

我們也修正了 rsvp_list.module 檔案中核取方塊標籤的一個錯字。

最後，我們測試了整個 RSVP 列表模組的功能，確認它能夠根據內容編輯者的設定，有條件地在節點頁面上顯示 RSVP 表單區塊。
總之，這個系列的影片透過逐步實作，教導我們如何使用 Drupal 的各種 API（例如 Form API、Database API、Configuration API 和 Block API）、hook 系統以及自訂服務，來建立一個功能完整的自訂模組。我們學會了如何定義資料庫結構、建立表單和管理介面、處理使用者輸入、進行資料庫操作、定義自訂權限、建立區塊外掛程式以及實作條件式的顯示邏輯。
