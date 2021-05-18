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
                $tvValues = array();

                $tmplvar = $modx->getObject('modTemplateVarResource', array('tmplvarid' => 13, 'contentid' => $product->get('id')));
                $userId = is_object($tmplvar) ? $tmplvar->get('value') : null;
                if ($userProfile = $modx->getObject('modUserProfile', array('internalKey' => $userId))) {
                    $tvValues['tags'] = $userProfile->get('fullname') . ' ' . $userProfile->get('surname');
                }

                $tmplvar = $modx->getObject('modTemplateVarResource', array('tmplvarid' => 24, 'contentid' => $product->get('id')));
                $date = is_object($tmplvar) ? $tmplvar->get('value') : null;
                if (!empty($date)) {
                    $tvValues['date'] = date('d.m.Y', strtotime($date));
                    $tvValues['time'] = date('H:i', strtotime($date));
                }
                $good['options'][$propsElem] = $tvValues;
            }
        }
        $cart->set($cartArray);
        //  $modx->log(1, 'CART: ' . print_r($cart->get(), 1));
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
            if (!empty($goodOptions[$propsElem])) {

                // $modx->log(1, 'responsible_user_id: ' . $goodOptions['amoCRMFields']['responsible_user_id']);

                $orderProps = $amoCRM->tools->mergeOrderOptions(
                    $msOrder->get('properties'),
                    $goodOptions[$propsElem],
                    array('responsible_user_id' => $modx->getOption('amocrm_default_responsible_user_id'))
                );
                $msOrder->set('properties', $orderProps);
            }
        }
//        $modx->log(1, 'ORDER PROPERTIES: ' . print_r($msOrder->get('properties'), 1));
        break;
}