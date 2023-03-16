<?php
require_once MODX_CORE_PATH . 'model/modx/modprocessor.class.php';
class ModificationRemainCreateProcessor extends modObjectCreateProcessor
{
    public $classKey = 'ModificationRemain';
    public $languageTopics = array('resource','autentiments:default');
    public $permission = 'ModificationRemain_save';
    public $beforeSaveEvent = 'OnBeforeModificationRemainSave';
    public $afterSaveEvent = 'OnModificationRemainSave';
    /** @var ModificationSubscriber $object */
    public $object;
}

return 'ModificationRemainCreateProcessor';
