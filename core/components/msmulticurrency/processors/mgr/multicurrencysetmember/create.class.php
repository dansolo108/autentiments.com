<?php

class MultiCurrencySetMemberCreateProcessor extends modObjectCreateProcessor
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

    public function beforeSet()
    {

        $sid = $this->getProperty('sid');
        $cid = $this->getProperty('cid');

        $this->setCheckbox('base');
        $this->setCheckbox('auto');
        $this->setCheckbox('enable');
        $this->setCheckbox('selected');
        $base = $this->getProperty('base');
        $rank = $this->modx->getCount($this->classKey, array('sid' => $sid)) + 1;

        if ($this->modx->getCount($this->classKey, array('sid' => $sid, 'cid' => $cid))) {
            $this->modx->error->addField('cid', $this->modx->lexicon('msmulticurrency.setmember.err_cid_ae'));
        }

        if ($base) {
            $this->setProperty('enable', 1);
            $this->setProperty('course', 1);
            $this->setProperty('rate', 1);
            $this->setProperty('val', 1);
        }

        $this->setProperty('rank', $rank);

        return !$this->hasErrors();
    }

    public function afterSave()
    {
        $base = $this->getProperty('base');
        $sid = $this->object->get('sid');
        $cid = $this->object->get('cid');
        $selected = $this->object->get('selected', 0);

        if ($selected) {
            $this->msmc->setOption('msmulticurrency.selected_currency_default', $cid);
            $table = $this->modx->getTableName($this->classKey);
            $sql = "UPDATE {$table} SET selected = 0 WHERE cid !=" . $cid;
            $this->modx->exec($sql);
        }

        if ($canSave = parent::afterSave() && $base) {

            if ($this->msmc->isBaseSet($sid)) {
                $this->msmc->setOption('msmulticurrency.base_currency', $cid);
            }

            $table = $this->modx->getTableName($this->classKey);
            $sql = "UPDATE {$table} SET base = 0 WHERE sid = {$sid} AND cid !=" . $cid;
            $this->modx->exec($sql);
        }

        $this->object->calculateVal();
        $this->msmc->clearCache();

        return $canSave;
    }

}

return 'MultiCurrencySetMemberCreateProcessor';