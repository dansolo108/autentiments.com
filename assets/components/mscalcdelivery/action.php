<?php
$path = __DIR__;
while (!file_exists($path . '/config.core.php') && (strlen($path) > 1)) {
    $path = dirname($path);
}
define("MODX_API_MODE",true);
require_once  $path. '/config.core.php';
/** @noinspection PhpIncludeInspection */
require_once MODX_CORE_PATH . 'config/' . MODX_CONFIG_KEY . '.inc.php';
require_once MODX_BASE_PATH . 'index.php';
/** @var $modx modX */
$result = $modx->runSnippet('msCalcDelivery');
@session_write_close(); exit($result);