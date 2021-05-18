<?php

class PolylangPolylangContentEnableProcessor extends modObjectUpdateProcessor
{
    public $languageTopics = array('polylang:default');
    public $classKey = 'PolylangContent';

    public function beforeSet()
    {
        $this->setProperty('active', 1);
        return true;
    }
}

return 'PolylangPolylangContentEnableProcessor';