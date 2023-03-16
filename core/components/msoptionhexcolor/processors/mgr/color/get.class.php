<?php

class msHexColorGetProcessor extends modObjectGetProcessor
{
    /** @var msHexColor $object */
    public $object;
    public $classKey = 'msHexColor';
    public $languageTopics = array('minishop2');
    public $permission = 'mssetting_view';


    /**
     * @return bool|null|string
     */
    public function initialize()
    {
        if (!$this->modx->hasPermission($this->permission)) {
            return $this->modx->lexicon('access_denied');
        }

        return parent::initialize();
    }

}

return 'msHexColorGetProcessor';