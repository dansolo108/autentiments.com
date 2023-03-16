<?php

class mspcConditionGetListProcessor extends modObjectGetListProcessor
{
    public $objectType = 'mspcCondition';
    public $classKey = 'mspcCondition';
    public $defaultSortField = 'id';
    public $defaultSortDirection = 'DESC';
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
        $owner = $this->getProperty('owner', 'coupon');

        $owner_id = (int) $this->getProperty('owner_id', 0);
        if (!empty($owner_id)) {
            $c->where(array("{$this->classKey}.{$owner}_id" => $owner_id));
        }

        $type = $this->getProperty('type', false);
        if (!empty($type)) {
            $c->where(array("{$this->classKey}.type" => $type));
        }

        $c->select($this->modx->getSelectColumns($this->classKey, $this->classKey));

        return $c;
    }

    public function prepareRow(xPDOObject $object)
    {
        $array = $object->toArray('', true);
        // $this->modx->log(MODX::LOG_LEVEL_ERROR, print_r($array,1));

        $array['actions'] = array();

        // Удалить
        $array['actions'][] = array(
            'cls' => '',
            'icon' => 'icon icon-times action-red',
            'title' => $this->modx->lexicon('mspromocode_consition_remove'),
            // 'multiple' => $this->modx->lexicon('mspromocode_consitions_remove'),
            'action' => 'removeCondition',
            'button' => true,
            'menu' => true,
        );

        return $array;
    }
}

return 'mspcConditionGetListProcessor';
