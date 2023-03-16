<?php

/**
 * Create an msopModification
 */
class msopModificationDuplicateProcessor extends modObjectCreateProcessor
{
    /** @var xPDOObject|msopModification $object */
    public $object;
    public $objectType = 'msopModification';
    public $classKey = 'msopModification';
    public $languageTopics = array('msoptionsprice');
    public $permission = '';

    /** @var msoptionsprice $msoptionsprice */
    public $msoptionsprice;

    public function initialize()
    {
        $this->msoptionsprice = $this->modx->getService('msoptionsprice');
        $this->msoptionsprice->initialize($this->getProperty('context', $this->modx->context->key));

        /** @var msopModification $o */
        if (!$o = $this->modx->getObject($this->classKey, $this->getProperty('id'))) {
            return false;
        }

        $row = $o->toArray();
        $this->object = $this->modx->newObject($this->classKey);
        $this->object->fromArray(array_merge($row, array(
            'active'       => false,
        )));

        $this->object->set('sync_id', null);
        $this->object->set('sync_service', null);

        return true;
    }

    /** {@inheritDoc} */
    public function beforeSave()
    {
        $this->object->fromArray(array(
            'rank' => $this->modx->getCount($this->classKey)
        ));

        return parent::beforeSave();
    }

    /** {@inheritDoc} */
    public function afterSave()
    {
        $mid = $this->object->get('id');
        $rid = $this->object->get('rid');
        $options = $this->modx->call('msopModificationOption', 'getOptions',
            array(&$this->modx, $this->getProperty('id'), $rid));
        $this->modx->call('msopModificationOption', 'saveOptions', array(&$this->modx, $mid, $rid, $options));

        return true;
    }

}

return 'msopModificationDuplicateProcessor';