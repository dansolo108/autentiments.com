<?php


class msOptionsPriceMsOnBeforeChangeInCart extends msOptionsPricePlugin
{
    public function run()
    {
        /* remains */
        /** @var  msCartInterface $cart */
        if (
            $this->msoptionsprice->getOption('allow_remains', null)
            AND
            $key = $this->modx->getOption('key', $this->scriptProperties)
            AND
            $count = $this->modx->getOption('count', $this->scriptProperties)
            AND
            $cart = $this->modx->getOption('cart', $this->scriptProperties)
        ) {

            $items = (array)$cart->get();
            $item = isset($items[$key]) ? $items[$key] : array();


            // exclude product
            /** @var  msProduct $product */
            if (!$rid = (int)$this->modx->getOption('id', $item) OR !$product = $this->modx->getObject('msProduct', $rid)) {
                return;
            }
            if (!$this->msoptionsprice->isWorkingTemplates($product)) {
                return;
            }


            $options = (array)$this->modx->getOption('options', $item, array(), true);
            $mid = $this->modx->getOption('modification', $options);
            if (!$modification = $this->msoptionsprice->getModificationById($mid, 0, $options)) {
                return;
            }
            $remains = $this->modx->getOption('count', $modification);

            foreach ($items as $k => $item) {
                /* skip work key */
                if ($key == $k) {
                    continue;
                }
                $mid = $this->modx->getOption('modification', (array)$item['options']);
                if ($modification['id'] == $mid) {
                    $remains = $remains - $item['count'];
                }
            }

            if ($count > $remains) {
                $this->modx->event->output($this->msoptionsprice->lexicon('err_available_count'));
            }
        }

    }
}