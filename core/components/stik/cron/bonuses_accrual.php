<?php
require_once dirname(dirname(dirname(dirname(dirname(__FILE__))))) . '/config.core.php';
require_once MODX_CORE_PATH . 'model/modx/modx.class.php';
$modx = new modX();
$modx->initialize('mgr');

// Load main services
$modx->setLogTarget(XPDO_CLI_MODE ? 'ECHO' : 'HTML');
$modx->setLogLevel(modX::LOG_LEVEL_ERROR);
$modx->getService('error','error.modError');

// Time limit
set_time_limit(600);
$tmp = 'Trying to set time limit = 600 sec: ';
$tmp .= ini_get('max_execution_time') == 600 ? 'done' : 'error';
$modx->log(modX::LOG_LEVEL_INFO,  $tmp);

$maxma = $modx->getService('maxma', 'maxma', $modx->getOption('core_path').'components/stik/model/', []);

$orders = $modx->getCollection('msOrder', [
    'createdon:<=' => date('Y-m-d h:i:s', strtotime('-13 days')),
    'AND:createdon:>=' => date('Y-m-d h:i:s', strtotime('-15 days')),
]);

foreach ($orders as $order) {
    if (!in_array([1,4], $order->get('status'))) {
        $properties = $order->get('properties');
        if (empty($properties['msloyalty']) && !empty($properties['msloyalty_accrue'])) {
            print $order->get('num') . "<br>\n";
            $maxma->confirmOrder($order->get('id')); // подтверждаем заказ и списываем бонусы
        }
    }
}


if (!XPDO_CLI_MODE) {echo '<pre>';}
// Получим количество удаленных записей
echo "\nOperation complete in ".number_format(microtime(true) - $modx->startTime, 7) . " s\n";
echo "Удалено $count записей.\n";
if (!XPDO_CLI_MODE) {echo '</pre>';}

?>