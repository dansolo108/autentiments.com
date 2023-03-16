<?php

class PolylangPolylangFieldInputTypesGetListProcessor extends modProcessor
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
        $list = $this->modx->getOption('polylang_input_types');
        $list = $this->polylang->getTools()->explodeAndClean($list);
        if ($list) {
            foreach ($list as $val) {
                $name = str_replace(array('polylang-', '-'), array('', '_'), $val);
                $name = $this->modx->lexicon('polylang_field_input_type_' . strtolower($name));
                array_push($result, array(
                    'key' => $val,
                    'name' => $name,
                ));
            }
        }
        return $this->outputArray($result, count($result));
    }

}

return 'PolylangPolylangFieldInputTypesGetListProcessor';