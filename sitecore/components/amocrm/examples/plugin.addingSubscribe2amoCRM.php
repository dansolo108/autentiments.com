<?php
/** @var modX $modx */
$propsElem = $modx->getOption('amocrm_order_properties_element', 'amoCRMFields');
switch ($modx->event->name) {
    case 'PasOnBeforeCreateOrder':

        /** @var msOrder $msOrder */
        /** @var amoCRM $amoCRM */
        $amoCRM = $modx->getService('amocrm');

        /** @var msOrderProduct[] $goods */
        $goods = $msOrder->getMany('Products');

        foreach ($goods as $good) {

            $goodOptions = $good->get('options');
            $opts = array();
            switch ($good->get('name')) {

                case 'Партнерство':
                    $opts['pipeline_id'] = 1510342;
                    break;

                case 'Пополнение':
                    $opts['pipeline_id'] = 1440514;
                    break;
            }

            if (!empty($opts)) {
                $orderProps = $amoCRM->tools->mergeOrderOptions(
                    $msOrder->get('properties'),
                    $opts,
                    array('responsible_user_id' => $modx->getOption('amocrm_default_responsible_user_id'))
                );
                $msOrder->set('properties', $orderProps);
            }
        }
        // $modx->log(1, 'ORDER PROPERTIES: ' . print_r($msOrder->get('properties'), 1));
        break;
}