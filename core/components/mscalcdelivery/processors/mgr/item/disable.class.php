<?php

class msCalcDeliveryItemDisableProcessor extends modObjectProcessor
{
    public $objectType = 'msCalcDeliveryItem';
    public $classKey = 'msCalcDeliveryItem';
    public $languageTopics = ['mscalcdelivery'];
    //public $permission = 'save';


    /**
     * @return array|string
     */
    public function process()
    {
        if (!$this->checkPermissions()) {
            return $this->failure($this->modx->lexicon('access_denied'));
        }

        $ids = $this->modx->fromJSON($this->getProperty('ids'));
        if (empty($ids)) {
            return $this->failure($this->modx->lexicon('mscalcdelivery_item_err_ns'));
        }

        foreach ($ids as $id) {
            /** @var msCalcDeliveryItem $object */
            if (!$object = $this->modx->getObject($this->classKey, $id)) {
                return $this->failure($this->modx->lexicon('mscalcdelivery_item_err_nf'));
            }

            $object->set('active', false);
            $object->save();
        }

        return $this->success();
    }

}

return 'msCalcDeliveryItemDisableProcessor';
