<?php

/**
 * Remove a msopModification
 */
class msopModificationRemoveProcessor extends modObjectRemoveProcessor
{
    public $classKey = 'msopModification';
    public $languageTopics = array('modclassvar');
    public $permission = '';

    public function initialize()
    {
        return parent::initialize();
    }

    /** {@inheritDoc} */
    public function beforeRemove()
    {
        return parent::beforeRemove();
    }

    /** {@inheritDoc} */
    public function afterRemove()
    {
        return true;
    }

}

return 'msopModificationRemoveProcessor';