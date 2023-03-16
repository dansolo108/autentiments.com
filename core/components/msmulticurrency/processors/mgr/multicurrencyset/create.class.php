<?php

class MultiCurrencySetCreateProcessor extends modObjectCreateProcessor
{
    public $classKey = 'MultiCurrencySet';
    public $languageTopics = array('msmulticurrency:default', 'msmulticurrency:multicurrencyset');
    /** @var MsMC $msmc */
    public $msmc;

    public function initialize()
    {
        $this->msmc = $this->modx->getService('msmulticurrency', 'MsMC');
        return parent::initialize();
    }

    public function afterSave()
    {
        if ($ok = parent::afterSave()) {
            $currencies = $this->getProperty('currency', array());
            if ($currencies) {
                $params = array(
                    'sid' => $this->object->get('id'),
                );

                $rank = 0;
                foreach ($currencies as $currency => $val) {
                    $params['cid'] = $currency;
                    $params['rank'] = $rank;
                    $rank++;
                    $this->modx->error->reset();
                    $response = $this->modx->runProcessor(
                        'mgr/multicurrencysetmember/create',
                        $params,
                        array('processors_path' => $this->msmc->config['processorsPath'])
                    );
                    if ($response->isError()) {
                        $this->modx->log(modX::LOG_LEVEL_ERROR, print_r($response->getResponse(), 1));
                    }
                }
            }

        }
        return $ok;
    }


}

return 'MultiCurrencySetCreateProcessor';