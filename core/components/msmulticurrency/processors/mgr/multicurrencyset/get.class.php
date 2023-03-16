<?php

class MultiCurrencySetGetProcessor extends modObjectGetProcessor
{
    public $classKey = 'MultiCurrencySet';
    public $languageTopics = array('msmulticurrency:default','msmulticurrency:multicurrencyset');
    /** @var MsMC $msmc  */
    public $msmc ;

    public function initialize()
{
    // $this->msmc = $this->modx->getService('msmulticurrency', 'MsMC');
    return parent::initialize();
}

}

return 'MultiCurrencySetGetProcessor';