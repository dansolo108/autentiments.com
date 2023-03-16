<?php

class smsGetListProcessor extends modObjectGetListProcessor
{
    public $objectType = 'sms';
    public $classKey = 'extSms';
    public $defaultSortField = 'service';
    public $defaultSortDirection = 'ASC';
    public $permission = '';

    public function prepareQueryBeforeCount(xPDOQuery $c)
    {
        extract(array_map('trim', $this->getProperties()));
        if ($id) {
            $c->where(array(
                'service' => $id,
            ));
        } else {
            if ($query) {
                $c->where(array(
                    'service:LIKE' => '%' . $query . '%',
                ));
            }
            $c->groupby('service');
        }
        return $c;
    }

}

return 'smsGetListProcessor';