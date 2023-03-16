<?php

class mspcCouponUpdateProcessor extends modObjectUpdateProcessor
{
    public $objectType = 'mspcCoupon';
    public $classKey = 'mspcCoupon';
    public $languageTopics = array('mspromocode:default');

    //public $permission = 'save';

    public function beforeSave()
    {
        if (!$this->checkPermissions()) {
            return $this->modx->lexicon('access_denied');
        }

        return true;
    }

    public function beforeSet()
    {
        $id = (int)$this->getProperty('id');
        if (empty($id)) {
            return $this->modx->lexicon('mspromocode_err_ns');
        }

        $props['updatedon'] = date('Y-m-d H:i:s');
        $this->setProperties($props);

        $props = $this->getProperties();
        //$this->modx->log(MODX::LOG_LEVEL_ERROR, print_r($props,1) );
        foreach ($props as $k => $v) {
            $props[$k] = $this->modx->mspromocode->sanitize($k, $v);
        }
        //$this->modx->log(MODX::LOG_LEVEL_ERROR, print_r($props,1) );
        $this->setProperties($props);

        $required = array('code', 'discount');
        foreach ($required as $v) {
            if ($this->getProperty($v) == '') {
                $this->modx->error->addField($v, $this->modx->lexicon('mspromocode_err_field'));
            }
        }

        $unique = array('code');
        foreach ($unique as $v) {
            if ($this->modx->getCount($this->classKey, array($v => $this->getProperty($v), 'id:!=' => $id))) {
                $this->modx->error->addField($v, $this->modx->lexicon('mspromocode_err_ae'));
            }
        }

        $this->unsetProperty('allcart');

        return parent::beforeSet();
    }
}

return 'mspcCouponUpdateProcessor';
