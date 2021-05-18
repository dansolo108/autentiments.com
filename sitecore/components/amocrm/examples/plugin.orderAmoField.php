<?php
/** @var modX $modx */
$propsElem = $modx->getOption('amocrm_order_properties_element', 'amoCRMFields');
switch ($modx->event->name) {
    case 'msOnAddToCart':
        /** @var msCartHandler $cart */
        $cartArray = $cart->get();
        foreach ($cartArray as & $good) {
            /** @var msProduct $product */
            if ($product = $modx->getObject('modResource', $good['id'])) {
                $good['options'][$propsElem] = array('date' => $product->getTVValue('teaching_datatime'), 'tags' => $product->getTVValue('teaching_leading'));
            }
        }
        $cart->set($cartArray);
         $modx->log(1, 'CART: ' . print_r($cart->get(), 1));
        break;
    case 'msOnBeforeCreateOrder':
        /** @var msOrder $msOrder */
        /** @var amoCRM $amoCRM */
        $amoCRM = $modx->getService('amocrm');
        /** @var msOrderProduct[] $goods */
        $goods = $msOrder->getMany('Products');
        foreach ($goods as $good) {
            $goodOptions = $good->get('options');
            
            // $modx->log(1, 'GOOD OPTIONS: ' . print_r($goodOptions, 1));
            if (isset($goodOptions[$propsElem]['responsible_user_id'])) {
                
                // $modx->log(1, 'responsible_user_id: ' . $goodOptions['amoCRMFields']['responsible_user_id']);
                $orderProps = $amoCRM->tools->mergeOrderOptions($msOrder->get('properties'), $goodOptions[$propsElem]);
                $msOrder->set('properties', $orderProps);
            }
        }
        // $modx->log(1, 'ORDER PROPERTIES: ' . print_r($msOrder->get('properties'), 1));
        break;
}