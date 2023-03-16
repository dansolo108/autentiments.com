<?php

class MultiCurrencySetRemoveProcessor extends modObjectRemoveProcessor
{
    public $classKey = 'MultiCurrencySet';
    public $languageTopics = array('msmulticurrency:default', 'msmulticurrency:multicurrencyset');
    /** @var MsMC $msmc */
    public $msmc;

    public function initialize()
    {
        $this->msmc = $this->modx->getService('msmulticurrency', 'MsMC');
        $baseSetId = $this->modx->getOption('msmulticurrency.base_currency_set', null, 1, true);
        if($this->getProperty('id') == $baseSetId)  {
           return $this->modx->lexicon('msmulticurrency.set.err_remove_base_set');
        }
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

return 'MultiCurrencySetRemoveProcessor';