<?php

class msOptionsPricemsOnSaveOrder extends msOptionsPricePlugin
{
    public function run()
    {
        if (!$this->msoptionsprice->getOption('allow_remains', null)) {
            return;
        }
        if (!$before = $this->msoptionsprice->getOption('order_products_before')) {
            return;
        }

        $object = $this->modx->getOption('object', $this->scriptProperties);
        if (is_object($object) AND $object instanceof msOrder AND $object->get('update_products')) {
            if ($order = $this->modx->getObjectGraph('msOrder', array('Products' => array()), $object->get('id'), false) AND $o = $order->Products) {
                $after = $o;
            }
        }

        if (isset($before) AND isset($after)) {
            $arr_before = array_map(function (msOrderProduct $o) {
                return $o->toArray();
            }, $before);
            $arr_after = array_map(function (msOrderProduct $o) {
                return $o->toArray();
            }, $after);

            if ((serialize($arr_before) != serialize($arr_after))) {
                $this->msoptionsprice->processOrderProductsRemains($before, 'return');
                $this->msoptionsprice->processOrderProductsRemains($after, 'pickup');
            }
        }
    }
}