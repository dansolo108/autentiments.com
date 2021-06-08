<?php
exit;
ini_set('display_errors', 1);
ini_set('error_reporting', 1);

define('MODX_API_MODE', true);
require_once dirname(dirname(dirname(dirname(__FILE__)))) . '/index.php';

$modx->getService('error', 'error.modError');
$modx->getRequest();
$modx->setLogLevel(modX::LOG_LEVEL_ERROR);
$modx->setLogTarget('FILE');
$modx->error->message = null;

/* @var mSync $mSync */
$mSync = $modx->getService('msync', 'mSync', $modx->getOption('msync_core_path', null, $modx->getOption('core_path') . 'components/msync/') . 'model/msync/', array());
if ($modx->error->hasError() || !($mSync instanceof mSync)) {
    die('Error');
}
$mSync->initialize('web', array('json_response' => true));


header("Content-type: text/xml; charset=windows-1251");
$response = $mSync->sale->query();

@session_write_close();
exit($response);