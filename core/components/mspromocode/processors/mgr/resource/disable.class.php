<?php

class mspcResourceDisableProcessor extends modObjectProcessor
{
    public $objectType = 'mspcResource';
    public $classKey = 'mspcResource';
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
            /** @var mspcResource $object */
            if (!$object = $this->modx->getObject($this->classKey, $id)) {
                return $this->failure($this->modx->lexicon('mspromocode_err_nf'));
            }

            $object->set('active', false);
            $object->save();
        }

        return $this->success();
    }
}

return 'mspcResourceDisableProcessor';
