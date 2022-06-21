<?php
/**
 *  MODX Configuration file
 */
$database_type = 'mysql';
$database_server = '127.0.0.1:3310';
$database_user = 'autentiments_dev';
$database_password = 'jV7pQ8lD9mtT1y';
$database_connection_charset = 'utf8';
$dbase = 'autntmts_dev';
$table_prefix = 'hLd5Cd_';
$database_dsn = 'mysql:host=127.0.0.1:3310;dbname=autntmts_dev;charset=utf8';
$config_options = array (
);
$driver_options = array (
);

$lastInstallTime = 1618856705;

$site_id = 'modx607dcb013a6f66.77239946';
$site_sessionname = 'SN607dcab322ba2';
$https_port = '443';
$uuid = '18a46727-e0a6-468c-9173-42c17a35245c';

if (!defined('MODX_CORE_PATH')) {
    $modx_core_path=  dirname(__DIR__).'/';
    define('MODX_CORE_PATH', $modx_core_path);
}
if (!defined('MODX_BASE_PATH')) {
    $modx_base_path= dirname(__DIR__,2).'/';
    $modx_base_url= '/';
    define('MODX_BASE_PATH', $modx_base_path);
    define('MODX_BASE_URL', $modx_base_url);
}
if (!defined('MODX_PROCESSORS_PATH')) {
    $modx_processors_path= MODX_CORE_PATH.'/model/modx/processors/';
    define('MODX_PROCESSORS_PATH', $modx_processors_path);
}
if (!defined('MODX_CONNECTORS_PATH')) {
    $modx_connectors_path= MODX_BASE_PATH.'/gate/';
    $modx_connectors_url= '/gate/';
    define('MODX_CONNECTORS_PATH', $modx_connectors_path);
    define('MODX_CONNECTORS_URL', $modx_connectors_url);
}
if (!defined('MODX_MANAGER_PATH')) {
    $modx_manager_path= MODX_BASE_PATH.'/sitepanel/';
    $modx_manager_url= '/sitepanel/';
    define('MODX_MANAGER_PATH', $modx_manager_path);
    define('MODX_MANAGER_URL', $modx_manager_url);
}

if(defined('PHP_SAPI') && (PHP_SAPI == "cli" || PHP_SAPI == "embed")) {
    $isSecureRequest = false;
} else {
    $isSecureRequest = ((isset($_SERVER['HTTPS']) && !empty($_SERVER['HTTPS']) && strtolower($_SERVER['HTTPS']) !== 'off') || $_SERVER['SERVER_PORT'] == $https_port);
}
if (!defined('MODX_URL_SCHEME')) {
    $url_scheme=  $isSecureRequest ? 'https://' : 'http://';
    define('MODX_URL_SCHEME', $url_scheme);
}
if (!defined('MODX_HTTP_HOST')) {
    if(defined('PHP_SAPI') && (PHP_SAPI == "cli" || PHP_SAPI == "embed")) {
        $http_host='dev.autentiments.com';
        define('MODX_HTTP_HOST', $http_host);
    } else {
        $http_host= array_key_exists('HTTP_HOST', $_SERVER) ? htmlspecialchars($_SERVER['HTTP_HOST'], ENT_QUOTES) : 'dev.autentiments.com';
        if ($_SERVER['SERVER_PORT'] != 80) {
            $http_host= str_replace(':' . $_SERVER['SERVER_PORT'], '', $http_host); // remove port from HTTP_HOST
        }
        $http_host .= ($_SERVER['SERVER_PORT'] == 80 || $isSecureRequest) ? '' : ':' . $_SERVER['SERVER_PORT'];
        define('MODX_HTTP_HOST', $http_host);
    }
}
if (!defined('MODX_SITE_URL')) {
    $site_url= $url_scheme . $http_host . MODX_BASE_URL;
    define('MODX_SITE_URL', $site_url);
}
if (!defined('MODX_ASSETS_PATH')) {
    $modx_assets_path= MODX_BASE_PATH.'/assets/';
    $modx_assets_url= '/assets/';
    define('MODX_ASSETS_PATH', $modx_assets_path);
    define('MODX_ASSETS_URL', $modx_assets_url);
}
if (!defined('MODX_LOG_LEVEL_FATAL')) {
    define('MODX_LOG_LEVEL_FATAL', 0);
    define('MODX_LOG_LEVEL_ERROR', 1);
    define('MODX_LOG_LEVEL_WARN', 2);
    define('MODX_LOG_LEVEL_INFO', 3);
    define('MODX_LOG_LEVEL_DEBUG', 4);
}
