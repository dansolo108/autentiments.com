<?php
if (file_exists(dirname(dirname(dirname(dirname(__FILE__)))) . '/config.core.php')) {
    /** @noinspection PhpIncludeInspection */
    require_once dirname(dirname(dirname(dirname(__FILE__)))) . '/config.core.php';
} else {
    require_once dirname(dirname(dirname(dirname(dirname(__FILE__))))) . '/config.core.php';
}
/** @noinspection PhpIncludeInspection */
require_once MODX_CORE_PATH . 'config/' . MODX_CONFIG_KEY . '.inc.php';
/** @noinspection PhpIncludeInspection */
require_once MODX_CONNECTORS_PATH . 'index.php';
/** @var amocrm $amocrm */
$amocrm = $modx->getService('amocrm', 'amocrm', MODX_CORE_PATH . 'components/amocrm/model/');
$modx->lexicon->load('amocrm:default');

// handle request
$corePath = $modx->getOption('amocrm_core_path', null, $modx->getOption('core_path') . 'components/amocrm/');
$path = $modx->getOption('processorsPath', $amocrm->config, $corePath . 'processors/');
$modx->getRequest();

/** @var modConnectorRequest $request */
$request = $modx->request;
$request->handleRequest([
    'processors_path' => $path,
    'location' => '',
]);