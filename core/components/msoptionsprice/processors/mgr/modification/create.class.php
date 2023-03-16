<?php

/**
 * Create an msopModification
 */
class msopModificationCreateProcessor extends modObjectCreateProcessor
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

        return parent::initialize();
    }

    /** {@inheritDoc} */
    public function beforeSet()
    {
        /** @var  $rid */
        /** @var  $type */
        foreach (array('rid', 'type') as $k) {
            ${$k} = trim($this->getProperty($k));
            if (empty(${$k})) {
                $this->modx->error->addField($k, $this->modx->lexicon('msoptionsprice_err_ae'));
            }
        }

        $options = $this->modx->call('msopModificationOption', 'getOptions', array(&$this->modx, 0, $rid));
        if (/*empty($options) OR */!empty($options) AND $this->msoptionsprice->getModificationByOptions($rid, $options, true)
        ) {
            return $this->msoptionsprice->lexicon('err_modify_exists');
        }

        $image = $this->getProperty('image');
        $strictImage = $this->msoptionsprice->getOption('search_modification_by_image_strict', null, false, true);
        if (!empty($image) AND $strictImage AND $this->modx->getCount($this->classKey, array('image' => $image))) {
            $this->modx->error->addField('image', $this->modx->lexicon('msoptionsprice_err_ae'));
        }

        return parent::beforeSet();
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
        $options = $this->modx->call('msopModificationOption', 'getOptions', array(&$this->modx, 0, $rid));
        $this->modx->call('msopModificationOption', 'saveOptions', array(&$this->modx, $mid, $rid, $options));
        $this->modx->call('msopModificationOption', 'removeOptions', array(&$this->modx, 0, $rid));

        return true;
    }

}

return 'msopModificationCreateProcessor';