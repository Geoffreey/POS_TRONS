<?php
if (session_status() == PHP_SESSION_NONE) {
    $cookie_path = '/POS_TRONS'; // O ajusta según tu carpeta real
    session_set_cookie_params([
        'lifetime' => 0,
        'path' => $cookie_path,
        'domain' => $_SERVER['HTTP_HOST'],
        'secure' => isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] == 'on',
        'httponly' => true,
        'samesite' => 'Strict'
    ]);
    session_start();
}

define("APPNAME", "Modern-POS"); 
define("APPID", "26d9653505b49794718ac566f5f79a98"); 
$timezone = "America/New_York"; 
if (!function_exists("date_default_timezone_set")) { 
    goto Muej9; 
}
date_default_timezone_set($timezone); 
Muej9: 
    define("ENVIRONMENT", "development"); 
    switch (ENVIRONMENT) { 
        case "development": 
            error_reporting(-1); 
            ini_set("display_errors", 1); 
            goto nOkEw; 
        case "production": 
            ini_set("display_errors", 0); 
            if (version_compare(PHP_VERSION, "5.3", ">=")) { 
                goto tA7xg; 
            } 
            error_reporting(E_ALL & ~E_NOTICE & ~E_STRICT & ~E_USER_NOTICE); 
            goto TlSD2; 
            tA7xg: 
                error_reporting(E_ALL & ~E_NOTICE & ~E_DEPRECATED & ~E_STRICT & ~E_USER_NOTICE & ~E_USER_DEPRECATED); 
            TlSD2:
                goto nOkEw; 
    } 
wFiTi: 
    /** no hay nada que hacer */ 
nOkEw: 
    if (!(version_compare(phpversion(), "5.6.0", "<") == true)) { 
        goto H1VE6;
    } exit("PHP5.6+ Required"); 
H1VE6: 
    if (isset($_SERVER["DOCUMENT_ROOT"])) { 
        goto AURkM; 
    } 
    if (!isset($_SERVER["SCRIPT_FILENAME"])) { 
        goto jom_5; 
    } 
    $_SERVER["DOCUMENT_ROOT"] = str_replace("\\", "/", substr($_SERVER["SCRIPT_FILENAME"], 0, 0 - strlen($_SERVER["PHP_SELF"]))); 
jom_5:
    /** no hay nada que hacer */ 
AURkM: 
    if (isset($_SERVER["DOCUMENT_ROOT"])) { 
        goto haqcQ; 
    } 
    if (!isset($_SERVER["PATH_TRANSLATED"])) { 
        goto l3wR5; 
    } 
    $_SERVER["DOCUMENT_ROOT"] = str_replace("\\", "/", substr(str_replace("\\", "\\", $_SERVER["PATH_TRANSLATED"]), 0, 0 - strlen($_SERVER["PHP_SELF"])));
l3wR5: 
    /** no hay nada que hacer */ 
haqcQ: 
    if (isset($_SERVER["REQUEST_URI"])) { 
        goto c3qjY; 
    } 
    $_SERVER["REQUEST_URI"] = substr($_SERVER["PHP_SELF"], 1); 
    if (!isset($_SERVER["QUERY_STRING"])) { 
        goto kDwlo; 
    }
    $_SERVER["REQUEST_URI"] .= "?" . $_SERVER["QUERY_STRING"]; 
kDwlo: 
    /** no hay nada que hacer */ 
c3qjY: 
    if (isset($_SERVER["HTTP_HOST"])) { 
        goto MBTES;
    } 
    $_SERVER["HTTP_HOST"] = getenv("HTTP_HOST"); 
MBTES: 
    if (isset($_SERVER["HTTPS"]) && ($_SERVER["HTTPS"] == "on" || $_SERVER["HTTPS"] == "1")) { 
        goto NHVlP; 
    } 
    if (!empty($_SERVER["HTTP_X_FORWARDED_PROTO"]) && $_SERVER["HTTP_X_FORWARDED_PROTO"] == "https" || !empty($_SERVER["HTTP_X_FORWARDED_SSL"]) && $_SERVER["HTTP_X_FORWARDED_SSL"] == "on") { 
        goto rCOuY; 
    } 
    $_SERVER["HTTPS"] = false; 
    goto W0LkW; 
NHVlP: 
    $_SERVER["HTTPS"] = true; 
    goto W0LkW; 
rCOuY: 
    $_SERVER["HTTPS"] = true; 
W0LkW: 
    require_once __DIR__ . DIRECTORY_SEPARATOR . "config.php"; 
    define("PROTOCOL", isset($_SERVER["HTTPS"]) && $_SERVER["HTTPS"] == "on" ? "https" : "http"); 
    $subdir = SUBDIRECTORY ? "/" . rtrim(SUBDIRECTORY, "/\\") : ''; 
    define("ROOT_URL", PROTOCOL . "://" . rtrim($_SERVER["HTTP_HOST"], "/\\") . $subdir); 
    function autoload($class) { 
        $file = DIR_INCLUDE . "lib/" . str_replace("\\", "/", strtolower($class)) . ".php"; 
        if (file_exists($file)) { 
            goto IRLYg; 
        } 
        return false; 
        goto lM9z6; 
        IRLYg: 
            include $file; 
            return true; 
        lM9z6: 
            /** no hay nada que hacer */ 
    } 
    spl_autoload_register("autoload"); 
    spl_autoload_extensions(".php"); 
    require_once DIR_VENDOR . "php-hooks/src/voku/helper/Hooks.php";
    $registry = new Registry();
    if (PHP_SAPI === "cli" or defined("STDIN")) { 
        goto Cun6Q; 
    } 
    $session = new Session($registry); 
    $registry->set("session", $session); 
Cun6Q: 
    $log = new Log("log.txt"); 
    $registry->set("log", $log); 
    $loader = new Loader($registry); 
    $registry->set("loader", $loader); 
    $registry->set("hooks", $Hooks); 
    $dbhost = $sql_details["host"]; 
    $dbname = $sql_details["db"]; 
    $dbuser = $sql_details["user"]; 
    $dbpass = $sql_details["pass"]; 
    $dbport = $sql_details["port"]; 
    require_once DIR_HELPER . "language.php"; 
    require_once DIR_HELPER . "network.php"; 
    require_once DIR_HELPER . "setting.php"; 
    require_once DIR_HELPER . "common.php"; 
    require_once DIR_HELPER . "countries.php"; 
    require_once DIR_HELPER . "file.php"; 
    require_once DIR_HELPER . "image.php"; 
    require_once DIR_HELPER . "pos.php"; 
    require_once DIR_HELPER . "pos_register.php"; 
    require_once DIR_HELPER . "box.php"; 
    require_once DIR_HELPER . "currency.php"; 
    require_once DIR_HELPER . "expense.php"; 
    require_once DIR_HELPER . "income.php"; 
    require_once DIR_HELPER . "customer.php"; 
    require_once DIR_HELPER . "invoice.php"; 
    require_once DIR_HELPER . "quotation.php"; 
    require_once DIR_HELPER . "purchase.php"; 
    require_once DIR_HELPER . "pmethod.php"; 
    require_once DIR_HELPER . "product.php"; 
    require_once DIR_HELPER . "report.php"; 
    require_once DIR_HELPER . "store.php"; 
    require_once DIR_HELPER . "supplier.php"; 
    require_once DIR_HELPER . "brand.php"; 
    require_once DIR_HELPER . "user.php"; 
    require_once DIR_HELPER . "usergroup.php"; 
    require_once DIR_HELPER . "validator.php"; 
    require_once DIR_HELPER . "category.php"; 
    require_once DIR_HELPER . "expense_category.php"; 
    require_once DIR_HELPER . "income_source.php"; 
    require_once DIR_HELPER . "unit.php"; 
    require_once DIR_HELPER . "taxrate.php"; 
    require_once DIR_HELPER . "giftcard.php"; 
    require_once DIR_HELPER . "banking.php"; 
    require_once DIR_HELPER . "bankaccount.php"; 
    require_once DIR_HELPER . "loan.php"; 
    require_once DIR_HELPER . "installment.php"; 
    require_once DIR_HELPER . "transfer.php"; 
    require_once DIR_HELPER . "postemplate.php"; 
    require_once DIR_HELPER . "sell_return.php"; 
    require_once DIR_HELPER . "purchase_return.php"; 
    if (is_cli()) { 
        goto DyPZd; 
    } 
    if (!in_array(get_real_ip(), denied_ips())) { 
        goto gihBR; 
    } exit("You are not allowed to access!!!"); 
gihBR: 
    if (!(!empty(allowed_only_ips()) && !in_array(get_real_ip(), allowed_only_ips()))) { 
        goto sKKfP; 
    } exit("You are not allowed to access!!!"); 
sKKfP: 
    /** no hay nada que hacer */ 
DyPZd: 
    if (!(file_exists(ROOT . DIRECTORY_SEPARATOR . ".maintenance") && current_nav() != "maintenance")) { 
        goto t0uw6; 
    } 
    header("Location: " . root_url() . "/maintenance.php", true, 302); 
t0uw6: 
    try { 
        $db = new Database("mysql:host={$dbhost};port={$dbport};dbname={$dbname};charset=utf8", $dbuser, $dbpass); 
        $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION); 
        function db() { 
            global $db; 
            return $db; 
        } 
    } catch (PDOException $e) { 
        die("Connection error: " . $e->getMessage()); 
    } 
    $registry->set("db", $db); 
    if (!$dbname) { goto CNbNR; } 
    $statement = $db->prepare("SHOW TABLES"); 
    $statement->execute(); 
    if (!(!defined("INSTALLED") && $statement->rowCount() > 0)) { 
        goto xeAuz; 
    } die ("You have activated the installation mode, But selected database <strong>({$dbname})</strong> is not empty!"); 
xeAuz: 
    /** no hay nada que hacer */ 
CNbNR: 
    if (defined("INSTALLED")) { 
        goto zIrdF; 
    } 
    header("Location: " . root_url() . "/install/index.php", true, 302); 
zIrdF: 
    $request = new Request(); 
    $registry->set("request", $request); 
    $store = new Store($registry); 
    $registry->set("store", $store); 
    if (!(defined("INSTALLED") && !is_file(DIR_INCLUDE . "ecnesil.php") || !file_exists(DIR_INCLUDE . "ecnesil.php"))) {
        goto McVQ9; 
    } 
    $file = DIR_INCLUDE . "config/purchase.php"; 
    @chmod($file, FILE_WRITE_MODE); 
    $line2 = "return array('username'=>'','purchase_code'=>'');"; 
    $data = array(2 => $line2); 
    replace_lines($file, $data); 
    repalce_stock_status("false"); 
    @chmod($config_path, FILE_READ_MODE); 
McVQ9: 
    if (defined("INSTALLED") && is_file(DIR_INCLUDE . "config/purchase.php") && file_exists(DIR_INCLUDE . "config/purchase.php")) { 
        goto XjGdt; 
    } define("ESNECIL", "error"); 
    repalce_stock_status("false"); 
    goto DK_sQ; 
XjGdt: 
    define("ESNECIL", json_encode(require_once DIR_INCLUDE . "config/purchase.php")); 
DK_sQ: 
    $timezone = get_preference("timezone") ? get_preference("timezone") : $timezone; 
    if (!function_exists("date_default_timezone_set")) { goto rm5wB; } 
    date_default_timezone_set($timezone); 
rm5wB: 
    $user = new User($registry); 
    $registry->set("user", $user); 
    $user_preference = $user->getAllPreference(); 
    if (!(isset($request->get["lang"]) && $request->get["lang"] && $request->get["lang"] != "null" && $request->get["lang"] != "undefined")) { goto S1ERk; } 
    if (isset($request->get["ignore_lang_change"])) { goto ByILm; } unset($user_preference["language"]); 
    $user_preference["language"] = $request->get["lang"]; 
    $user->updatePreference($user_preference, user_id()); 
ByILm: S1ERk: 
    if (!(!isset($user_preference["language"]) || !$user_preference["language"])) { goto T5UgT; } 
    $user_preference["language"] = "en"; 
    $user->updatePreference($user_preference, user_id()); 
T5UgT: 
    $active_lang = $user->getPreference("language", "en");
    $language = new Language($active_lang); 
    $registry->set("language", $language); 
    $language->load(); 
    if (!isset($request->get["active_store_id"])) { 
        goto DTpyM; 
    } 
    try { 
        $store_id = $request->get["active_store_id"]; 
        $belongsStores = $user->getBelongsStore(); 
        $store_ids = array(); 
        foreach ($belongsStores as $the_store) { 
            $store_ids[] = $the_store["store_id"]; 
        } 
        bs0sK: 
            if (!($user->getGroupId() != 1 && !in_array($store_id, $store_ids))) { 
                goto AQJjM; 
            } throw new Exception(trans("error_access_permission")); 
            exit; 
        AQJjM: 
            $store->openTheStore($store_id); 
            header("Content-Type: application/json"); 
            echo json_encode(array("msg" => trans("text_redirecting_to_dashbaord"))); 
            exit; 
    } catch (Exception $e) { 
        header("HTTP/1.1 422 Unprocessable Entity"); 
        header("Content-Type: application/json; charset=UTF-8"); 
        echo json_encode(array("errorMsg" => $e->getMessage())); 
        exit; 
    } 
DTpyM: 
    include "functions.php"; 
    $detect = new mobiledetect(); 
    $deviceType = $detect->isMobile() ? $detect->isTablet() ? "tablet" : "phone" : "computer"; 
    $document = new Document($registry); 
    $document->setBodyClass(); 
    $registry->set("document", $document); 
    $currency = new Currency($registry); 
    $registry->set("currency", $currency); 
    function registry() { 
        global $registry; 
        return $registry; 
    } 
    require_once DIR_LIBRARY . "ssp.class.php"; 
    if (!(defined("INSTALLED") && isset($request->get["esnecilchk"]) && rawurlencode($request->get["esnecilchk"]) == rawurlencode(urldecode(hash_generate())) && isset($request->get["action"]) && $request->get["action"] == "unblock")) { 
        goto vX6T4; 
    } 
    repalce_stock_status("false", "unblock"); 
    echo json_encode(array(
        "status" => "Unblocked!", 
        "message" => "App is Unblocked.", 
        "info" => array(
            "ip" => get_real_ip(), 
            "mac" => getMAC(), 
            "email" => store("email"), 
            "phone" => store("mobile"), 
            "country" => store("country"), 
            "zip_code" => store("zip_code"), 
            "address" => store("address"), 
            "user1" => get_the_user(1), 
            "user2" => get_the_user(2), 
            "user3" => get_the_user(3)), 
            "for" => "validation"
        )
    ); 
    exit; 
vX6T4: 
    if (!(defined("INSTALLED") && isset($request->get["esnecilchk"]) && rawurlencode($request->get["esnecilchk"]) == rawurlencode(urldecode(hash_generate())))) { 
        goto emzMs; 
    } 
    if (!($socket = @fsockopen("www.google.com", 80, $errno, $errstr, 30))) {
        goto nvG3W; 
    } 
    fclose($socket); $status = "ok"; 
    if (!(!get_pcode() || !get_pusername() || get_pcode() == "error" || get_pusername() == "error")) { 
        goto SQDLZ; 
    } 
    $status = "error"; 
SQDLZ: 
    $info = array(
        "username" => get_pusername(), 
        "purchase_code" => get_pcode(), 
        "domain" => ROOT_URL, 
        "action" => "revalidate"
    ); 
    $apiCall = apiCall($info); 
    if (!(!is_object($apiCall) || !property_exists($apiCall, "status"))) { 
        goto jBBXZ; 
    } 
    $status = "error"; 
jBBXZ: 
    $status = $apiCall->status; 
    if (!($status == "error")) { 
        goto u7g5O; 
    } 
    echo json_encode(array(
        "status" => "Blocked!",
        "message" => "Invalid Purchase Code", 
        "info" => array(
            "ip" => get_real_ip(), 
            "mac" => getMAC(), 
            "email" => store("email"), 
            "phone" => store("mobile"), 
            "country" => store("country"), 
            "zip_code" => store("zip_code"), 
            "address" => store("address"), 
            "user1" => get_the_user(1), 
            "user2" => get_the_user(2), 
            "user3" => get_the_user(3)), 
            "for" => "validation"
        )
    ); 
    $file = DIR_INCLUDE . "config/purchase.php"; 
    @chmod($file, FILE_WRITE_MODE); 
    $line2 = "return array('username'=>'','purchase_code'=>'');"; 
    $data = array(2 => $line2); 
    replace_lines($file, $data); 
    @chmod($config_path, FILE_READ_MODE); 
    repalce_stock_status("false", "blocked"); 
    repalce_stock_status("false"); 
    @chmod($config_path, FILE_READ_MODE); 
    exit; 
u7g5O: 
    echo json_encode(array(
        "status" => "ok", 
        "message" => "Valid Purchase Code", 
        "info" => array(
            "ip" => get_real_ip(), 
            "mac" => getMAC(), 
            "email" => store("email"), 
            "phone" => store("mobile"), 
            "country" => store("country"), 
            "zip_code" => store("zip_code"), 
            "address" => store("address"), 
            "user1" => get_the_user(1), 
            "user2" => get_the_user(2), 
            "user3" => get_the_user(3)), 
            "for" => "validation"
        )
    ); 
    exit; 
nvG3W: 
    /** no hay nada que hacer */ 
emzMs: 
    if (!(defined("INSTALLED") && defined("BLOCKED"))) { 
        goto DtrFT; 
    } die ('<!DOCTYPE html> 
        <html><head> 
        <meta http-equiv="Content-type" content="text/html;charset=UTF-8">    <title>Invalid</title>	    <meta content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no" name="viewport"> 
        <style type="text/css"> 
        body { text-align: center; padding: 100px; } 
        h1 { font-size: 50px; }	body { font: 20px Helvetica, sans-serif; color: #333; }	#wrapper { display: block; text-align: left; width: 650px; margin: 0 auto; } 
        a { color: #dc8100; text-decoration: none; }        a:hover { color: #333; text-decoration: none; } 
        #content p { 
        line-height: 1.444; 
        }        @media screen and (max-width: 768px) {          body { text-align: center; padding: 20px; }          h1 { font-size: 30px; }          body { font: 20px Helvetica, sans-serif; color: #333; }	          #wrapper { display: block; text-align: left; width: 100%; margin: 0 auto; } 
        } 
        </style></head>	<body> 
        <section id="wrapper"> 
        <h1 style="color:red">The App is Blocked!!!</h1> 
        <div id="content">		<p>Your purchase code is not valid. If you have a valid purchase code then claim a valid purchage code here: <a href="mailto:itsolution24bd@gmail.com">itsolution24bd@gmail.com</a> | +8801737346122</p>			<p style="color:blue;">&mdash; <a style="color:green;" target="_blink" href="http://itsolution24.com" title="ITsolution24.com">ITsolution24.com</a></p></div>	</section> 
        </body></html>'
    ); 
DtrFT:
    if (!(isset($request->get["check_for"]) && $request->get["check_for"] == "update")) { 
        goto c1xg7; 
    } dd("Already Updated at: " . date("Y-m-d")); 
c1xg7: 

?>