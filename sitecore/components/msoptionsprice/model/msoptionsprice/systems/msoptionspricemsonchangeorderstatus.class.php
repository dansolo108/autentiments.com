<?php


class msOptionsPriceMsOnChangeOrderStatus extends msOptionsPricePlugin
{
    public function run()
    {
        /** @var msOrder $msOrder */
        /** @var msOrderProduct[] $products */

        /* pickup_remains */
        if (
            $this->msoptionsprice->getOption('allow_remains', null)
            AND
            $status = $this->modx->getOption('status', $this->scriptProperties)
            AND
            $status == $this->msoptionsprice->getOption('status_pickup_remains', null, 1, true)
            AND
            $msOrder = $this->modx->getOption('order', $this->scriptProperties)
            AND
            $products = $msOrder->getMany('Products')
        ) {
            $this->msoptionsprice->processOrderProductsRemains($products, 'pickup');
        }

        /* return_remains */
        if (
            $this->msoptionsprice->getOption('allow_remains', null)
            AND
            $status = $this->modx->getOption('status', $this->scriptProperties)
            AND
            $status == $this->msoptionsprice->getOption('status_return_remains', null, 4, true)
            AND
            $msOrder = $this->modx->getOption('order', $this->scriptProperties)
            AND
            $products = $msOrder->getMany('Products')
        ) {
            $this->msoptionsprice->processOrderProductsRemains($products, 'return');
        }
    }
}
