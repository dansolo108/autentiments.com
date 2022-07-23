<?php
require_once dirname(dirname(dirname(dirname(__FILE__)))) . '/config.core.php';
require_once MODX_CORE_PATH . 'components/gitmodx/model/gitmodx/gitmodx.class.php';
$modx = new gitModx();
$modx->initialize('web');

// Load main services
$modx->setLogTarget('FILE');
$modx->setLogLevel(modX::LOG_LEVEL_ERROR);
$modx->getService('error','error.modError');

print $modx->runSnippet('cdekDeliveryPoints');

exit;