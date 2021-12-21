id: 30
source: 1
name: stikAmoCRM
category: stikAmoCRM
properties: null
static_file: core/components/stikamocrm/elements/plugins/stikamocrm.php

-----

$stikAmoCRM = $modx->getService('stikAmoCRM', 'stikAmoCRM', MODX_CORE_PATH . 'components/stikamocrm/model/', []);
if (!$stikAmoCRM) {
    $modx->log(MODX_LOG_LEVEL_ERROR, 'Could not load stikAmoCRM class!');
    return;
}
switch ($modx->event->name) {
    case "msOnCreateOrder":
        /** @var msOrder $msOrder */
        $stikAmoCRM->queue->push($msOrder->get('id'), 'order_create');
        break;
    case 'msOnChangeOrderStatus':
        /** @var msOrder $order */
        $stikAmoCRM->queue->push($order->get('id'), 'order_change_status');
        break;
}