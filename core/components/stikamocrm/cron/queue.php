<?php
require_once dirname(dirname(dirname(dirname(dirname(__FILE__))))) . '/config.core.php';
require_once MODX_CORE_PATH . 'model/modx/modx.class.php';
$modx = new modX();
$modx->initialize('mgr');

// Load main services
$modx->setLogTarget('FILE');
$modx->setLogLevel(modX::LOG_LEVEL_ERROR);
$modx->getService('error','error.modError');

// Time limit
set_time_limit(600);
$tmp = 'Trying to set time limit = 600 sec: ';
$tmp .= ini_get('max_execution_time') == 600 ? 'done' : 'error';
$modx->log(modX::LOG_LEVEL_INFO,  $tmp);

$stikAmoCRM = $modx->getService('stikAmoCRM', 'stikAmoCRM', MODX_CORE_PATH . 'components/stikamocrm/model/', []);
if (!$stikAmoCRM) {
    $modx->log(MODX_LOG_LEVEL_ERROR, 'Could not load stikAmoCRM class!');
    exit;
}

$stikAmoCRM->queue->execute();

if (!XPDO_CLI_MODE) {echo '<pre>';}
echo "\nOperation complete in ".number_format(microtime(true) - $modx->startTime, 7) . " s\n";
if (!XPDO_CLI_MODE) {echo '</pre>';}

?>