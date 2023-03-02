<?php

/**
 * Update an msopModification
 */
class msopModificationUpdateProcessor extends modObjectUpdateProcessor
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
    public function beforeSave()
    {
        if (!$this->checkPermissions()) {
            return $this->modx->lexicon('access_denied');
        }

        return true;
    }

    /** {@inheritDoc} */
    public function beforeSet()
    {
        $this->setProperties(array_merge($this->object->toArray(), $this->getProperties()));
        /** @var  $id */
        /** @var  $rid */
        /** @var  $type */
        foreach (array('id', 'rid', 'type') as $k) {
            ${$k} = trim($this->getProperty($k));
            if (empty(${$k})) {
                $this->modx->error->addField($k, $this->modx->lexicon('msoptionsprice_err_ae'));
            }
        }

        $options = $this->modx->call('msopModificationOption', 'getOptions', array(&$this->modx, $id, $rid));
        if (/*empty($options) OR */!empty($options) AND $this->msoptionsprice->getModificationByOptions($rid, $options, true, array(0, $id))) {
            return $this->msoptionsprice->lexicon('err_modify_exists');
        }

        $image = $this->getProperty('image');
        if (
            $firstImage = $this->object->getFirstImage()
            AND
            $image != $firstImage
        ) {
            $image = $firstImage;
        }
        
        $strictImage = $this->msoptionsprice->getOption('search_modification_by_image_strict', null, false, true);
        if (!empty($image) AND $strictImage AND $this->modx->getCount($this->classKey,
                array('id:!=' => $id, 'image' => $image))
        ) {
            $this->modx->error->addField('image', $this->modx->lexicon('msoptionsprice_err_ae'));
        }


        return parent::beforeSet();
    }

    /** {@inheritDoc} */
    public function afterSave()
    {
        return true;
    }

}

return 'msopModificationUpdateProcessor';
