<?php

if (!class_exists('msOrderHandler')) {
    require_once MODX_CORE_PATH . 'components/minishop2/handlers/msorderhandler.class.php';
}


class msOrderCustom extends msOrderHandler implements msOrderInterface
{
    /** @var $changed array */
    public array $changed = [];
    /**
     * @param array $data
     *
     * @return array|string
     */
    public function submit($data = array())
    {
        $response = $this->ms2->invokeEvent('msOnSubmitOrder', array(
            'data' => $data,
            'order' => $this,
        ));
        if (!$response['success']) {
            return $this->error($response['message']);
        }
        if (!empty($response['data']['data'])) {
            $this->set($response['data']['data']);
        }

        $response = $this->getDeliveryRequiresFields();
        if ($this->ms2->config['json_response']) {
            $response = json_decode($response, true);
        }
        if (!$response['success']) {
            return $this->error($response['message']);
        }
        $requires = $response['data']['requires'];

        $errors = array();
        foreach ($requires as $v) {
            if (!empty($v) && empty($this->order[$v])) {
                $errors[] = $v;
            }
        }
        $this->modx->log(1,var_export(1,1));
        if (!empty($errors)) {
            return $this->error('ms2_order_err_requires', $errors);
        }

        $user_id = $this->ms2->getCustomerId();
        if (empty($user_id) || !is_int($user_id)) {
            return $this->error(is_string($user_id) ? $user_id : 'ms2_err_user_nf');
        }
        $this->modx->log(1,var_export(2,1));

        $cart_status = $this->ms2->cart->status();
        $delivery_cost = $this->getCost(false, true);
        $cart_cost = $this->getCost(true, true) - $delivery_cost;
        $createdon = date('Y-m-d H:i:s');

        // Выводим ошибку, если количество товаров больше допустимого для курьера по городу и оплаты при получении
        if ($cart_status['total_count'] > $this->config['local_courier_max_count'] && $this->order['delivery'] == 5 && $this->order['payment'] == 1) {
            return $this->error($this->modx->lexicon('stik_order_delivery_local_courier_max_count_err', ['count' => $this->config['local_courier_max_count']]));
        }

        /** @var msOrder $order */
        $order = $this->modx->newObject('msOrder');
        //utm метки
        $utmsKeys = ['utm_content', 'utm_medium', 'utm_campaign', 'utm_source', 'utm_term'];
        $utms = [];
        foreach ($utmsKeys as $key) {
            if (isset($_COOKIE[$key])) {
                $utms[$key] = $_COOKIE[$key];
            }
        }
        $this->modx->log(1,var_export(3,1));

        $order->fromArray(array(
            'user_id' => $user_id,
            'createdon' => $createdon,
            'num' => $this->getNum(),
            'delivery' => $this->order['delivery'],
            'payment' => $this->order['payment']?:5,//иначе оплата через тинькоф
            'cart_cost' => $cart_cost,
            'weight' => $cart_status['total_weight'],
            'delivery_cost' => $delivery_cost,
            'cost' => $cart_cost + $delivery_cost,
            'status' => 0,
            'context' => $this->ms2->config['ctx'],
            'comment' => json_encode($utms),
        ));
        // Adding address
        /** @var msOrderAddress $address */
        $address = $this->modx->newObject('msOrderAddress');
        $address->fromArray(array_merge($this->order, array(
            'user_id' => $user_id,
            'createdon' => $createdon,
        )));
        $order->addOne($address);
        $this->modx->log(1,var_export(4,1));

        // Adding products
        $cart = $this->ms2->cart->get();
        $products = array();
        foreach ($cart as $v) {
            if ($tmp = $this->modx->getObject('msProduct', array('id' => $v['product_id']))) {
                $name = $tmp->get('pagetitle');
            } else {
                $name = '';
            }
            $modification = $this->modx->getObject('Modification', $v['id']);
            if (!count($v['options']))
                $v['options'] = [];
            $v['options'] = array_merge($v['options'], $modification->getDetails());
            /** @var msOrderProduct $product */
            $product = $this->modx->newObject('msOrderProduct');
            $product->fromArray(array_merge($v, array(
                'product_id' => $v['product_id'],
                'modification_id' => $v['id'],
                'name' => $name,
                'cost' => $v['price'] * $v['count'],
            )));
            $products[] = $product;
        }
        $order->addMany($products);
        $response = $this->ms2->invokeEvent('msOnBeforeCreateOrder', array(
            'msOrder' => $order,
            'order' => $this,
        ));
        if (!$response['success']) {
            return $this->error($response['message']);
        }
        $this->modx->log(1,var_export(5,1));

        if ($order->save()) {
            $response = $this->ms2->invokeEvent('msOnCreateOrder', array(
                'msOrder' => $order,
                'order' => $this,
            ));
            if (!$response['success']) {
                return $this->error($response['message']);
            }

            $this->ms2->cart->clean();
            $this->clean();
            if (empty($_SESSION['minishop2']['orders'])) {
                $_SESSION['minishop2']['orders'] = array();
            }
            $_SESSION['minishop2']['orders'][] = $order->get('id');

            // Trying to set status "new"
            $response = $this->ms2->changeOrderStatus($order->get('id'), 1);
            if ($response !== true) {
                return $this->error($response, array('msorder' => $order->get('id')));
            }
            $this->modx->log(1,var_export(6,1));

            // Reload order object after changes in changeOrderStatus method
            /** @var msOrder $order */
            $order = $this->modx->getObject('msOrder', array('id' => $order->get('id')));
            /** @var msPayment $payment */
            if ($payment = $this->modx->getObject('msPayment',
                array('id' => $order->get('payment'), 'active' => 1))
            ) {
                $response = $payment->send($order);
                if ($this->config['return_response']) {
                    return is_array($response) ? json_encode($response) : $response;
                }
                if ($this->config['json_response']) {
                    @session_write_close();
                    exit(is_array($response) ? json_encode($response) : $response);
                } else {
                    if (!empty($response['data']['redirect'])) {
                        $this->modx->sendRedirect($response['data']['redirect']);
                    } elseif (!empty($response['data']['msorder'])) {
                        $this->modx->sendRedirect(
                            $this->modx->context->makeUrl(
                                $this->modx->resource->id,
                                array('msorder' => $response['data']['msorder'])
                            )
                        );
                    } else {
                        $this->modx->sendRedirect($this->modx->context->makeUrl($this->modx->resource->id));
                    }

                    return $this->success();
                }
            } else {
                if ($this->ms2->config['json_response']) {
                    return $this->success('', array('msorder' => $order->get('id')));
                } else {
                    $this->modx->sendRedirect(
                        $this->modx->context->makeUrl(
                            $this->modx->resource->id,
                            array('msorder' => $response['data']['msorder'])
                        )
                    );

                    return $this->success();
                }
            }
        }

        return $this->error();
    }

    /**
     * @param bool $with_cart
     * @param bool $only_cost
     *
     * @return array|string
     */

    /* Параметр $backend - кастомный. Используется для исключения предварительного расчета всех способов доставок */
//    public function getCost($with_cart = true, $only_cost = false, $backend = false){
//        $response = $this->ms2->invokeEvent('msOnBeforeGetOrderCost', array(
//            'order' => $this,
//            'cart' => $this->ms2->cart,
//            'with_cart' => $with_cart,
//            'only_cost' => $only_cost,
//        ));
//        if (!$response['success'])
//            return $this->error($response['message']);
//
//        $percent = $this->modx->getOption('stik_maxma_cart_percent');
//
//        $stikLoyalty = $this->modx->getService('stik_loyalty', 'stikLoyalty', $this->modx->getOption('core_path').'components/stik/model/', []);
//        $maxma = $this->modx->getService('maxma', 'maxma', $this->modx->getOption('core_path').'components/stik/model/', []);
//        $msmc = $this->modx->getService('msmulticurrency', 'MsMC');
//        $userCurrencyId = $msmc->getUserCurrency();
//
//        $cart = $this->ms2->cart->status();
//        $msloyalty = $this->order['msloyalty'] && $cart['total_discount'] == 0?$this->order['msloyalty']: 0;
//
//        $discount_loyalty = 0;
//        if ($msloyalty) {
//            $currency = (float)$this->modx->getPlaceholder('msmc.val');
//            $discount_loyalty =  max($msloyalty * $currency ,$discount_loyalty);
//        }
//        $cost = $cart['total_cost'] - $discount_loyalty;
//        $loyaltyAccrual = $stikLoyalty->getLoyaltyBonusAccrual($cart['total_cost']);
//
//        /** @var msDelivery $delivery */
//        if (!empty($this->order['delivery']) && $delivery = $this->modx->getObject('msDelivery', ['id' => $this->order['delivery']])) {
//            $deliveryOutput = $delivery->getCost($this, $cost);
//            if (is_array($deliveryOutput) && $deliveryOutput[1] == 0 && $deliveryOutput[2] == 0)
//                return $this->error('Доставка не рассчитана');
//
//            if(is_array($deliveryOutput))
//                $costWithDelivery = $deliveryOutput[0];
//            else
//                $costWithDelivery = $deliveryOutput;
//
//            $delivery_cost = $costWithDelivery - $cost;
//            if($cost > 20000)
//                $delivery_cost = 0;
//
//            $free_delivery = false;
//            if($delivery_cost === 0)
//                $free_delivery = true;
//            $cost = $costWithDelivery;
//        }
//        /** @var msPayment $payment */
//        if (!empty($this->order['payment']) && $payment = $this->modx->getObject('msPayment', ['id' => $this->order['payment']])) {
//            $payment_cost = $cost - $payment->getCost($this, $cost);
//            $cost += $payment_cost;
//        }
//        // скидка авторизованным пользователям на первый заказ
//
//        if ($stikLoyalty->userHasFirstOrderDiscount() === true) {
//            $noDisc = true;
//            $cart = $this->ms2->cart->get();
//            foreach($cart as $item){
//                if($item['price'] < $item['old_price'])
//                    $noDisc = false;
//            }
//            if($noDisc)
//                $cost = $stikLoyalty->getFirstOrderDiscount($cost);
//        }
//        $response = $this->ms2->invokeEvent('msOnGetOrderCost', array(
//            'order' => $this,
//            'cart' => $this->ms2->cart,
//            'with_cart' => $with_cart,
//            'only_cost' => $only_cost,
//            'cost' => $cost,
//        ));
//        if (!$response['success']) {
//            return $this->error($response['message']);
//        }
//
//        $cost = $response['data']['cost'];
//
//        if ($maxma->userphone) { // проверяем участвует ли пользователь в программе лояльности
//            $msloyalty_allowable_amount = floor($cart['total_cost'] * $percent / 100);
//
//            $bonus = $this->modx->runSnippet('msMultiCurrencyPriceFloor', ['price' => $maxma->getClientBalanceByPhone($maxma->userphone)]) /*number_format(($maxma->getClientBalanceByPhone($profile->get('mobilephone'))), 0, '.', '')*/;
//            if ($bonus < $msloyalty_allowable_amount) {
//                $msloyalty_allowable_amount = $bonus;
//            }
//
//            $pdoTools = $this->modx->getService('pdoTools');
//            if ($declension = $pdoTools->getFenom()->getModifier('declension')) {
//                $allowable_amount_text = $declension($msloyalty_allowable_amount, $this->modx->lexicon('stik_declension_bonuses'), true);
//            }
//            $msloyalty_text = $this->modx->lexicon('stik_order_loyalty_text_max') . ' ' . ($allowable_amount_text ? $allowable_amount_text : $msloyalty_allowable_amount);
//        }
//
//        if ($userCurrencyId != 1 && $backend === false) {
//            $cost = $msmc->getPrice($cost, 0, 0, 0.0, false);
//        }
//        if(!$with_cart)
//            $cost -= $cart['total_cost'] - $discount_loyalty;
//        return $only_cost
//            ? $cost
//            : $this->success('', array(
//                'cost' => $cost,
//                'delivery_cost' => $msmc->getPrice($delivery_cost, 0, 0, 0.0, false),
//                'free_delivery' => $free_delivery,
//				'msloyalty' => $msloyalty,
//				'msloyalty_text' => $msloyalty_text,
//                'cart'=>$cart,
//				'msloyalty_allowable_amount' => $this->modx->runSnippet('msMultiCurrencyPriceFloor', ['price' => $msloyalty_allowable_amount]),
//				'cost_loyalty' => $msmc->getPrice($with_cart?$cost - $discount_loyalty:$cost, 0, 0, 0.0, false),
//				'loyalty_accrual' => $msmc->getPrice($loyaltyAccrual, 0, 0, 0.0, false),
//            ));
//    }
    public function getCost($with_cart = true, $only_cost = false)
    {
        $response = $this->ms2->invokeEvent('msOnBeforeGetOrderCost', array(
            'order' => $this,
            'cart' => $this->ms2->cart,
            'with_cart' => $with_cart,
            'only_cost' => $only_cost,
        ));
        if (!$response['success']) {
            return $this->error($response['message']);
        }

        $cart = $this->ms2->cart->status();
        $cost = $with_cart
            ? $cart['total_cost']
            : 0;

        $delivery_cost = 0;
        /** @var msDelivery $delivery */
        if (
            !empty($this->order['delivery']) && $delivery = $this->modx->getObject(
                'msDelivery',
                array('id' => $this->order['delivery'])
            )
        ) {
            $delivery_cost = $delivery->getCost($this, $cost);
            if ($delivery_cost == null) {
                return $this->error("Ошибка при расчёте доставки");
            } else if (is_array($delivery_cost)) {
                $delivery_cost = $delivery_cost[0];
            }
            $delivery_cost = $delivery_cost - $cost;
            $cost += $delivery_cost;
            if ($this->storage === 'db') {
                $this->storageHandler->setDeliveryCost($delivery_cost);
            }
        }

        /** @var msPayment $payment */
        if (
            !empty($this->order['payment']) && $payment = $this->modx->getObject(
                'msPayment',
                array('id' => $this->order['payment'])
            )
        ) {
            $cost = $payment->getCost($this, $cost);
        }
        $result = [
            'cost' => $cost,
            'cart_cost' => $cart['total_cost'],
            'discount_cost' => $cart['total_discount'],
            'delivery_cost' => $delivery_cost
        ];
        $response = $this->ms2->invokeEvent('msOnGetOrderCost', array(
            'order' => $this,
            'cart' => $this->ms2->cart,
            'with_cart' => $with_cart,
            'only_cost' => $only_cost,
            'result'=>$result,
        ));
        if (!$response['success']) {
            return $this->error($response['message']);
        }
        $result = $response["data"]["result"];
        return $only_cost
            ? $result["cost"]
            : $this->success('', $result);
    }

    public function add($key, $value)
    {
        $response = $this->ms2->invokeEvent('msOnBeforeAddToOrder', array(
            'key' => $key,
            'value' => $value,
            'order' => $this,
        ));
        if (!$response['success']) {
            return $this->error($response['message']);
        }
        $value = $response['data']['value'];
        if($key == "bonuses"){
            $this->modx->log(1,$value);
        }
        if (empty($value)) {
            $this->order = $this->storageHandler->add($key);
        } else {
            $validateResponse = $this->validate($key, $value);
            if ($validateResponse["success"] === true) {
                $this->changed[$key] = $validateResponse["data"]["value"];
                $this->order = $this->storageHandler->add($key, $validateResponse["data"]["value"]);
                $validateResponse = $this->ms2->invokeEvent('msOnAddToOrder', array(
                    'key' => $key,
                    'value' => $validateResponse["data"]["value"],
                    'order' => $this,
                ));
                if (!$validateResponse['success']) {
                    return $this->error($validateResponse['message']);
                }
            } else {
                $this->order = $this->storageHandler->add($key);
                return $this->error($validateResponse['message']);
            }
        }
        return ($validateResponse["success"] === false)
            ? $this->error('', array($key => $value))
            : $this->success('',$this->changed);
    }
    /**
     * @param string $key
     * @param string $value
     *
     * @return bool|mixed|string
     */
    public function validate($key, $value)
    {
        if ($key != 'comment') {
            $value = preg_replace('/\s+/', ' ', trim($value));
        }

        $response = $this->ms2->invokeEvent('msOnBeforeValidateOrderValue', array(
            'key' => $key,
            'value' => $value,
            'order' => $this,
        ));
        $value = $response['data']['value'];

        $old_value = isset($this->order[$key]) ? $this->order[$key] : '';
        switch ($key) {
            case 'email':
                $value = preg_match('/^[^@а-яА-Я]+@[^@а-яА-Я]+(?<!\.)\.[^\.а-яА-Я]{2,}$/m', $value)
                    ? $value
                    : false;
                break;
            case 'receiver':
                // Transforms string from "nikolaj -  coster--Waldau jr." to "Nikolaj Coster-Waldau Jr."
                $tmp = preg_replace(
                    array('/[^-a-zа-яёґєіїўäëïöüçàéèîôûäüöÜÖÄÁČĎĚÍŇÓŘŠŤÚŮÝŽ\s\.\'’ʼ`"]/iu', '/\s+/', '/\-+/', '/\.+/', '/[\'’ʼ`"]/iu', '/\'+/'),
                    array('', ' ', '-', '.', '\'', '\''),
                    $value
                );
                $tmp = preg_split('/\s/', $tmp, -1, PREG_SPLIT_NO_EMPTY);
                $tmp = array_map(array($this, 'ucfirst'), $tmp);
                $value = preg_replace('/\s+/', ' ', implode(' ', $tmp));
                if (empty($value)) {
                    $value = false;
                }
                break;
            case 'phone':
                $value = preg_replace('/[^-+()0-9]/u', '', $value);
                break;
            case 'delivery':
                /** @var msDelivery $delivery */
                if (!$delivery = $this->modx->getObject('msDelivery', array('id' => $value, 'active' => 1))) {
                    $value = $old_value;
                } elseif (!empty($this->order['payment'])) {
                    if (!$this->hasPayment($value, $this->order['payment'])) {
                        $this->order['payment'] = $delivery->getFirstPayment();
                    };
                }
                break;
            case 'payment':
                if (!empty($this->order['delivery'])) {
                    $value = $this->hasPayment($this->order['delivery'], $value)
                        ? $value
                        : $old_value;
                }
                break;
            case 'index':
                $value = substr(preg_replace('/[^-0-9a-z]/iu', '', $value), 0, 10);
                break;
        }

        $response = $this->ms2->invokeEvent('msOnValidateOrderValue', array(
            'key' => $key,
            'value' => $value,
            'order' => $this,
        ));

        return $response;
    }

    function remove($key)
    {
        $this->changed[$key] = "";
        return parent::remove($key); // TODO: Change the autogenerated stub
    }


}
