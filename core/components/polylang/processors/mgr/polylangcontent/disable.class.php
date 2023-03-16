<?php

class PolylangPolylangContentDisableProcessor extends modObjectUpdateProcessor
{
    public $languageTopics = array('polylang:default');
    public $classKey = 'PolylangContent';

    public function beforeSet()
    {
        $this->setProperty('active', 0);
        return true;
    }

}

return 'PolylangPolylangContentDisableProcessor';