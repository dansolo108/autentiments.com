<?php

class MultiCurrencySetMemberRemoveProcessor extends modObjectRemoveProcessor
{
    public $classKey = 'MultiCurrencySetMember';
    public $languageTopics = array('msmulticurrency:default', 'msmulticurrency:multicurrencysetmember');
    /** @var MsMC $msmc */
    public $msmc;

    public function initialize()
    {
        $this->msmc = $this->modx->getService('msmulticurrency', 'MsMC');
        return parent::initialize();
    }

    public function beforeRemove()
    {
        if ($this->object->get('base')) {
            return $this->modx->lexicon('msmulticurrency.setmember.err_remove_base');
        }

        if ($this->object->get('selected')) {
            return $this->modx->lexicon('msmulticurrency.setmember.err_remove_selected');
        }
        return parent::beforeRemove();
    }

    public function afterRemove()
    {
        $rank = $this->object->get('rank');
        $sid = $this->object->get('sid');
        $sql = "UPDATE {$this->modx->getTableName($this->classKey)} SET `rank`=`rank`-1 WHERE sid= {$sid}  `rank`>{$rank}";

        $this->modx->exec($sql);
        $this->msmc->clearCache();

        return parent::afterRemove();
    }

}

return 'MultiCurrencySetMemberRemoveProcessor';