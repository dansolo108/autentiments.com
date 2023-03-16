<?php

class MultiCurrencySetMemberUpdateProcessor extends modObjectUpdateProcessor
{
    public $classKey = 'MultiCurrencySetMember';
    public $languageTopics = array('msmulticurrency:default', 'msmulticurrency:multicurrencysetmember');
    /** @var MsMC $msmc */
    public $msmc;
    public $oldCourse;
    public $oldRate;

    public function initialize()
    {
        $this->msmc = $this->modx->getService('msmulticurrency', 'MsMC');
        return parent::initialize();
    }

    public function beforeSet()
    {
        $this->setCheckbox('base');
        $this->setCheckbox('auto');
        $this->setCheckbox('enable');
        $this->setCheckbox('selected');
        $base = $this->getProperty('base');

        if ($this->object->get('base') && empty($base)) {
            $this->modx->error->addField('base', $this->modx->lexicon('msmulticurrency.setmember.err_unset_base'));
        }

        if ($base) {
            $this->setProperty('enable', 1);
            $this->setProperty('course', 1);
            $this->setProperty('rate', 1);
            $this->setProperty('val', 1);
        }
        $this->oldCourse = $this->object->get('course');
        $this->oldRate = $this->object->get('rate');
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
            $sql = "UPDATE {$table} SET base = 0 WHERE  sid = {$sid} AND cid !=" . $cid;
            $this->modx->exec($sql);
        }

        if (
            $this->oldCourse != $this->object->get('course') ||
            $this->oldRate != $this->object->get('rate')
        ) {
            $this->object->calculateVal();
            $this->msmc->updateProductsPrice($cid, $sid);
            $this->msmc->updateProductsOptionsPrice($cid, $sid);
            $this->msmc->clearAllCache();
        }

        $this->msmc->clearCache();

        return $canSave;
    }


}

return 'MultiCurrencySetMemberUpdateProcessor';