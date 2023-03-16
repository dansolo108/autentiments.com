<?php

class PolylangPolylangLanguageCreateProcessor extends modObjectCreateProcessor
{
    public $classKey = 'PolylangLanguage';
    public $languageTopics = array('polylang:default');
    public $beforeSaveEvent = 'OnBeforeSavePolylangLanguage';
    public $afterSaveEvent = 'OnSavePolylangLanguage';
    /** @var Polylang $polylang */
    public $polylang;

    public function initialize()
    {
        // $this->polylang = $this->modx->getService('polylang', 'Polylang');
        return parent::initialize();
    }

    public function beforeSet()
    {
        $rank = $this->getProperty('rank', '');
        if (empty($rank)) {
            $rank = $this->modx->getCount($this->classKey) + 1;
            $this->setProperty('rank', $rank);
        }
        return true;
    }

    public function beforeSave()
    {
        $key = $this->getProperty('culture_key', '');
        if ($this->modx->getCount($this->classKey, array('culture_key' => $key))) {
            $this->addFieldError('culture_key', $this->modx->lexicon('polylang_err_culture_key_exists'));
        }
        return !$this->hasErrors();
    }
}

return 'PolylangPolylangLanguageCreateProcessor';