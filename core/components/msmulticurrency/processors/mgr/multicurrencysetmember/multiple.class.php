<?php

class MultiCurrencySetMemberMultipleProcessor extends modProcessor
{

    /** @var MsMC $msmc  */
    public $msmc ;

    public function initialize()
{
    $this->msmc = $this->modx->getService('msmulticurrency', 'MsMC');
    return parent::initialize();
}

    /**
     * @return array|string
     */
    public function process()
    {

        if (!$method = $this->getProperty('method', false)) {
            return $this->failure();
        }
        $ids = json_decode($this->getProperty('ids'), true);
        if (empty($ids)) {
            return $this->success();
        }

        foreach ($ids as $id) {
            $this->modx->error->reset();
            /** @var modProcessorResponse $response */
            $response = $this->modx->runProcessor(
                'mgr/multicurrencysetmember/' . $method,
                array('id' => $id),
                array('processors_path' => $this->msmc->config['processorsPath'])
            );
            if ($response->isError()) {
                return $response->getResponse();
            }
        }

        return $this->success();
    }

}

return 'MultiCurrencySetMemberMultipleProcessor';