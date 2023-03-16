<?php

class mspcActionGetListProcessor extends modObjectGetListProcessor
{
    public $objectType = 'mspcAction';
    public $classKey = 'mspcAction';
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
        $query = trim($this->getProperty('query'));
        if ($query) {
            $c->where(array(
                'name:LIKE' => "%{$query}%",
                'OR:description:LIKE' => "%{$query}%",
            ));
        }

        return $c;
    }

    public function prepareRow(xPDOObject $obj)
    {
        $array = $obj->toArray('', true);

        $array['coupons'] = $this->modx->getCount('mspcCoupon', array('action_id' => $array['id']));
        $array['activated'] = $this->modx->getCount('mspcCoupon', array(
            'action_id' => $array['id'],
            'count' => '0',
            'active' => false,
        ));

        $array['actions'] = array();

        if ($array['coupons'] > 0) {
            $array['actions'][] = array(
                'cls' => '',
                'icon' => 'icon icon-download',
                'title' => $this->modx->lexicon('mspromocode_action_download'),
                'multiple' => $this->modx->lexicon('mspromocode_action_download'),
                'action' => 'downloadCoupons',
                'button' => true,
                'menu' => true,
            );
        }

        // Edit
        $array['actions'][] = array(
            'cls' => '',
            'icon' => 'icon icon-edit',
            'title' => $this->modx->lexicon('mspromocode_action_update'),
            //'multiple' => $this->modx->lexicon('mspromocode_actions_update'),
            'action' => 'updateAction',
            'button' => true,
            'menu' => true,
        );

        if (!$array['active']) {
            $array['actions'][] = array(
                'cls' => '',
                'icon' => 'icon icon-toggle-on action-green',
                'title' => $this->modx->lexicon('mspromocode_action_enable'),
                'multiple' => $this->modx->lexicon('mspromocode_actions_enable'),
                'action' => 'enableAction',
                'button' => true,
                'menu' => true,
            );
        } else {
            $array['actions'][] = array(
                'cls' => '',
                'icon' => 'icon icon-toggle-off action-red',
                'title' => $this->modx->lexicon('mspromocode_action_disable'),
                'multiple' => $this->modx->lexicon('mspromocode_actions_disable'),
                'action' => 'disableAction',
                'button' => true,
                'menu' => true,
            );
        }

        // Remove
        $array['actions'][] = array(
            'cls' => '',
            'icon' => 'icon icon-trash-o action-red',
            'title' => $this->modx->lexicon('mspromocode_action_remove'),
            'multiple' => $this->modx->lexicon('mspromocode_actions_remove'),
            'action' => 'removeAction',
            'button' => true,
            'menu' => true,
        );

        return $array;
    }
}

return 'mspcActionGetListProcessor';
