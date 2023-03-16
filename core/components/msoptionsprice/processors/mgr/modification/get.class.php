<?php

/**
 * Get an msopModification
 */
class msopModificationGetProcessor extends modObjectGetProcessor
{
    public $objectType = 'msopModification';
    public $classKey = 'msopModification';
    public $languageTopics = array('msoptionsprice');
    public $permission = '';

    /** {@inheritDoc} */
    public function process()
    {
        if (!$this->checkPermissions()) {
            return $this->failure($this->modx->lexicon('access_denied'));
        }

        return parent::process();
    }

    /**
     * Return the response
     * @return array
     */
    public function cleanup()
    {
        $array = $this->object->toArray();

        return $this->success('', $array);
    }
    
}

return 'msopModificationGetProcessor';