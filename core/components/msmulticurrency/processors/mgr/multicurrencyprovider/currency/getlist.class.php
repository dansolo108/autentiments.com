<?php

class MultiCurrencyProviderCurrencyGetListProcessor extends modProcessor
{
    public $languageTopics = array('msmulticurrency:multicurrency');
    /** @var MsMC $msmc */
    public $msmc;

    public function initialize()
    {
        $this->msmc = $this->modx->getService('msmulticurrency', 'MsMC');
        foreach ($this->languageTopics as $topic) {
            $this->modx->lexicon->load($topic);
        }
        return parent::initialize();
    }

    /**
     * {@inheritDoc}
     * @return mixed
     */
    public function process()
    {
        $list = array();
        $query = $this->getProperty('query', '');
        $limit = intval($this->getProperty('limit'));
        $start = intval($this->getProperty('start'));
        $providerId = $this->getProperty('provider', 0);
        if ($provider = $this->msmc->getProviderInstance($providerId)) {
            if ($items = $provider->getCourse()) {
                foreach ($items as $key => $val) {
                    if (!empty($query) && !preg_match('/' . $query . '/imu', $key)) continue;
                    $list[] = array('code' => $key, 'course' => $val);
                }
            }
        }
        return $this->outputArray(array_slice($list, $start, $limit), count($list));
    }

}

return 'MultiCurrencyProviderCurrencyGetListProcessor';