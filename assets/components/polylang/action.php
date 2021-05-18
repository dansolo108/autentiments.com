<?php
if (empty($_REQUEST['action'])) {
    @session_write_close();
    die('Access denied');
}
header('Content-Type: application/json; charset=UTF-8');
define('MODX_API_MODE', true);
require_once dirname(dirname(dirname(dirname(__FILE__)))) . '/index.php';

$modx->getService('error', 'error.modError');
$modx->setLogLevel(modX::LOG_LEVEL_ERROR);
$modx->setLogTarget('FILE');

/** @var Polylang $polylang */
$polylang = $modx->getService('polylang', 'Polylang');
$polylang->config['prepareResponse'] = true;
if (!$response = $polylang->runProcessor($_REQUEST['action'], $_REQUEST)) {
    $response = $modx->toJSON(array(
        'success' => false,
        'code' => 401,
    ));
}
@session_write_close();
echo $response;