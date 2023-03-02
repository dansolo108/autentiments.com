<?php


class msopModificationTypesProcessor extends modObjectProcessor
{
    public $classKey = 'msopModificationOption';
    public $classField = 'msopModificationOption';
    public $languageTopics = array('msoptionsprice');
    public $permission = '';

    /** {@inheritDoc} */
    public function process()
    {
        $array = array(
            array(
                'id'          => 1,
                'name'        => $this->modx->lexicon('msoptionsprice_modification_name_type_1'),
                'description' => $this->modx->lexicon('msoptionsprice_modification_description_type_1'),
            ),
            array(
                'id'          => 2,
                'name'        => $this->modx->lexicon('msoptionsprice_modification_name_type_2'),
                'description' => $this->modx->lexicon('msoptionsprice_modification_description_type_2'),
            ),
            array(
                'id'          => 3,
                'name'        => $this->modx->lexicon('msoptionsprice_modification_name_type_3'),
                'description' => $this->modx->lexicon('msoptionsprice_modification_description_type_3'),
            ),
        );

        return $this->outputArray($array);
    }

    /** {@inheritDoc} */
    public function outputArray(array $array, $count = false)
    {
        if ($this->getProperty('addall')) {
            $array = array_merge_recursive(array(
                array(
                    'id'   => '-',
                    'name' => $this->modx->lexicon('msoptionsprice_all'),
                )
            ), $array);
        }

        return parent::outputArray($array, $count);
    }

}

return 'msopModificationTypesProcessor';