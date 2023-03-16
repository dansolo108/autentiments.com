<?php

class MultiCurrencyCreateProcessor extends modObjectCreateProcessor
{
    public $classKey = 'MultiCurrency';
    public $languageTopics = array('msmulticurrency:multicurrency');
    /** @var MsMC $msmc */
    public $msmc;

    public function initialize()
    {
        $this->msmc = $this->modx->getService('msmulticurrency', 'MsMC');
        return parent::initialize();
    }

    public function beforeSet()
    {

        $code = trim(mb_strtoupper($this->getProperty('code')));
        if ($this->modx->getCount($this->classKey, array('code' => $code))) {
            $this->modx->error->addField('code', $this->modx->lexicon('msmulticurrency.err.code_ae'));
        }
        $this->setProperty('code', $code);

        return !$this->hasErrors();
    }

    public function afterSave()
    {
        if ($canSave = parent::afterSave()) {
            $this->msmc->clearCache();
        }

        return $canSave;
    }

}

return 'MultiCurrencyCreateProcessor';