<?php

if(!class_exists('msCartHandler')) {
    require_once dirname(__DIR__, 2) . '/handlers/mscarthandler.class.php';
}

class msCartHandlerCustom extends msCartHandler implements msCartInterface
{
    public function add($id, $count = 1, $options = array())
    {
        if (empty($id) || !is_numeric($id)) {
            return $this->error('ms2_cart_add_err_id');
        }
        $count = intval($count);
        if (is_string($options)) {
            $options = json_decode($options, true);
        }
        if (!is_array($options)) {
            $options = array();
        }

        $modificationFilter = array('id' => $id);
        $filter = [];
        if (!$this->config['allow_deleted']) {
            $filter['deleted'] = 0;
        }
        if (!$this->config['allow_unpublished']) {
            $filter['published'] = 1;
        }
        if (!$this->config['allow_hidden_modification']) {
            $modificationFilter['hide'] = 0;
        }
        /** @var Modification $modification */
        $modification = $this->modx->getObject('Modification', $modificationFilter);
        $filter['id']= $modification->get('product_id');
        /** @var msProduct $product */
        if ($modification && $product = $this->modx->getObject('modResource',$filter)) {
            if (!($product instanceof msProduct)) {
                return $this->error('ms2_cart_add_err_product', $this->status());
            }
            if ($count > $this->config['max_count'] || $count <= 0) {
                return $this->error('ms2_cart_add_err_count', $this->status(), array('count' => $count));
            }
            $response = $this->ms2->invokeEvent('msOnBeforeAddToCart', array(
                'product' => $product,
                'modification'=>$modification,
                'count' => $count,
                'options' => $options,
                'cart' => $this,
            ));
            if (!($response['success'])) {
                return $this->error($response['message']);
            }
            $price = $modification->get('price');
            $oldPrice = $modification->get('old_price');
            $weight = $product->getWeight();
            $count = $response['data']['count'];
            $remains = $modification->getRemains();
            if($remains !== null && $count > $remains){
                $count = $remains;
            }
            $options = $response['data']['options'];
            $discount_price = $oldPrice > 0 ? $oldPrice - $price : 0;
            $discount_cost = $discount_price * $count;

            $key = md5($id . $price . $weight . (json_encode($options)));
            if (array_key_exists($key, $this->cart)) {
                return $this->change($key, $this->cart[$key]['count'] + $count);
            } else {
                $ctx_key = 'web';
                if (!$this->modx->getOption('ms2_cart_context', null, '', true)) {
                    $ctx_key = $this->modx->context->get('key');
                }
                $this->cart[$key] = array(
                    'id' => $id,
                    'product_id' =>$product->get('id'),
                    'price' => $price,
                    'old_price' => $oldPrice,
                    'discount_price' => $discount_price,
                    'discount_cost' => $discount_cost,
                    'weight' => $weight,
                    'count' => $count,
                    'options' => $options,
                    'ctx' => $ctx_key,
                );
                $details = $modification->getMany('Details');
                foreach ($details as $detail) {
                    $this->cart[$key][$detail->getOne('Type')->get('name')] = $detail->get('value');
                }
                $response = $this->ms2->invokeEvent('msOnAddToCart', array('key' => $key, 'cart' => $this));
                if (!$response['success']) {
                    return $this->error($response['message']);
                }
                return $this->success('ms2_cart_add_success', $this->status(array('key' => $key)),
                    array('count' => $count));
            }
        }

        return $this->error('ms2_cart_add_err_nf', $this->status());
    }
    /**
     * @param string $key
     *
     * @return array|string
     */
    public function remove($key)
    {
        if (array_key_exists($key, $this->cart)) {
            $response = $this->ms2->invokeEvent('msOnBeforeRemoveFromCart', array('key' => $key, 'cart' => $this));
            if (!$response['success']) {
                return $this->error($response['message']);
            }
            unset($this->cart[$key]);

            $response = $this->ms2->invokeEvent('msOnRemoveFromCart', array('key' => $key, 'cart' => $this));
            if (!$response['success']) {
                return $this->error($response['message']);
            }
            return $this->success('ms2_cart_remove_success', $this->status());
        } else {
            return $this->error('ms2_cart_remove_error');
        }
    }


    /**
     * @param string $key
     * @param int $count
     *
     * @return array|string
     */
    public function change($key, $count)
    {
        $status = array();
        if (array_key_exists($key, $this->cart)) {
            if ($count <= 0) {
                return $this->remove($key);
            } else {
                if ($count > $this->config['max_count']) {
                    return $this->error('ms2_cart_add_err_count', $this->status(), array('count' => $count));
                } else {
                    $response = $this->ms2->invokeEvent('msOnBeforeChangeInCart',
                        array('key' => $key, 'count' => $count, 'cart' => $this));
                    if (!$response['success']) {
                        return $this->error($response['message']);
                    }
                    /** @var Modification $modification */
                    $modification = $this->modx->getObject('Modification',$this->cart[$key]['id']);
                    $count = $response['data']['count'];
                    $remains = $modification->getRemains();
                    if($count > $remains ){
                        $count = $remains;
                    }
                    $this->cart[$key]['count'] = $count;
                    $response = $this->ms2->invokeEvent('msOnChangeInCart',
                        array('key' => $key, 'count' => $count, 'cart' => $this));
                    if (!$response['success']) {
                        return $this->error($response['message']);
                    }
                    $status['key'] = $key;
                    $status['cost'] = $count * $this->cart[$key]['price'];
                    $status['max_count'] = $remains;
                }
            }
            return $this->success('ms2_cart_change_success', $this->status($status),
                array('count' => $count));
        } else {
            return $this->error('ms2_cart_change_error', $this->status($status));
        }
    }
}