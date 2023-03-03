<?php

if ((empty($_REQUEST['leads']) && empty($_REQUEST['contacts']) && empty($_REQUEST['company'])) || empty($_REQUEST['account'])) {
    die('Access denied');
}


define('MODX_API_MODE', true);

require dirname(dirname(dirname(dirname(__FILE__)))) . '/index.php';

/** @var modX $modx */
$modx->getService('error', 'error.modError');
$modx->setLogLevel(modX::LOG_LEVEL_ERROR);
$modx->setLogTarget('FILE');

$modx->lexicon->load('default');

$output = $modx->lexicon('failure');

/** @var amoCRM $amo */
$amo = $modx->getService('amocrm', 'amoCRM',
    $modx->getOption('amocrm_core_path', null,
        $modx->getOption('core_path') . 'components/amocrm/') . 'model/amocrm/', array()
);

$webhook = $amo->tools->getWebhook();

if ($amo && $webhook = $amo->tools->getWebhook()) {
    return $webhook->process($_POST);
} else {
    @session_write_close();
    header($_SERVER['SERVER_PROTOCOL'] . ' 500 Internal Server Error', true, 500);
}
if ((empty($_REQUEST['leads']) && empty($_REQUEST['contacts']) && empty($_REQUEST['company'])) || empty($_REQUEST['account'])) {
    die('Access denied');
}
