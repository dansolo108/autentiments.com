<?php

class MultiCurrencyRemoveProcessor extends modObjectRemoveProcessor
{
    public $classKey = 'MultiCurrency';
    public $languageTopics = array('msmulticurrency:multicurrency');
    /** @var MsMC $msmc */
    public $msmc;

    public function initialize()
    {
        //$this->msmc = $this->modx->getService('msmulticurrency', 'MsMC');
        return parent::initialize();
    }
}

return 'MultiCurrencyRemoveProcessor';