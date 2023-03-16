<?php

class PolylangPolylangFieldRemoveProcessor extends modObjectRemoveProcessor
{
    public $classKey = 'PolylangField';
    public $languageTopics = array('polylang:default');
    /** @var Polylang $polylang */
    public $polylang;

    public function initialize()
    {
        $this->polylang = $this->modx->getService('polylang', 'Polylang');
        return parent::initialize();
    }

    public function afterRemove()
    {
        $sql = "UPDATE {$this->modx->getTableName($this->classKey)} SET `rank`=`rank`-1 WHERE `rank`>{$this->object->get('rank')} AND  class_name = {$this->object->get('class_name')}";
        $this->modx->exec($sql);
        $cacheKey = $this->polylang->getTools()->getCacheKey($this->object->getCacheKey());
        $this->modx->cacheManager->delete($cacheKey);
        return true;
    }

}

return 'PolylangPolylangFieldRemoveProcessor';