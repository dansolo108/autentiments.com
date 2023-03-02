<?php
/**
 * @var modX $modx
 * @var MsMC $msmc
 * @var array $scriptProperties
 */

$msmc = $modx->getService('msmulticurrency', 'MsMC');
$baseCurrencyId = $modx->getOption('msmulticurrency.base_currency', null, 0, true);
$cartUserCurrency = $modx->getOption('msmulticurrency.cart_user_currency', null, 0, true);
$showInProduct = $modx->getOption('msmulticurrency.show_currency_in_product', null, 1, true);
$ctx = $modx->context->get('key');

switch ($modx->event->name) {
    case 'msOnGetStatusCart':
        $userCurrencyData = $msmc->getUserCurrencyData();
        $status['total_cost'] = $msmc->getCartTotalCost($userCurrencyData['id']);
        if (isset($status['cost']) && isset($status['key'])) {
            $status['cost'] = $msmc->getCartCost($status['key'], $userCurrencyData['id']);
        }
        $modx->event->returnedValues['status'] = $status;
        break;
    case 'msOnGetOrderCost':
        $userCurrencyData = $msmc->getUserCurrencyData();
        if (!empty($with_cart) && !empty($cost)) {
            $deliveryCost = 0;
            if (!empty($delivery_cost)) {
                $deliveryCost = $msmc->getPrice($delivery_cost, 0, $userCurrencyData['id'], 0, false);
            }
            $cost = $msmc->getOrderCost($order, $userCurrencyData['id'], $with_cart);
            $modx->event->returnedValues['cost'] = $cost + $deliveryCost;
            $modx->event->returnedValues['delivery_cost'] = $deliveryCost;
        } else if (empty($with_cart) && $only_cost) {
            $modx->event->returnedValues['cost'] = $msmc->getPrice($cost, 0, $userCurrencyData['id'], 0, false);
        }
        break;
    case 'msOnSubmitOrder':
        if (!$cartUserCurrency) {
            $userCurrencyData = $msmc->getUserCurrencyData(array('id', 'name', 'code', 'symbol_left', 'symbol_right', 'val'));
            $modx->setOption('msmc_force_base_currency', true);
            $modx->setOption('msmc_user_currency_data', $userCurrencyData);
        }
        break;
    case 'msOnBeforeCreateOrder':
        $properties = $msOrder->get('properties');
        if (empty($properties)) $properties = array();
        if ($cartUserCurrency) {
            $userCurrencyData = $msmc->getUserCurrencyData(array('id', 'name', 'code', 'symbol_left', 'symbol_right', 'val'));
            foreach ($msOrder->Products as $product) {
                $price = $msmc->getPrice($product->get('price'), 0, $userCurrencyData['id'], 0, false);
                $cost = $price * $product->get('count');
                $product->set('price', $price);
                $product->set('cost', $cost);
            }
        } else {
            $userCurrencyData = $modx->getOption('msmc_user_currency_data', null, array(), true);
        }

        $userCurrencyData['base_currency_id'] = $baseCurrencyId;
        $userCurrencyData['cart_user_currency'] = $cartUserCurrency;
        $properties['msmc'] = $userCurrencyData;
        $msOrder->set('properties', $properties);
        break;
    case 'OnBeforeDocFormSave':
        if ($resource->get('class_key') != 'msProduct') return;
        if ($resource->get('currency_id')) {
            $setId = $resource->get('currency_set_id');
            $price = $msmc->convertPriceToBaseCurrency($resource->get('msmc_price'), $resource->get('currency_id'), $setId);
            $oldPrice = $msmc->convertPriceToBaseCurrency($resource->get('msmc_old_price'), $resource->get('currency_id'), $setId);
            $resource->set('price',$price);
            $resource->set('old_price',$oldPrice);
           // $msmc->clearAllCache();
        }
        break;
    case 'msopOnModificationSave':
        if ($modification->get('currency_id')) {
            $setId = $modification->get('currency_set_id');
            $price = $msmc->convertPriceToBaseCurrency($modification->get('msmc_price'), $modification->get('currency_id'), $setId);
            $oldPrice = $msmc->convertPriceToBaseCurrency($modification->get('msmc_old_price'), $modification->get('currency_id'), $setId);
            $msmc->updateProductOptionsPrice($modification->get('id'), $price, $oldPrice);
            $msmc->clearAllCache();
        }
        break;
    case 'msOnManagerCustomCssJs':
        if (empty($showInProduct)) return;
        if (in_array($page, array('product_create', 'product_update'))) {
            $msmc->loadControllerJsCss($modx->controller);
        } else if (in_array($page, array('category_create', 'category_update'))) {
            $msmc->loadControllerJsCss($modx->controller, false);
        } else if ($page == 'orders') {
            $msmc->loadControllerJsCssOrder($modx->controller);
        }
        break;
    case 'OnMODXInit':
        $msmc->extendMsOptionsPriceModel();
        if ($ctx !== 'mgr') {
            $msmc->makePlaceholders();
        }
        break;
    case 'OnHandleRequest':
        if ($ctx === 'mgr') return;
        $key = $msmc->getSessionContextKey();
        $_SESSION[$key] = $ctx;
        break;
}

return;