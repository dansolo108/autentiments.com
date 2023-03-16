<?php

//ini_set('display_errors', 1);
//ini_set('error_reporting', -1);

if (!defined('MODX_CORE_PATH')) {
    $path = __DIR__;
    while (!file_exists($path . '/core/config/config.inc.php') AND (strlen($path) > 1)) {
        $path = dirname($path);
    }
    define('MODX_CORE_PATH', $path . '/core/');
}
if (!defined('MODX_CORE_PATH')) {
    exit('Core not found');
}

/** @noinspection PhpIncludeInspection */
require_once MODX_CORE_PATH . "config/config.inc.php";
/** @noinspection PhpIncludeInspection */
require_once MODX_CONNECTORS_PATH . "index.php";

// load services
$modx->setLogTarget(XPDO_CLI_MODE ? 'ECHO' : 'FILE');

$modx->setLogLevel(modX::LOG_LEVEL_ERROR);
$modx->getService('error', 'error.modError');
$modx->error->message = null;

