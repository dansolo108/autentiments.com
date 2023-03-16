<?php

class msopBaseGetListProcessor extends modObjectGetListProcessor
{

    /** @var string $prepareObjectEvent The name of the event to fire on prepare data */
    public $prepareObjectEvent = 'msopOnManagerPrepareObjectData';
    public $prepareObjectType = 'getlist';

    /** @var msoptionsprice $msoptionsprice */
    public $msoptionsprice;

    public function initialize()
    {
        $this->msoptionsprice = $this->modx->getService('msoptionsprice');
        $this->msoptionsprice->initialize($this->getProperty('context', $this->modx->context->key));

        return parent::initialize();
    }

    public function prepareObjectData(array $data)
    {
        if (!empty($this->prepareObjectEvent)) {
            $response = $this->msoptionsprice->invokeEvent($this->prepareObjectEvent, array(
                'type' => $this->prepareObjectType,
                'data' => $data,
                'self' => $this,
            ));

            if (!$response['success']) {
                return $this->failure($response['message']);
            }
            if (isset($response['data']['data'])) {
                $data = $response['data']['data'];
            }
        }

        return $data;
    }

}

return 'msopBaseGetListProcessor';