<?php

class mspcCouponCreateProcessor extends modObjectCreateProcessor
{
    public $objectType = 'mspcCoupon';
    public $classKey = 'mspcCoupon';
    public $languageTopics = array('mspromocode:default');

    //public $permission = 'create';

    public function beforeSet()
    {
        $props['createdon'] = $props['updatedon'] = date('Y-m-d H:i:s');
        $this->setProperties($props);

        // $this->modx->log(MODX::LOG_LEVEL_ERROR, print_r($this->getProperties(),1));

        $props = $this->getProperties();
        foreach ($props as $k => $v) {
            $props[$k] = $this->modx->mspromocode->sanitize($k, $v);
        }
        $this->setProperties($props);

        $required = array('code', 'discount');
        foreach ($required as $v) {
            if ($this->getProperty($v) == '') {
                $this->modx->error->addField($v, $this->modx->lexicon('mspromocode_err_field'));
            }
        }

        $unique = array('code');
        foreach ($unique as $v) {
            if ($this->modx->getCount($this->classKey, array($v => $this->getProperty($v)))) {
                $this->modx->error->addField($v, $this->modx->lexicon('mspromocode_err_ae'));
            }
        }

        // $this->modx->log(MODX::LOG_LEVEL_ERROR, print_r($this->getProperties(),1));

        return parent::beforeSet();
    }

    public function afterSave()
    {
        $resource_id = $this->getProperty('resource_id', 0);
        $resource_type = $this->getProperty('resource_type', '');

        if (!empty($resource_id) && !empty($resource_type)) {
            $resource = $this->modx->newObject('mspcResource');
            $resource->set('resource_id', $resource_id);
            $resource->set('type', $resource_type);
            $resource->set('discount', '');
            $resource->addOne($this->object);
            $resource->save();
            // $this->modx->log(MODX::LOG_LEVEL_ERROR, print_r($this->object->toArray(),1));
            // $this->modx->log(MODX::LOG_LEVEL_ERROR, print_r($resource->toArray(),1));
        }

        return parent::afterSave();
    }
}

return 'mspcCouponCreateProcessor';