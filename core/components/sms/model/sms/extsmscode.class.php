<?php

class extSmsCode extends xPDOSimpleObject
{
    public function save($cacheFlag = null)
    {
        if (!$this->get('id')) {
            $this->fromArray(array(
                'createdon' => date('Y-m-d H:i:s'),
                'ip' => $_SERVER['REMOTE_ADDR'],
                'client' => $_SERVER['HTTP_USER_AGENT'],
                'used' => 0,
                'attempt' => 0,
            ));
        }
        return parent::save($cacheFlag);
    }
}