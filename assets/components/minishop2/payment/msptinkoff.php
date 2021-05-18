<?php

ini_set('apc.cache_by_default', 'Off');

$stream = file_get_contents('php://input');
$stream = json_decode($stream, true);
if (!empty($stream) AND is_array($stream)) {
    $_REQUEST = array_merge($_REQUEST, $stream);
}

define('MODX_API_MODE', true);
require dirname(dirname(dirname(dirname(dirname(__FILE__))))) . '/index.php';
$modx->getService('error', 'error.modError');
$modx->setLogLevel(modX::LOG_LEVEL_ERROR);
$modx->setLogTarget('FILE');

/* @var miniShop2 $miniShop2 */
$miniShop2 = $modx->getService('minishop2', 'miniShop2', $modx->getOption('minishop2.core_path', null,
        $modx->getOption('core_path') . 'components/minishop2/') . 'model/minishop2/', []);
$miniShop2->loadCustomClasses('payment');
if (!class_exists('mspTinkoff')) {
    $modx->log(xPDO::LOG_LEVEL_ERROR, '[miniShop2:mspTinkoff] could not load payment class "mspTinkoff".');
}

if ($modx->getOption('ms2_payment_tinkoff_showLog', null, false, true)) {
    $modx->log(xPDO::LOG_LEVEL_ERROR, '[miniShop2:mspTinkoff] Test log.');
    $modx->log(xPDO::LOG_LEVEL_ERROR, print_r($_REQUEST, 1));
}

if (!empty($_REQUEST['OrderId'])) {

    $result = true;
    $identifierOrder = $modx->getOption('ms2_payment_tinkoff_identifierOrder', null, 'id', true);
    /** @var msOrder $order */
    if (!$order = $modx->getObject('msOrder', [$identifierOrder => (string)$_REQUEST['OrderId']])) {
        $order = $modx->newObject('msOrder');
        $result = false;
    }
    $modx->switchContext($order->get('context'));
    /** @var msPaymentInterface|mspTinkoff $handler */
    $handler = new mspTinkoff($order);
    if ($result) {
        $handler->receive($order, $_REQUEST);
    } else {
        $handler->paymentError('Order not found', $_REQUEST);
    }

} else {
    $modx->log(xPDO::LOG_LEVEL_ERROR, '[miniShop2:mspTinkoff] Wrong orderId.');
}

echo 'OK';