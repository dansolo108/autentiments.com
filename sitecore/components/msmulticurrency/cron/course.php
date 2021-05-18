<?php


define('MODX_API_MODE', true);
require_once dirname(dirname(dirname(dirname(dirname(__FILE__))))) . '/index.php';

$modx->getService('error', 'error.modError');
$modx->setLogLevel(modX::LOG_LEVEL_ERROR);
$modx->setLogTarget('FILE');

/** @var MsMC $msmc */
$msmc = $modx->getService('msmulticurrency', 'MsMC');
/** @var MsMCProvider $provider */
if (!$provider = $msmc->getProviderInstance()) exit;

$provider->run();
$msmc->updateProductsPrice();
$msmc->updateProductsOptionsPrice();
$msmc->clearAllCache();