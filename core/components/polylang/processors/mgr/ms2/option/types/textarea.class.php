<?php
class PolylangTextareaType extends PolylangOptionType
{
    /**
     * @param $field
     *
     * @return string
     */
    public function getField($field)
    {
        return "{xtype:'textarea'}";
    }
}

return 'PolylangTextareaType';