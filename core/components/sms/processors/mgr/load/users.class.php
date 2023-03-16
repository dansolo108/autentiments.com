<?php

class smsGetListProcessor extends modObjectGetListProcessor
{
    public $objectType = 'sms';
    public $classKey = 'modUser';
    public $defaultSortField = 'Profile.fullname';
    public $defaultSortDirection = 'ASC';

    //public $permission = 'list';

    public function beforeQuery()
    {
        if (!$this->checkPermissions()) {
            return $this->modx->lexicon('access_denied');
        }

        return true;
    }

    public function prepareQueryBeforeCount(xPDOQuery $c)
    {
        $c->leftJoin('modUserProfile', 'Profile', 'Profile.internalKey = modUser.id');
        $c->select($this->modx->getSelectColumns('modUserProfile', 'Profile', ''));
        $c->select($this->modx->getSelectColumns('modUser', 'modUser', '', ['id']));
        extract(array_map('trim', $this->getProperties()));
        if ($id) {
            $c->where(array(
                'modUser.id' => $id,
            ));
        } else {
            if ($query) {
                $c->where(array(
                    'Profile.fullname:LIKE' => '%' . $query . '%',
                    'OR:Profile.email:LIKE' => '%' . $query . '%',
                    'OR:Profile.phone:LIKE' => '%' . $query . '%',
                ));
            }
            if ($filter) {
                $c->where(array(
                    'modUser.id:IN' => $this->getIds(),
                ));
            } else {
                $c->where(array(
                    'modUser.active' => 1,
                ));
            }
        }
        return $c;
    }

    public function prepareRow(xPDOObject $object)
    {
        $array = $object->toArray();
        $array['fullname'] = $array['fullname'] ? $array['fullname'] : $array['email'];
        return $array;
    }

    public function getIds()
    {
        $c = $this->modx->newQuery('extSms');
        $c->select('user_id');
        $c->groupby('user_id');
        if ($c->prepare() && $c->stmt->execute() && $ids = $c->stmt->fetchAll(PDO::FETCH_COLUMN)) {
            return $ids;
        }
        return [0];
    }

}

return 'smsGetListProcessor';