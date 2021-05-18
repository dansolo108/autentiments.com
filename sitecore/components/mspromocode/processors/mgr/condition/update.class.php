<?php

class mspcConditionUpdateProcessor extends modObjectUpdateProcessor
{
    public $objectType = 'mspcCondition';
    public $classKey = 'mspcCondition';
    public $languageTopics = array('mspromocode:default');
    //public $permission = 'save';

    /**
     * We doing special check of permission
     * because of our objects is not an instances of modAccessibleObject.
     * @return bool|string
     */
    public function beforeSave()
    {
        if (!$this->checkPermissions()) {
            return $this->modx->lexicon('access_denied');
        }

        return true;
    }

    /**
     * @return bool
     */
    public function beforeSet()
    {
        $id = (int)$this->getProperty('id');
        if (empty($id)) {
            return $this->modx->lexicon('mspromocode_err_ns');
        }

        $props = $this->getProperties();
        //$this->modx->log(MODX::LOG_LEVEL_ERROR, print_r($props,1) );
        foreach ($props as $k => $v) {
            $props[$k] = $this->modx->mspromocode->sanitize($k, $v);
        }
        //$this->modx->log(MODX::LOG_LEVEL_ERROR, print_r($props,1) );
        $this->setProperties($props);

        /*$required = array('name');
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
            if( $this->modx->getCount($this->classKey, array($v => $this->getProperty($v), 'id:!=' => $id)) )
            {
                $this->modx->error->addField($v, $this->modx->lexicon('mspromocode_item_err_ae'));
            }
        }*/

        return parent::beforeSet();
    }
}

return 'mspcConditionUpdateProcessor';
