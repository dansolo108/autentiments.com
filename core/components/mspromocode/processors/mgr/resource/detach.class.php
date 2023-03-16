<?php

class mspcResourceRemoveProcessor extends modObjectProcessor
{
    public $objectType = 'mspcResource';
    public $classKey = 'mspcResource';
    public $languageTopics = array('mspromocode:default');
    //public $permission = 'remove';

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

            $object->remove();
        }

        return $this->success();
    }
}

return 'mspcResourceRemoveProcessor';
