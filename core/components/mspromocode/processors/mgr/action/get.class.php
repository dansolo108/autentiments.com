<?php

class mspcActionGetProcessor extends modObjectGetProcessor
{
    public $objectType = 'mspcAction';
    public $classKey = 'mspcAction';
    public $languageTopics = array('mspromocode:default');
    //public $permission = 'view';

    /**
     * @return mixed
     */
    public function process()
    {
        if (!$this->checkPermissions()) {
            return $this->failure($this->modx->lexicon('access_denied'));
        }

        return parent::process();
    }

    public function cleanup()
    {
        $array = $this->object->toArray('', true);

        return $this->success('', $array);
    }
}

return 'mspcActionGetProcessor';
