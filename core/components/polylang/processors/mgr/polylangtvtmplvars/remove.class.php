<?php
class PolylangPolylangTvTmplvarsRemoveProcessor extends modObjectRemoveProcessor {
    public $classKey = 'PolylangTvTmplvars';
    public $languageTopics = array('polylang:default');
    /** @var Polylang $polylang  */
    public $polylang ;

    public function initialize()
    {
        // $this->polylang = $this->modx->getService('polylang', 'Polylang');
        return parent::initialize();
    }

    public function afterRemove()
    {

        /*$sql = "UPDATE {$this->modx->getTableName($this->classKey)} SET `rank`=`rank`-1 WHERE `rank`>{$this->object->get('rank')}
        // AND  parent_id = {$this->object->get('parent_id')
        ";
        $this->modx->exec($sql);*/

        return parent::afterRemove();
    }

}
return 'PolylangPolylangTvTmplvarsRemoveProcessor';