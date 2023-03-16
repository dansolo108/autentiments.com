<?php

class PolylangPolylangLanguageEnableProcessor extends modObjectUpdateProcessor
{
    public $languageTopics = array('polylang:default');
    public $classKey = 'PolylangLanguage';



    public function beforeSet()
    {
        $this->setProperty('active', 1);
        return true;
    }
}

return 'PolylangPolylangLanguageEnableProcessor';