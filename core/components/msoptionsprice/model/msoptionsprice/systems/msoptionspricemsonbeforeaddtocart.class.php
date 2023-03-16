<?php


class msOptionsPriceMsOnBeforeAddToCart extends msOptionsPricePlugin
{
    public function run()
    {
        /** @var  msProduct $product */
        $product = $this->modx->getOption('product', $this->scriptProperties);
        if (!$product OR !($product instanceof xPDOObject)) {
            return;
        }

        // exclude product
        if (!$this->msoptionsprice->isWorkingTemplates($product)) {
            return;
        }


        $rid = $product->get('id');
        $options = (array)$this->modx->getOption('options', $this->scriptProperties, array(), true);
        $mid = $this->modx->getOption('msoptionsprice_mid', $this->scriptProperties, $this->modx->getOption('mid', $_REQUEST), true);

        $excludeIds = array(0);
        $excludeType = array(0, 2, 3);

        /* get modification by id */
        if ($mid AND $modification = $this->msoptionsprice->getModificationById($mid, $rid, $options)) {

        } else if (!$modification = $this->msoptionsprice->getModificationByOptions($rid, $options, null, $excludeIds,
            $excludeType)
        ) {
            /* get modification by id */
            $modification = $this->msoptionsprice->getModificationById(0, $rid, $options);
        }
        $excludeIds[] = $modification['id'];

        // TODO hz
        $excludeType = array(0, 1);
        /* get not main modification */
        while (
        $tmp = $this->msoptionsprice->getModificationByOptions($rid, $options, null, $excludeIds, $excludeType)
        ) {
            $excludeIds[] = $tmp['id'];
        }

        $options['modifications'] = $this->msoptionsprice->cleanArray($excludeIds);
        $options['modification'] = $modification['id'];
        $this->modx->event->returnedValues['options'] = $options;


        $returned = array();
        $returned['id'] = $rid;
        $returned['msoptionsprice_options'] = $options;
        $this->modx->setPlaceholder('_returned_price', $returned);

        /* remains */
        /** @var  msCartInterface $cart */
        if (
            $this->msoptionsprice->getOption('allow_remains', null)
            AND
            $count = $this->modx->getOption('count', $this->scriptProperties)
            AND
            $cart = $this->modx->getOption('cart', $this->scriptProperties)
        ) {

            $remains = $this->modx->getOption('count', $modification);

            $items = (array)$cart->get();
            foreach ($items as $item) {
                $mid = $this->modx->getOption('modification', (array)$item['options']);
                if ($modification['id'] == $mid) {
                    $remains = $remains - $item['count'];
                }
            }

            if ($count > $remains) {
                $this->modx->event->output($this->msoptionsprice->lexicon('err_available_count'));
            }
        }


        if (empty($modification['id']) AND !$this->msoptionsprice->getOption('allow_zero_modification', null, true)) {
            $this->modx->event->output($this->msoptionsprice->lexicon('err_available_modification'));
        }

    }
}