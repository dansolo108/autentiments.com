<?php
require_once dirname(__FILE__, 4) . '/config.core.php';
require_once MODX_CORE_PATH . 'config/' . MODX_CONFIG_KEY . '.inc.php';
require_once MODX_CONNECTORS_PATH . 'index.php';
/** @var gitModx $modx */
/** @var Autentiments $autentiments */
$autentiments = $modx->getService('autentiments');

$modx->lexicon->load('autentiments:manager');
$modx->request->handleRequest(array(
    'processors_path' => $autentiments->config['processors_path'],
    'location' => '',
));