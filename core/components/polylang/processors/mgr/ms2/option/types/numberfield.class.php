<?php

class PolylangNumberfieldType extends PolylangOptionType
{
    /**
     * @param $field
     *
     * @return string
     */
    public function getField($field)
    {
        return "{xtype:'numberfield'}";
    }
}

return 'PolylangNumberfieldType';