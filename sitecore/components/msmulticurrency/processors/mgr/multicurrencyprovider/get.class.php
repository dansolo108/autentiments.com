<?php

class MultiCurrencyProviderGetProcessor extends modObjectGetProcessor
{
    public $classKey = 'MultiCurrencyProvider';
    public $languageTopics = array('msmulticurrency:multicurrency');

    /**
     * Return the response
     * @return array
     */
    public function cleanup()
    {
        $data = $this->object->toArray();
        if (empty($data['properties'])) {
            $data['properties'] = '';
        } else {
            $data['properties'] = $this->modx->toJSON($data['properties']);
        }
        return $this->success('', $data);
    }

}

return 'MultiCurrencyProviderGetProcessor';