<?php

class mspcConditionTypesGetListProcessor extends modProcessor
{
    public $types = array();

    public function getLanguageTopics()
    {
        return array('mspromocode:default');
    }

    public function initialize()
    {
        $this->types = array(
            'from_total_cost',
            'to_total_cost',
            'from_total_count',
            'to_total_count',
            'first_buy',
            'no_discount_products',
            // 'user_group',
        );

        return parent::initialize();
    }

    public function process()
    {
        $array = array();
        $exclude = $this->getProperty('exclude', '[]');
        $exclude = empty($exclude)
            ? array()
            : $this->modx->fromJSON($exclude);

        if ($this->getProperty('filter')) {
            $array[] = array(
                'value' => '',
                'display' => $this->modx->lexicon('mspromocode_combo_filter_all'),
            );
        }

        foreach (array_diff($this->types, $exclude) as $type) {
            $array[] = array(
                'value' => $type,
                'display' => $this->modx->lexicon('mspromocode_combo_condition_'.$type),
            );
        }
        // $array[] = array(
        //     'value' => 'user_group',
        //     'display' => $this->modx->lexicon('mspromocode_combo_condition_user_group'),
        // );

        return $this->outputArray($array);
    }
}

return 'mspcConditionTypesGetListProcessor';
