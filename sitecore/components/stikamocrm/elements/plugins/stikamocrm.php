<?php
$stikAmoCRM = $modx->getService('stikAmoCRM', 'stikAmoCRM', MODX_CORE_PATH . 'components/stikamocrm/model/', []);
if (!$stikAmoCRM) {
    $modx->log(MODX_LOG_LEVEL_ERROR, 'Could not load stikAmoCRM class!');
    return;
}
switch ($modx->event->name) {
    case 'msOnChangeOrderStatus':
        /** @var msOrder $order */
        if ($status == 1) {
            $stikAmoCRM->queue->push($order->get('id'), 'order_create');
        } else {
            $stikAmoCRM->queue->push($order->get('id'), 'order_change_status');
        }
        break;
}