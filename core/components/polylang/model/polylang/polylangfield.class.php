<?php

class PolylangField extends xPDOSimpleObject
{

    /**
     * @param string $prefix
     * @return string
     */
    public function getCacheKey($prefix = '')
    {
        return $prefix . 'field' . $this->get('class_name');
    }

}