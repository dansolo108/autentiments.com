<?php

class mspcConditionEnableProcessor extends modObjectProcessor
{
    public $objectType = 'mspcCondition';
    public $classKey = 'mspcCondition';
    public $languageTopics = array('mspromocode:default');
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
            return $this->failure($this->modx->lexicon('mspromocode_err_ns'));
        }

        foreach ($ids as $id) {
            /** @var mspcCondition $object */
            if (!$object = $this->modx->getObject($this->classKey, $id)) {
                return $this->failure($this->modx->lexicon('mspromocode_err_nf'));
            }

            $object->set('active', true);
            $object->save();
        }

        return $this->success();
    }
}

return 'mspcConditionEnableProcessor';
