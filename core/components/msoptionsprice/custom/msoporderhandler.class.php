<?php

if (!class_exists('msOrderInterface')) {
    require_once MODX_CORE_PATH . 'components/minishop2/model/minishop2/msorderhandler.class.php';
}

class msopOrderHandler extends msOrderHandler implements msOrderInterface
{
    /** @var modX $modx */
    public $modx;
    /** @var miniShop2 $ms2 */
    public $ms2;
    /** @var array $config */
    public $config;

    /** @var msoptionsprice $msoptionsprice */
    public $msoptionsprice;

    /** @var array $order */
    protected $order;


    function __construct(miniShop2 & $ms2, array $config = array())
    {
        parent::__construct($ms2, $config);

        $corePath = $this->modx->getOption('msoptionsprice_core_path', null,
            $this->modx->getOption('core_path', null, MODX_CORE_PATH) . 'components/msoptionsprice/');
        /** @var msoptionsprice $msoptionsprice */
        $this->msoptionsprice = $this->modx->getService('msoptionsprice', 'msoptionsprice',
            $corePath . 'model/msoptionsprice/',
            array('core_path' => $corePath));
        if (!$this->msoptionsprice) {
            return 'Could not load msoptionsprice class!';
        }

        $this->msoptionsprice->initialize($this->modx->context->key);
    }

    public function submit($data = array())
    {
        $response = $this->ms2->invokeEvent('msOnSubmitOrder', array(
            'data'  => $data,
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
        $cart_status = $this->ms2->cart->status();
        $delivery_cost = $this->getCost(false, true);
        $cart_cost = $this->getCost(true, true) - $delivery_cost;
        $createdon = date('Y-m-d H:i:s');
        /** @var msOrder $order */
        $order = $this->modx->newObject('msOrder');
        $order->fromArray(array(
            'user_id'       => $user_id,
            'createdon'     => $createdon,
            'num'           => $this->getNum(),
            'delivery'      => $this->order['delivery'],
            'payment'       => $this->order['payment'],
            'cart_cost'     => $cart_cost,
            'weight'        => $cart_status['total_weight'],
            'delivery_cost' => $delivery_cost,
            'cost'          => $cart_cost + $delivery_cost,
            'status'        => 0,
            'context'       => $this->ms2->config['ctx'],
        ));
        // Adding address
        /** @var msOrderAddress $address */
        $address = $this->modx->newObject('msOrderAddress');
        $address->fromArray(array_merge($this->order, array(
            'user_id'   => $user_id,
            'createdon' => $createdon,
        )));
        $order->addOne($address);
        // Adding products
        $cart = $this->ms2->cart->get();
        $products = array();
        foreach ($cart as $v) {
            if ($tmp = $this->modx->getObject('msProduct', $v['id'])) {
                $name = $tmp->get('pagetitle');
            } else {
                $name = '';
            }

            /*$m = $this->msoptionsprice->getModificationByOptions($v['id'], $v['options'], null, array(0),
                array(0, 2, 3));
            if ($m AND !empty($m['article'])) {

            }*/

            /** @var msOrderProduct $product */
            $product = $this->modx->newObject('msOrderProduct');
            $product->fromArray(array_merge($v, array(
                'product_id' => $v['id'],
                'name'       => $name,
                'cost'       => $v['price'] * $v['count'],
            )));
            $products[] = $product;
        }
        $order->addMany($products);
        $response = $this->ms2->invokeEvent('msOnBeforeCreateOrder', array(
            'msOrder' => $order,
            'order'   => $this,
        ));
        if (!$response['success']) {
            return $this->error($response['message']);
        }
        if ($order->save()) {
            $response = $this->ms2->invokeEvent('msOnCreateOrder', array(
                'msOrder' => $order,
                'order'   => $this,
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
            } /** @var msPayment $payment */
            elseif ($payment = $this->modx->getObject('msPayment',
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

}
