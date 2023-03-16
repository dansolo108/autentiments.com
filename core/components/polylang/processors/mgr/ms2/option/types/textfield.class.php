<?php

class PolylangTextfieldType extends PolylangOptionType
{
    /**
     * @param $field
     *
     * @return string
     */
    public function getField($field)
    {
        return "{xtype:'polylang-field'}";
    }
}

return 'PolylangTextfieldType';