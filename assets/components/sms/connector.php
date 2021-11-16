<?php
if (file_exists(dirname(dirname(dirname(dirname(__FILE__)))) . '/config.core.php')) {
    require_once dirname(dirname(dirname(dirname(__FILE__)))) . '/config.core.php';
} else {
    require_once dirname(dirname(dirname(dirname(dirname(__FILE__))))) . '/config.core.php';
}
require_once MODX_CORE_PATH . 'config/' . MODX_CONFIG_KEY . '.inc.php';
require_once MODX_CONNECTORS_PATH . 'index.php';
$sms = $modx->getService('sms', 'sms', $modx->getOption('sms_core_path', null,
        MODX_CORE_PATH . 'components/sms/') . 'model/sms/'
);
$modx->lexicon->load('sms:default');

$corePath = $modx->getOption('core_path') . 'components/sms/';
$path = $modx->getOption('processorsPath', $sms->config, $corePath . 'processors/');
$modx->getRequest();

$request = $modx->request;
$request->handleRequest(array(
    'processors_path' => $path,
    'location' => '',
));