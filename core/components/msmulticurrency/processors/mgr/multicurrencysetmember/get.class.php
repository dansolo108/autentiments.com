<?php

class MultiCurrencySetMemberGetProcessor extends modObjectGetProcessor
{
    public $classKey = 'MultiCurrencySetMember';
    public $languageTopics = array('msmulticurrency:default','msmulticurrency:multicurrencysetmember');
    /** @var MsMC $msmc  */
    public $msmc ;

    public function initialize()
{
    // $this->msmc = $this->modx->getService('msmulticurrency', 'MsMC');
    return parent::initialize();
}

}

return 'MultiCurrencySetMemberGetProcessor';