<?php


/**
 * Get an msopModification
 */
class msopModificationGetProcessor extends modProcessor
{

    public $objectType = 'msopModification';
    public $classKey = 'msopModification';
    public $languageTopics = ['msoptionsprice'];
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
        $iid = (int)$this->getProperty('iid');
        $options = (array)$this->getProperty('options', []);

        /** @var $product msProduct */
        if (!$product = $this->modx->getObject('msProduct', ['id' => (int)$rid])) {
            return $msoptionsprice->failure('', $this->getProperties());
        }

        $modification = null;
        $queryOptions = $options;
        $modifications = [];
        $excludeIds = $excludeType = [0];

        /* get modification by image */
        if ($iid) {
            while (
            $modification = $msoptionsprice->getModificationByImage($rid, $iid, $queryOptions, null, $excludeIds,
                $excludeType)
            ) {
                $modifications[] = $modification;
                $excludeIds[] = $modification['id'];
            }
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

        /* get modification by id */
        if (empty($modifications) AND $modification = $msoptionsprice->getModificationById(0, $rid, $options)) {
            $modifications[] = $modification;
            $excludeIds[] = $modification['id'];
        }

        /* set main modification */
        if (!empty($modifications[0])) {
            $modification = $modifications[0];

            $images = $thumbs = [];
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
                        [&$this->modx, $modification['id'], $modification['rid'], null])
                );
            }
        }

        if ($modification AND !is_null($modification['id'])) {
            $modification['cost'] = $product->getPrice(['msoptionsprice_options' => $options]);
            $modification['mass'] = $product->getWeight(['msoptionsprice_options' => $options]);

            // TODO установить старую цену если изменилась стоимость. Изменения по тикету #7439
            if ($modification['type'] == 1 AND $modification['cost'] < $modification['price']) {
                $modification['old_cost'] = $modification['price'];
            } elseif ($returned = (array)$this->modx->getPlaceholder('_returned_price') AND $returned['id'] == $rid AND $modification['cost'] < $returned['price']) {
                $modification['old_cost'] = $returned['price'];
            }
        }

        /* process old price */
        if ($modification) {
            $modification['old_cost'] = $msoptionsprice->getOldCostByModification($modification);
        }

        /* process msbonus */
        if ($modification AND $msoptionsprice->isExistService('msBonus')) {
            $msBonus = $this->modx->getService('msbonus', 'msBonus', $this->modx->getOption('core_path') . 'components/msbonus/model/msbonus/', []);
            if ($msBonus) {
                $properties = $product->get('properties');
                $bonus = $msBonus->nonamefunction($properties['msbonus'] ? : $msBonus->config['accrual'], $modification['cost']);
                $modification['msbonus'] = $bonus;
            }
        }

        /* process msmulticurrency */
        if ($modification AND $msoptionsprice->isExistService('msmulticurrency')) {
            /** @var MsMC $msmc */
            $msmc = $this->modx->getService('msmulticurrency', 'MsMC');
            if ($msmc) {
                foreach (['cost', 'old_cost'] as $key) {
                    $modification[$key] = $msmc->getPrice($modification[$key], $modification['rid'], 0, 0, 0);
                }
            }
        }

        $data = [
            'rid'           => $rid,
            'modification'  => $modification,
            'modifications' => $modifications,
            'options'       => $options,
            'set'           => [
                'options' => (bool)$iid,
            ],
            'errors'        => null,
        ];

        return $msoptionsprice->success('', $data);
    }

}


return 'msopModificationGetProcessor';