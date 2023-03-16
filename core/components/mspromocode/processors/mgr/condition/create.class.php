<?php

class mspcConditionCreateProcessor extends modObjectCreateProcessor
{
    public $objectType = 'mspcCondition';
    public $classKey = 'mspcCondition';
    public $languageTopics = array('mspromocode:default');
    //public $permission = 'create';

    /**
     * @return bool
     */
    public function beforeSet()
    {
        $props = $this->getProperties();
        foreach ($props as $k => $v) {
            $props[$k] = $this->modx->mspromocode->sanitize($k, $v);
        }
        $this->setProperties($props);

        $owner = $this->getProperty('owner', 'coupon');
        $owner_id = (int)$this->getProperty('owner_id', 0);
        if (!empty($owner_id)) {
            $this->setProperty("{$owner}_id", $owner_id);
            $this->unsetProperty('owner_id');
        }

        /*
        $required = array('name');
        foreach( $required as $v )
        {
            if( $this->getProperty($v) == '' )
            {
                $this->modx->error->addField($v, $this->modx->lexicon('mspromocode_item_err_field'));
            }
        }

        $unique = array('name');
        foreach( $unique as $v )
        {
            if( $this->modx->getCount($this->classKey, array($v => $this->getProperty($v))) )
            {
                $this->modx->error->addField($v, $this->modx->lexicon('mspromocode_item_err_ae'));
            }
        }
        */

        return parent::beforeSet();
    }
}

return 'mspcConditionCreateProcessor';
