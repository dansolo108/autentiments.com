<?php

class mspcActionCreateProcessor extends modObjectCreateProcessor
{
    public $objectType = 'mspcAction';
    public $classKey = 'mspcAction';
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

        $required = array('name');
        foreach ($required as $v) {
            if ($this->getProperty($v) == '') {
                $this->modx->error->addField($v, $this->modx->lexicon('mspromocode_err_field'));
            }
        }

        $unique = array('name');
        foreach ($unique as $v) {
            if ($this->modx->getCount($this->classKey, array($v => $this->getProperty($v)))) {
                $this->modx->error->addField($v, $this->modx->lexicon('mspromocode_err_ae'));
            }
        }

        if ($this->getProperty('ref') && $this->modx->getCount($this->classKey, array('ref' => true))) {
            return $this->modx->lexicon('mspromocode_err_action_ref');
        }

        return parent::beforeSet();
    }
}

return 'mspcActionCreateProcessor';
