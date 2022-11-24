<?php
require_once MODX_CORE_PATH . 'model/modx/modprocessor.class.php';
class ModificationRemainUpdateProcessor extends modObjectUpdateProcessor
{
    public $classKey = 'ModificationRemain';
    public $languageTopics = array('resource','autentiments:default');
    public $permission = 'ModificationRemain_save';
    public $beforeSaveEvent = 'OnBeforeModificationRemainSave';
    public $afterSaveEvent = 'OnModificationRemainSave';
    /** @var ModificationSubscriber $object */
    public $object;


    /**
     * @return mixed
     */
    public function beforeSave()
    {
        return parent::beforeSave();
    }


    /**
     * @return mixed
     */
    public function afterSave()
    {
        return parent::afterSave();
    }

}

return 'ModificationRemainUpdateProcessor';
