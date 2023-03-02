<?php

class msOptionsPricemsOnRemoveOrder extends msOptionsPricePlugin
{
    public function run()
    {
        if (!$this->msoptionsprice->getOption('allow_remains', null)) {
            return;
        }

        if (!$msOrder = $this->modx->getOption('object', $this->scriptProperties) OR !($msOrder instanceof msOrder)) {
            return;
        }

        if ($msOrder->get('status') == $this->msoptionsprice->getOption('status_return_remains', null, 4, true)) {
            return;
        }

        if ($products = $msOrder->Products) {
            $this->msoptionsprice->processOrderProductsRemains($products, 'return');
        }
    }
}