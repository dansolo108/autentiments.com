<?php

class smsGetListProcessor extends modObjectGetListProcessor
{
    public $objectType = 'sms';
    public $classKey = 'extSms';
    public $defaultSortField = 'createdon';
    public $defaultSortDirection = 'DESC';
    public $permission = 'sms_view';

    public function beforeQuery()
    {
        if (!$this->checkPermissions()) {
            return $this->modx->lexicon('access_denied');
        }

        return true;
    }

    public function prepareQueryBeforeCount(xPDOQuery $c)
    {
        $c->leftJoin('modUserProfile', 'modUserProfile', 'modUserProfile.internalKey = extSms.user_id');
        $c->select($this->modx->getSelectColumns('extSms', 'extSms', ''));
        $c->select($this->modx->getSelectColumns('modUserProfile', 'modUserProfile', 'user_', ['id', 'internalKey'], true));
        extract(array_map('trim', $this->getProperties()));
        if ($user_id) {
            $c->where([
                'extSms.user_id' => $user_id,
            ]);
        }
        if ($service) {
            $c->where([
                'extSms.service' => $service,
            ]);
        }
        if ($query) {
            $c->where([
                'extSms.phone:LIKE' => '%' . $query . '%',
                'OR:extSms.message:LIKE' => '%' . $query . '%',
                'OR:extSms.ip:LIKE' => '%' . $query . '%',
            ]);
        }
        return $c;
    }

    public function prepareRow(xPDOObject $object)
    {
        $array = $object->toArray();
        $array['properties'] = $array['properties'] ? json_encode($array['properties']) : '';
        $array['actions'] = array();
        $array['actions'][] = array(
            'cls' => '',
            'icon' => 'icon icon-edit',
            'title' => $this->modx->lexicon('sms_menu_view'),
            'action' => 'viewData',
            'button' => true,
            'menu' => true,
        );

        return $array;
    }

}

return 'smsGetListProcessor';