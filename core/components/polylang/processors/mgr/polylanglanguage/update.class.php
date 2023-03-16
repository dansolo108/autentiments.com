<?php

class PolylangPolylangLanguageUpdateProcessor extends modObjectUpdateProcessor
{
    public $classKey = 'PolylangLanguage';
    public $languageTopics = array('polylang:default');
    public $beforeSaveEvent = 'OnBeforeSavePolylangLanguage';
    public $afterSaveEvent = 'OnSavePolylangLanguage';
    /** @var Polylang $polylang */
    public $polylang;
    protected $oldCultureKey;

    public function initialize()
    {
        // $this->polylang = $this->modx->getService('polylang', 'Polylang');
        return parent::initialize();
    }

    public function beforeSave()
    {
        $id = $this->object->get('id');
        $key = $this->getProperty('culture_key', '');
        $this->oldCultureKey = $this->object->get('culture_key');
        if ($this->modx->getCount($this->classKey, array('culture_key' => $key, 'id:!=' => $id))) {
            $this->addFieldError('culture_key', $this->modx->lexicon('polylang_err_culture_key_exists'));
        }
        return !$this->hasErrors();
    }

    public function afterSave()
    {
        $key = $this->object->get('culture_key');
        if ($this->oldCultureKey != $key) {
            $list = array('PolylangResource', 'PolylangProduct', 'PolylangTv');
            foreach ($list as $className) {
                $q = $this->modx->newQuery($className);
                $q->command('UPDATE');
                $q->query['set']['culture_key'] = array(
                    'value' => $key,
                    'type' => true,
                );
                $q->where(array('culture_key' => $this->oldCultureKey));
                if ($q->prepare()) {
                    if (!$q->stmt->execute()) {
                        $this->modx->log(modX::LOG_LEVEL_ERROR, print_r($q->stmt->errorInfo(), true) . ' SQL: ' . $q->toSQL());
                    }
                }
            }
        }

        return true;
    }


}

return 'PolylangPolylangLanguageUpdateProcessor';