<?php

class extSms extends xPDOSimpleObject
{
    public function save($cacheFlag = null)
    {
        if (!$this->get('id')) {
            $this->set('createdon', date('Y-m-d H:i:s'));
        }
        return parent::save($cacheFlag);
    }
}