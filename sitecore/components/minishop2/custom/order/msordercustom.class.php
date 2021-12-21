<?php

if(!class_exists('msOrderHandler')) {
    require_once dirname(dirname(dirname(__FILE__))) . '/model/minishop2/msorderhandler.class.php';
}


class msOrderCustom extends msOrderHandler implements msOrderInterface
{

    /**
     * @param miniShop2 $ms2
     * @param array $config
     */
    function __construct(miniShop2 & $ms2, array $config = array())
    {
        $this->ms2 = $ms2;
        $this->modx = $ms2->modx;
        $this->pdotools = $this->modx->getService('pdoTools');

        $this->config = array_merge(array(
            'order' => & $_SESSION['minishop2']['order'],
        ), $config);

        $this->order = &$this->config['order'];
        $this->modx->lexicon->load('minishop2:order');

        if (empty($this->order) || !is_array($this->order)) {
            $this->order = array();
        }
    }

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
        if (!empty($errors)) {
            return $this->error('ms2_order_err_requires', $errors);
        }

        $user_id = $this->ms2->getCustomerId();
        if (empty($user_id) || !is_int($user_id)) {
            return $this->error(is_string($user_id) ? $user_id : 'ms2_err_user_nf');
        }

        $cart_status = $this->ms2->cart->status();
        $delivery_cost = $this->getCost(false, true, true);
        $cart_cost = $this->getCost(true, true, true) - $delivery_cost;
        $createdon = date('Y-m-d H:i:s');
        
        /** @var msDelivery $delivery */
        $delivery = $this->modx->getObject('msDelivery', ['id' => $this->order['delivery']]);
        
        if (
            $delivery_cost <= 0 &&
            ($delivery->get('price') > 0 || $delivery->get('class')) &&
            ($delivery->get('free_delivery_rf') != 1 || !in_array(mb_strtolower($this->order['country']), ['россия','russian federation']))
        ) {
            return $this->error('stik_delivery_error_text');
        }
        
        // Выводим ошибку, если количество товаров больше допустимого для курьера по городу и оплаты при получении
        if ($cart_status['total_count'] > $this->config['local_courier_max_count'] && $this->order['delivery'] == 5 && $this->order['payment'] == 1) {
            return $this->error($this->modx->lexicon('stik_order_delivery_local_courier_max_count_err', ['count' => $this->config['local_courier_max_count']]));
        }
        
        /** @var msOrder $order */
        $order = $this->modx->newObject('msOrder');
        $order->fromArray(array(
            'user_id' => $user_id,
            'createdon' => $createdon,
            'num' => $this->getNum(),
            'delivery' => $this->order['delivery'],
            'payment' => $this->order['payment'],
            'cart_cost' => $cart_cost,
            'weight' => $cart_status['total_weight'],
            'delivery_cost' => $delivery_cost,
            'cost' => $cart_cost + $delivery_cost,
            'status' => 0,
            'context' => $this->ms2->config['ctx'],
        ));

        // Adding address
        /** @var msOrderAddress $address */
        $address = $this->modx->newObject('msOrderAddress');
        $address->fromArray(array_merge($this->order, array(
            'user_id' => $user_id,
            'createdon' => $createdon,
        )));
        $order->addOne($address);

        // Adding products
        $cart = $this->ms2->cart->get();
        $products = array();
        foreach ($cart as $v) {
            if ($tmp = $this->modx->getObject('msProduct', array('id' => $v['id']))) {
                $name = $tmp->get('pagetitle');
            } else {
                $name = '';
            }
            /** @var msOrderProduct $product */
            $product = $this->modx->newObject('msOrderProduct');
            $product->fromArray(array_merge($v, array(
                'product_id' => $v['id'],
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
            
            // Reload order object after changes in changeOrderStatus method
            $order = $this->modx->getObject('msOrder', array('id' => $order->get('id')));
            
            /** @var msPayment $payment */
            if ($payment = $this->modx->getObject('msPayment',
                array('id' => $order->get('payment'), 'active' => 1))
            ) {
                $response = $payment->send($order);
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
    public function getCost($with_cart = true, $only_cost = false, $backend = false)
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
        
        $lang = $this->modx->getOption('cultureKey');
        $percent = $this->modx->getOption('stik_maxma_cart_percent');
        
        $stikLoyalty = $this->modx->getService('stik_loyalty', 'stikLoyalty', $this->modx->getOption('core_path').'components/stik/model/', []);
        $maxma = $this->modx->getService('maxma', 'maxma', $this->modx->getOption('core_path').'components/stik/model/', []);
        $msmc = $this->modx->getService('msmulticurrency', 'MsMC');
        $userCurrencyId = $msmc->getUserCurrency();

        // $this->modx->log(1, print_r($this->order,1));

        $cart = $this->ms2->cart->status();
        $msloyalty = $this->order['msloyalty'] ?: 0;
        
        if (!empty($with_cart) && !empty($cart)) {
            if (!empty($msloyalty)) {
                $currency = (float)$this->modx->getPlaceholder('msmc.val');
                $cost_loyalty = $cart['total_cost'] - ($msloyalty * $currency);
                $cost_loyalty = $cost_loyalty ?: 0;
            }
        }
        
		$cost = $with_cart
			? ($msloyalty > 0
				? $cost_loyalty
				: $cart['total_cost'])
			: 0;
        
        $loyaltyAccrual = $stikLoyalty->getLoyaltyBonusAccrual($cart['total_cost']);

        // скидка авторизованным пользователям на первый заказ
        if ($stikLoyalty->userHasFirstOrderDiscount() === true) {
            $cost = $stikLoyalty->getFirstOrderDiscount($cost);
        }
        
        /** @var msDelivery $delivery */
        if (!empty($this->order['delivery']) && $delivery = $this->modx->getObject('msDelivery',
                array('id' => $this->order['delivery']))
        ) {
            $cost = $delivery->getCost($this, $cost);
        }
        
        if (is_array($cost)) {
            $cost = $cost[0];
        }
        $cost = $cost > 0 ? $cost : 0;
        
        if (!$delivery_cost && isset($delivery)) {
            $delivery_cost = $delivery->getCost($this, 0);
        }
        
        if (is_array($delivery_cost)) {
            $delivery_cost = $delivery_cost[0];
        }
        
        /** @var msPayment $payment */
        if (!empty($this->order['payment']) && $payment = $this->modx->getObject('msPayment',
                array('id' => $this->order['payment']))
        ) {
            $cost = $payment->getCost($this, $cost);
        }

        $response = $this->ms2->invokeEvent('msOnGetOrderCost', array(
            'order' => $this,
            'cart' => $this->ms2->cart,
            'with_cart' => $with_cart,
            'only_cost' => $only_cost,
            'cost' => $cost,
        ));
        if (!$response['success']) {
            return $this->error($response['message']);
        }
        
        $cost = $response['data']['cost'];
        
        if ($maxma->userphone) { // проверяем участвует ли пользователь в программе лояльности
            $msloyalty_allowable_amount = floor($cart['total_cost'] * $percent / 100);
            
            $bonus = $this->modx->runSnippet('msMultiCurrencyPriceFloor', ['price' => $maxma->getClientBalanceByPhone($maxma->userphone)]) /*number_format(($maxma->getClientBalanceByPhone($profile->get('mobilephone'))), 0, '.', '')*/;
            if ($bonus < $msloyalty_allowable_amount) {
                $msloyalty_allowable_amount = $bonus;
            }
            
            $pdoTools = $this->modx->getService('pdoTools');
            if ($declension = $pdoTools->getFenom()->getModifier('declension')) {
                $allowable_amount_text = $declension($msloyalty_allowable_amount, $this->modx->lexicon('stik_declension_bonuses'), true);
            }
            $msloyalty_text = $this->modx->lexicon('stik_order_loyalty_text_max') . ' ' . ($allowable_amount_text ? $allowable_amount_text : $msloyalty_allowable_amount);
        }
        
        if ($userCurrencyId != 1 && $backend === false) {
            $cost = $msmc->getPrice($cost, 0, 0, 0.0, false);
        }

        return $only_cost
            ? $cost
            : $this->success('', array(
                'cost' => $cost,
                'delivery_cost' => $msmc->getPrice($delivery_cost, 0, 0, 0.0, false),
				'msloyalty' => $msloyalty,
				'msloyalty_text' => $msloyalty_text,
				'msloyalty_allowable_amount' => $this->modx->runSnippet('msMultiCurrencyPriceFloor', ['price' => $msloyalty_allowable_amount]),
				'cost_loyalty' => $msmc->getPrice($cost_loyalty, 0, 0, 0.0, false),
				'loyalty_accrual' => $msmc->getPrice($loyaltyAccrual, 0, 0, 0.0, false),
            ));
    }
    
}
