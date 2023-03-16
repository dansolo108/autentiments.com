<?php

class PolylangPolylangTvTmplvarsCreateProcessor extends modObjectCreateProcessor
{
    public $classKey = 'PolylangTvTmplvars';
    public $languageTopics = array('polylang:default');
    /** @var Polylang $polylang */
    public $polylang;

    public function initialize()
    {
        // $this->polylang = $this->modx->getService('polylang', 'Polylang');
        return parent::initialize();
    }

    public function beforeSave()
    {
        $key = $this->getProperty('culture_key', '');
        $tvId = $this->getProperty('tmplvarid', 0);
        if ($this->modx->getCount($this->classKey, array('culture_key' => $key, 'tmplvarid' => $tvId))) {
            $this->addFieldError('culture_key', '');
            $this->addFieldError('tmplvarid', $this->modx->lexicon('polylang_tvtmplvars_err_tv_exists'));
        }
        return !$this->hasErrors();
    }

}

return 'PolylangPolylangTvTmplvarsCreateProcessor';