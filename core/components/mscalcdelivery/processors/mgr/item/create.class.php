<?php

class msCalcDeliveryItemCreateProcessor extends modObjectCreateProcessor
{
    public $objectType = 'msCalcDeliveryItem';
    public $classKey = 'msCalcDeliveryItem';
    public $languageTopics = ['mscalcdelivery'];
    //public $permission = 'create';


    /**
     * @return bool
     */
    public function beforeSet()
    {
        $name = trim($this->getProperty('name'));
        if (empty($name)) {
            $this->modx->error->addField('name', $this->modx->lexicon('mscalcdelivery_item_err_name'));
        } elseif ($this->modx->getCount($this->classKey, ['name' => $name])) {
            $this->modx->error->addField('name', $this->modx->lexicon('mscalcdelivery_item_err_ae'));
        }

        return parent::beforeSet();
    }

}

return 'msCalcDeliveryItemCreateProcessor';