<?php

/**
 * Get an msopModification
 */
class msopModificationSetProcessor extends modProcessor
{
    public $objectType = 'msopModification';
    public $classKey = 'msopModification';
    public $languageTopics = array('msoptionsprice');
    public $permission = '';

    public function initialize()
    {
        return parent::initialize();
    }

    /** {@inheritDoc} */
    public function process()
    {
        /** @var msoptionsprice $msoptionsprice */
        $msoptionsprice = $this->modx->getService('msoptionsprice');
        $msoptionsprice->initialize($this->getProperty('ctx', $this->modx->context->key));

        $rid = (int)$this->getProperty('id');
        $mid = (int)$this->getProperty('mid');
        $options = $this->getProperty('options');
        if (!is_array($options)) {
            $options = array();
        }

        /** @var $product msProduct */
        if (!$product = $this->modx->getObject('msProduct', array('id' => (int)$rid))) {
            return $msoptionsprice->failure('', $this->getProperties());
        }

        $modification = null;
        $queryOptions = $options;
        $modifications = array();
        $excludeIds = $excludeType = array(0);

        /* get modification by id */
        if ($mid AND $modification = $msoptionsprice->getModificationById($mid, $rid, $options)) {
            $modifications[] = $modification;
            $excludeIds[] = $modification['id'];
        }

        /* get modification by options */
        if (empty($modifications)) {
            while (
            $modification = $msoptionsprice->getModificationByOptions($rid, $queryOptions, null, $excludeIds,
                $excludeType)
            ) {
                $modifications[] = $modification;
                $excludeIds[] = $modification['id'];
            }
        }

        /* set main modification */
        if (!empty($modifications[0])) {
            $modification = $modifications[0];

            $images = $thumbs = array();
            foreach ($modifications as $m) {
                if (!empty($m['images'])) {
                    $images = array_merge($images, $m['images']);
                }
                if (!empty($m['thumbs'])) {
                    $thumbs = $msoptionsprice->array_merge_recursive_ex($thumbs, $m['thumbs']);
                }
            }
            $modification['images'] = $msoptionsprice->cleanArray($images);
            $modification['thumbs'] = $thumbs;

            if (isset($modification['options'])) {
                $options = array_merge(
                    $options,
                    (array)$modification['options']
                );
            } else {
                $options = array_merge(
                    $options,
                    $this->modx->call('msopModificationOption', 'getOptions',
                        array(&$this->modx, $modification['id'], $modification['rid'], null))
                );
            }

            $mid = $modification['id'];
        }

        if ($modification AND !is_null($modification['id']) AND $mid) {
            $modification['cost'] = $product->getPrice(array('msoptionsprice_mid' => $mid));
            $modification['mass'] = $product->getWeight(array('msoptionsprice_mid' => $mid));
        }
        elseif ($modification AND !is_null($modification['id'])) {
            $modification['cost'] = $product->getPrice(array('msoptionsprice_options' => $options));
            $modification['mass'] = $product->getWeight(array('msoptionsprice_options' => $options));
        }

        /* process old price */
        if ($modification) {
            $modification['old_cost'] = $msoptionsprice->getOldCostByModification($modification);
        }

        /* process msbonus */
        if ($modification AND $msoptionsprice->isExistService('msBonus')) {
            $msBonus = $this->modx->getService('msbonus', 'msBonus', $this->modx->getOption('core_path') . 'components/msbonus/model/msbonus/', array());
            if ($msBonus) {
                $properties = $product->get('properties');
                $bonus = $msBonus->nonamefunction($properties['msbonus'] ?: $msBonus->config['accrual'], $modification['cost']);
                $modification['msbonus'] = $bonus;
            }
        }

        /* process msmulticurrency */
        if ($modification AND $msoptionsprice->isExistService('msmulticurrency')) {
            /** @var MsMC $msmc */
            $msmc = $this->modx->getService('msmulticurrency', 'MsMC');
            if ($msmc) {
                foreach (array('cost', 'old_cost') as $key) {
                    $modification[$key] = $msmc->getPrice($modification[$key], $modification['rid'], 0, 0, 0);
                }
            }
        }

        $data = array(
            'rid'           => $rid,
            'modification'  => $modification,
            'modifications' => $modifications,
            'options'       => $options,
            'set'           => array(
                'options' => (bool)$mid,
                'image'   => (bool)$mid,
            ),
            'errors'        => null,
        );

        return $msoptionsprice->success('', $data);
    }

}

return 'msopModificationSetProcessor';