<?php
$path = __DIR__;
while (!file_exists($path . '/config.core.php') && (strlen($path) > 1)) {
    $path = dirname($path);
}
require_once  $path. '/config.core.php';
/** @noinspection PhpIncludeInspection */
require_once MODX_CORE_PATH . 'config/' . MODX_CONFIG_KEY . '.inc.php';
/** @noinspection PhpIncludeInspection */
require_once MODX_CONNECTORS_PATH . 'index.php';

/** @var modMaxma $modMaxma */
$modMaxma = $modx->getService('modMaxma', 'modMaxma', MODX_CORE_PATH . 'components/modmaxma/model/');
$modx->lexicon->load('modmaxma:default');

// handle request
$corePath = $modx->getOption('modmaxma_core_path', null, $modx->getOption('core_path') . 'components/modmaxma/');
$path = $modx->getOption('processorsPath', $modMaxma->config, $corePath . 'processors/');
$modx->getRequest();

/** @var modConnectorRequest $request */
$request = $modx->request;
$request->handleRequest([
    'processors_path' => $path,
    'location' => '',
]);
