<?php

class PolylangPolylangFieldMultipleProcessor extends modProcessor
{

    /** @var Polylang $polylang  */
    public $polylang ;
    public $languageTopics = array('polylang:default');

    public function initialize()
    {
        $this->polylang = $this->modx->getService('polylang', 'Polylang');
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
                'mgr/polylangfield/' . $method,
                array('id' => $id),
                array('processors_path' => $this->polylang->config['processorsPath'])
            );
            if ($response->isError()) {
                return $response->getResponse();
            }
        }

        return $this->success();
    }

}

return 'PolylangPolylangFieldMultipleProcessor';