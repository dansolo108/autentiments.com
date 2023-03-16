<?php


class msOptionsPriceMsOnGetProductPrice extends msOptionsPricePlugin
{
    public function run()
    {
        if ($this->modx->context->key == 'mgr') {
            return;
        }

        /** @var  msProduct $product */
        $product = $this->modx->getOption('product', $this->scriptProperties);
        if (
            !$product
            OR
            !($product instanceof xPDOObject)
        ) {
            return;
        }

        $rid = $product->get('id');
        $data = $this->modx->getOption('data', $this->scriptProperties, array(), true);
        $returned = (array)$this->modx->getPlaceholder('_returned_price');
        $reload = (bool)$this->modx->getOption('msoptionsprice_reload', $data, false, true);
        $options = $this->modx->getOption('msoptionsprice_options', $data);
        $mid = $this->modx->getOption('msoptionsprice_mid', $data, $this->modx->getOption('mid', $_REQUEST), true);

        if (
            empty($options)
            AND
            (isset($returned['id']) AND $returned['id'] == $rid)
        ) {
            $options = $this->modx->getOption('msoptionsprice_options', $returned);
        } else {
            if (empty($options)) {
                $options = $this->modx->getOption('options', $_REQUEST);
            }
        }

        if (!is_array($options) AND !$mid) {
            return;
        }

        if (
            !$price = $this->modx->getOption('price', $returned)
            OR
            !isset($returned['id'])
            OR
            $returned['id'] != $rid
            OR
            $reload
        ) {
            $price = $this->modx->getOption('price', $this->scriptProperties, 0, true);

            /*// TODO FIX
            if (!empty($this->modx->Event->returnedValues['price'])) {
                $price = $this->modx->Event->returnedValues['price'];
            }*/
        }


        $queryOptions = $options;
        $modifications = array();
        $excludeIds = $excludeType = array(0);

        $cost = $price;

        /* get modification by id */
        if ($mid AND $modification = $this->msoptionsprice->getModificationById($mid, $rid)) {
            $modifications[] = $modification;
            $excludeIds[] = $modification['id'];
            $cost = $this->msoptionsprice->getCostByModification($rid, $price, $modification);
            if ($cost !== false) {
                $price = $cost;
                if (isset($modification['options'])) {
                    $options = (array)$modification['options'];
                }
            }
        }

        if (empty($modifications)) {
            while ($modification = $this->msoptionsprice->getModificationByOptions($rid, $queryOptions, null, $excludeIds, $excludeType)) {
                $modifications[] = $modification;
                $excludeIds[] = $modification['id'];
                // TODO hz
                if ($modification['id'] AND $modification['type'] == 1) {
                    $excludeType[] = 1;
                }
                $cost = $this->msoptionsprice->getCostByModification($rid, $price, $modification);
                if ($cost !== false) {
                    $price = $cost;
                }
            }
        }

        if (empty($modifications) AND $modification = $this->msoptionsprice->getModificationById(0, $rid, $options)) {
            $modifications[] = $modification;
            $cost = $this->msoptionsprice->getCostByModification($rid, $price, $modification);

            if (isset($modification['options'])) {
                $options = (array)$modification['options'];
            }
        }

        /*******************************************/
        $response = $this->msoptionsprice->miniShop2->invokeEvent('msopOnGetFullCost', array(
            'product'       => $product,
            'rid'           => $rid,
            'cost'          => $cost,
            'options'       => $options,
            'modifications' => $modifications,
        ));
        if (!$response['success']) {
            return $response['message'];
        }
        $rid = $response['data']['rid'];
        $cost = $response['data']['cost'];
        $options = $response['data']['options'];
        /*******************************************/

        $returned['id'] = $rid;
        $returned['msoptionsprice_options'] = $options;
        $this->modx->event->returnedValues['price'] = $returned['price'] = $cost;
        $this->modx->setPlaceholder('_returned_price', $returned);

    }
}