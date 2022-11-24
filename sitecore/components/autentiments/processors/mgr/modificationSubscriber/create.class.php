<?php

require_once MODX_CORE_PATH . 'model/modx/modprocessor.class.php';
class ModificationSubscriberCreateProcessor extends modObjectCreateProcessor
{
    public $classKey = 'ModificationSubscriber';
    public $languageTopics = array('resource','autentiments:default');
    public $permission = 'ModificationSubscriber_save';
    public $beforeSaveEvent = 'OnBeforeModificationSubscriberSave';
    public $afterSaveEvent = 'OnModificationSubscriberSave';
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

return 'ModificationSubscriberCreateProcessor';
