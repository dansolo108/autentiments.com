<?php

class PolylangPolylangFieldUpdateProcessor extends modObjectUpdateProcessor
{
    public $classKey = 'PolylangField';
    public $languageTopics = array('polylang:default');
    /** @var Polylang $polylang */
    public $polylang;
    /** @var string $oldName */
    protected $oldName;

    public function initialize()
    {
        $this->polylang = $this->modx->getService('polylang', 'Polylang');
        return parent::initialize();
    }

    public function beforeSet()
    {
        $this->oldName = $this->object->get('name');
        return parent::beforeSet();
    }


    public function beforeSave()
    {
        $canSave = parent::beforeSave();
        if ($canSave === true) {
            if ($this->oldName != $this->object->get('name')) {
                /*$tools = $this->polylang->getTools();
                if (!$tools->getDbHelper()->renameField($this->object->get('class_name'), $this->oldName, $this->object->get('name'))) {
                    return false;
                }*/
            }
        }
        return $canSave;
    }

    public function afterSave()
    {
        $cacheKey = $this->polylang->getTools()->getCacheKey($this->object->getCacheKey());
        $this->modx->cacheManager->delete($cacheKey);
        $this->modx->cacheManager->refresh(array('resource' => array()));
        return parent::afterSave();
    }

}

return 'PolylangPolylangFieldUpdateProcessor';