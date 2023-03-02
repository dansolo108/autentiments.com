<?php


class msOptionsPriceMsOnGetProductWeight extends msOptionsPricePlugin
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
        $returned = (array)$this->modx->getPlaceholder('_returned_weight');
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
            !$weight = $this->modx->getOption('weight', $returned)
            OR
            !isset($returned['id'])
            OR
            $returned['id'] != $rid
            OR
            $reload
        ) {
            $weight = $this->modx->getOption('weight', $this->scriptProperties, 0, true);
        }


        $queryOptions = $options;
        $modifications = array();
        $excludeIds = $excludeType = array(0);

        $mass = $weight;

        /* get modification by id */
        if ($mid AND $modification = $this->msoptionsprice->getModificationById($mid, $rid)) {
            $modifications[] = $modification;
            $excludeIds[] = $modification['id'];
            $mass = $this->msoptionsprice->getMassByModification($rid, $weight, $modification);
            if ($mass !== false) {
                $weight = $mass;
                if (isset($modification['options'])) {
                    $options = (array)$modification['options'];
                }
            }
        }

        if (empty($modifications)) {
            while (
            $modification = $this->msoptionsprice->getModificationByOptions($rid, $queryOptions, null, $excludeIds,
                $excludeType)
            ) {
                $modifications[] = $modification;
                $excludeIds[] = $modification['id'];
                // TODO hz
                if ($modification['id'] AND $modification['type'] == 1) {
                    $excludeType[] = 1;
                }
                $mass = $this->msoptionsprice->getMassByModification($rid, $weight, $modification);
                if ($mass !== false) {
                    $weight = $mass;
                }
            }
        }

        if (empty($modifications) AND $modification = $this->msoptionsprice->getModificationById(0, $rid, $options)) {
            $modifications[] = $modification;
            $mass = $this->msoptionsprice->getMassByModification($rid, $weight, $modification);

            if (isset($modification['options'])) {
                $options = (array)$modification['options'];
            }
        }

        /*******************************************/
        $response = $this->msoptionsprice->miniShop2->invokeEvent('msopOnGetFullMass', array(
            'product'       => $product,
            'rid'           => $rid,
            'mass'          => $mass,
            'options'       => $options,
            'modifications' => $modifications,
        ));
        if (!$response['success']) {
            return $response['message'];
        }
        $rid = $response['data']['rid'];
        $mass = $response['data']['mass'];
        $options = $response['data']['options'];
        /*******************************************/

        $returned['id'] = $rid;
        $returned['msoptionsprice_options'] = $options;
        $this->modx->event->returnedValues['weight'] = $returned['weight'] = $mass;
        $this->modx->setPlaceholder('_returned_weight', $returned);


    }
}