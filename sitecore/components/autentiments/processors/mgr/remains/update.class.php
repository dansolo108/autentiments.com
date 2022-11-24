<?php
require_once MODX_CORE_PATH . 'model/modx/modprocessor.class.php';
class ModificationRemainUpdateProcessor extends modObjectUpdateProcessor
{
    public $classKey = 'ModificationRemain';
    public $languageTopics = array('resource','autentiments:default');
    public $permission = 'ModificationRemain_save';
    public $beforeSaveEvent = 'OnBeforeModificationRemainSave';
    public $afterSaveEvent = 'OnModificationRemainSave';
    /** @var ModificationRemain $object */
    public $object;
    public $beforeRemains;

    /**
     * @return mixed
     */
    public function beforeSet(){
        $this->beforeRemains = $this->object->get('remains');
        return parent::beforeSet();
    }


    /**
     * @return mixed
     */
    public function afterSave(){
        if($this->beforeRemains !== $this->object->get('remains')){
            $this->modx->invokeEvent("OnModificationRemainsUpdate",array(
                $this->primaryKeyField => $this->object->get($this->primaryKeyField),
                $this->objectType => &$this->object,
                'object' => &$this->object,
            ));
        }
        return parent::afterSave();
    }

}

return 'ModificationRemainUpdateProcessor';
