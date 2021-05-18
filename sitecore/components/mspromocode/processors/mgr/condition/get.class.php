<?php

class mspcConditionGetProcessor extends modObjectGetProcessor
{
    public $objectType = 'mspcCondition';
    public $classKey = 'mspcCondition';
    public $languageTopics = array('mspromocode:default');
    //public $permission = 'view';

    /**
     * We doing special check of permission
     * because of our objects is not an instances of modAccessibleObject.
     *
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

return 'mspcConditionGetProcessor';
