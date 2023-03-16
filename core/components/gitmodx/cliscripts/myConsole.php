<?php
define('MODX_API_MODE', true);
require_once dirname(__FILE__, 5) . '/index.php';
/** @var $modx gitModx */
$modx->setLogTarget('ECHO');
$modx->setLogLevel(MODX_LOG_LEVEL_INFO);
require_once MODX_CORE_PATH . 'components/stik_cdek/vendor/autoload.php';


$stikAmoCRM = $modx->getService('stikAmoCRM', 'stikAmoCRM', MODX_CORE_PATH . 'components/stikamocrm/model/', []);
if (!$stikAmoCRM) {
    $modx->log(MODX_LOG_LEVEL_ERROR, 'Could not load stikAmoCRM class!');
    return;
}
$response = $stikAmoCRM->modRest->get('api/v4/leads/custom_fields');
$response = $response->process();
var_export($response);