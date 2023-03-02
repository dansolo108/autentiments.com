<?php

//ini_set('display_errors', 1);
//ini_set('error_reporting', -1);

/** @noinspection PhpIncludeInspection */
require_once dirname(dirname(dirname(__DIR__))) . '/config.core.php';
/** @noinspection PhpIncludeInspection */
require_once MODX_CORE_PATH . 'config/' . MODX_CONFIG_KEY . '.inc.php';
/** @noinspection PhpIncludeInspection */
require_once MODX_CONNECTORS_PATH . 'index.php';

/** @var array $scriptProperties */
$corePath = $modx->getOption('msoptionsprice_core_path', null, $modx->getOption('core_path', null, MODX_CORE_PATH) . 'components/msoptionsprice/');
/** @var $msOptionsPrice $msOptionsPrice */
$msOptionsPrice = $modx->getService('msoptionsprice', 'msoptionsprice', $corePath . 'model/msoptionsprice/', array('core_path' => $corePath));
if (!$msOptionsPrice) {
    return;
}
$modx->lexicon->load('msoptionsprice:default');

/** @var modConnectorRequest $request */
$request = $modx->request;
$request->handleRequest(array(
    'processors_path' => $modx->getOption('processorsPath', $msOptionsPrice->config, $corePath . 'processors/'),
    'location'        => '',
));