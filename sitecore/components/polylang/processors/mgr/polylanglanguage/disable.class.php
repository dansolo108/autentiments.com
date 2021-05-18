<?php

class PolylangPolylangLanguageDisableProcessor extends modObjectUpdateProcessor
{
    public $languageTopics = array('polylang:default');
    public $classKey = 'PolylangLanguage';

    public function beforeSet()
    {
        $this->setProperty('active', 0);
        return true;
    }

}

return 'PolylangPolylangLanguageDisableProcessor';