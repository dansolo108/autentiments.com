<?php

class mspcResourceCreateProcessor extends modObjectCreateProcessor
{
    public $objectType = 'mspcResource';
    public $classKey = 'mspcResource';
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
        $obj_id = (int) $this->getProperty('obj_id', 0);
        if (!empty($obj_id)) {
            $this->setProperty("{$owner}_id", $obj_id);
            $this->unsetProperty('obj_id');
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

return 'mspcResourceCreateProcessor';
