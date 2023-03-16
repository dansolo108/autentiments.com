<?php
class MultiCurrencySetUpdateProcessor extends modObjectUpdateProcessor {
    public $classKey = 'MultiCurrencySet';
    public $languageTopics = array('msmulticurrency:default', 'msmulticurrency:multicurrencyset');
    /** @var MsMC $msmc  */
    public $msmc ;

    public function initialize()
    {
        // $this->msmc = $this->modx->getService('msmulticurrency', 'MsMC');
        return parent::initialize();
    }

    public function beforeSet() {
        //$this->setCheckbox('enable');
        return parent::beforeSet();
    }

}
return 'MultiCurrencySetUpdateProcessor';