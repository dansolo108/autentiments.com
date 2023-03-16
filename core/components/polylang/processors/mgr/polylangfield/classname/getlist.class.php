<?php

class PolylangPolylangFieldClassNameGetListProcessor extends modProcessor
{
    public $languageTopics = array('polylang:default');
    public $checkListPermission = true;
    /** @var Polylang $polylang */
    public $polylang;

    public function initialize()
    {
        $this->polylang = $this->modx->getService('polylang', 'Polylang');
        return parent::initialize();
    }

    public function process()
    {
        $result = array();
        $list = $this->modx->getOption('polylang_content_classes');
        $list = $this->polylang->getTools()->fromJSON($list, array());
        if ($list) {
            foreach ($list as $key => $class) {
                array_push($result, array(
                    'key' => $key,
                    'name' => $this->modx->lexicon('polylang_field_class_name_' . strtolower($key)),
                ));
            }
        }
        return $this->outputArray($result, count($result));
    }

}

return 'PolylangPolylangFieldClassNameGetListProcessor';