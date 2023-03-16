<?php

class  MultiCurrencySetMemberUpdateCourseProcessor extends modProcessor
{
    /** @var MsMC $msmc */
    public $msmc;
    /** @var MsMCProvider $provider */
    public $provider;
    public $languageTopics = array('msmulticurrency:default','msmulticurrency:multicurrencysetmember');

    public function initialize()
    {
        $this->msmc = $this->modx->getService('msmulticurrency', 'MsMC');
        foreach ($this->languageTopics as $topic) {
            $this->modx->lexicon->load($topic);
        }
        $this->provider = $this->msmc->getProviderInstance();
        return parent::initialize();
    }

    /**
     * {@inheritDoc}
     * @return mixed
     */
    public function process()
    {
        if ($this->provider) {
            $this->provider->run();
            $this->msmc->updateProductsPrice();
            $this->msmc->updateProductsOptionsPrice();
            $this->msmc->clearAllCache();
            return $this->success($this->modx->lexicon('msmulticurrency.setmember.success_update_course'));
        } else {
            return $this->failure($this->modx->lexicon('msmulticurrency.setmember.err_not_set_provider'));
        }

    }

}

return 'MultiCurrencySetMemberUpdateCourseProcessor';