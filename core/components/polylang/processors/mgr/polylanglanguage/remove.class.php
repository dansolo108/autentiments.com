<?php

class PolylangPolylangLanguageRemoveProcessor extends modObjectRemoveProcessor
{
    public $classKey = 'PolylangLanguage';
    public $languageTopics = array('polylang:default');
    public $beforeRemoveEvent = 'OnBeforeRemovePolylangLanguage';
    public $afterRemoveEvent = 'OnRemovePolylangLanguage';
    /** @var Polylang $polylang */
    public $polylang;

    public function initialize()
    {
        // $this->polylang = $this->modx->getService('polylang', 'Polylang');
        return parent::initialize();
    }

    public function afterRemove()
    {

        $sql = "UPDATE {$this->modx->getTableName($this->classKey)} SET `rank`=`rank`-1 WHERE `rank`>{$this->object->get('rank')}";
        $this->modx->exec($sql);
        return parent::afterRemove();
    }

}

return 'PolylangPolylangLanguageRemoveProcessor';