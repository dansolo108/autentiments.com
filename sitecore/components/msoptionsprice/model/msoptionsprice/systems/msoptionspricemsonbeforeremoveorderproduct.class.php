<?php

class msOptionsPricemsOnBeforeRemoveOrderProduct extends msOptionsPricePlugin
{
    public function run()
    {
        if (!$this->msoptionsprice->getOption('allow_remains', null)) {
            return;
        }
        $object = $this->modx->getOption('object', $this->scriptProperties);
        if (is_object($object) AND $object instanceof msOrderProduct) {
            if ($order = $object->getOne('Order') AND $o = $order->getMany('Products', null, false)) {
                $this->setOption('order_products_before', $o);
            }
        }
    }
}