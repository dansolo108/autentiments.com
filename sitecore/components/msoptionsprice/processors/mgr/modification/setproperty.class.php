<?php
require_once dirname(__FILE__) . '/update.class.php';

/**
 * SetProperty a msopModification
 */
class msopModificationSetPropertyProcessor extends msopModificationUpdateProcessor
{
    /** @var msopModification $object */
    public $object;
    public $objectType = 'msopModification';
    public $classKey = 'msopModification';
    public $languageTopics = array('modclassvar');
    public $permission = '';

    /** {@inheritDoc} */
    public function beforeSet()
    {
        $fieldName = $this->getProperty('field_name', null);
        $fieldValue = $this->getProperty('field_value', null);

        $this->properties = array();
        if (!is_null($fieldName) AND !is_null($fieldValue)) {
            $this->setProperty($fieldName, $fieldValue);
        }

        return parent::beforeSet();
    }

}

return 'msopModificationSetPropertyProcessor';