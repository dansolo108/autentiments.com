<?php

class mspcCouponGetListProcessor extends modObjectGetListProcessor
{
    public $objectType = 'mspcCoupon';
    public $classKey = 'mspcCoupon';
    public $defaultSortField = 'id';
    public $defaultSortDirection = 'DESC';

    //public $permission = 'list';

    public function beforeQuery()
    {
        if (!$this->checkPermissions()) {
            return $this->modx->lexicon('access_denied');
        }

        // Обрабатываем ключ сортировки
        $sort = $this->getProperty('sort');
        $sort = str_replace('_formatted', '', $sort);
        if ($sort == 'action') {
            $sort = 'a_name';
        }
        $this->setProperty('sort', $sort);

        return true;
    }

    public function prepareQueryBeforeCount(xPDOQuery $c)
    {
        $owner = $this->getProperty('owner', 'coupon');

        $c->select($this->modx->getSelectColumns($this->classKey, $this->classKey));

        $query = trim($this->getProperty('query'));
        if ($query != '') {
            $c->where(array(
                'code:LIKE' => "%{$query}%",
                'OR:description:LIKE' => "%{$query}%",
                'OR:modUser.username:LIKE' => "%{$query}%",
                'OR:modUserProfile.fullname:LIKE' => "%{$query}%",
            ));
        }

        $resource_id = (int)$this->getProperty('resource_id', 0);
        if (!empty($resource_id)) {
            $c->leftJoin('mspcResource', 'mspcResource',
                "mspcResource.coupon_id = {$this->classKey}.id AND mspcResource.resource_id = {$resource_id}");
            $c->where(array('mspcResource.resource_id' => $resource_id));
            $c->select($this->modx->getSelectColumns('mspcResource', 'mspcResource', 'r_', array('type', 'discount')));
        }

        $action_id = $this->getProperty('action_id');
        if ($owner == 'action' && empty($action_id)) {
            $c->innerJoin('mspcAction', 'mspcAction', "mspcAction.id = {$this->classKey}.action_id");
            // $c->where(array("mspcAction.id" => $action_id));
            $c->select($this->modx->getSelectColumns('mspcAction', 'mspcAction', 'a_', array(
                'name',
                'begins',
                'ends',
                'discount',
                'ref',
            )));
        }

        if ($owner == 'action' && !empty($action_id)) {
            $c->leftJoin('mspcAction', 'mspcAction', "mspcAction.id = {$this->classKey}.action_id AND mspcAction.id = {$action_id}");
            $c->where(array('mspcAction.id' => $action_id));
            $c->select($this->modx->getSelectColumns('mspcAction', 'mspcAction', 'a_', array(
                'name',
                'begins',
                'ends',
                'discount',
                'ref',
            )));
        } elseif ($owner != 'action') {
            $c->where(array(
                $this->classKey . '.action_id' => '0',
            ));
        }

        // если купон для акции, то приджоиним к запросу возможный заказ
        if ($owner == 'action') {
            $c->leftJoin('mspcOrder', 'mspcOrder', "mspcOrder.coupon_id = {$this->classKey}.id");
            $c->select($this->modx->getSelectColumns('mspcOrder', 'mspcOrder', 'o_', array(
                'order_id',
                'discount_amount',
                'createdon',
            )));

            $c->leftJoin('msOrder', 'msOrder', 'msOrder.id = mspcOrder.order_id');
            $c->select($this->modx->getSelectColumns('msOrder', 'msOrder', 'o_', array('num')));
        }

        $c->leftJoin('modUser', 'modUser', "modUser.id = {$this->classKey}.referrer_id");
        $c->leftJoin('modUserProfile', 'modUserProfile', "modUserProfile.internalKey = {$this->classKey}.referrer_id");
        $c->select($this->modx->getSelectColumns('modUser', 'modUser', 'referrer_', array(
            'username',
        )));
        $c->select($this->modx->getSelectColumns('modUserProfile', 'modUserProfile', 'referrer_', array(
            'fullname',
        )));

        // $this->modx->log(1, print_r($owner, 1));
        // $c->prepare();
        // $this->modx->log(1, print_r($c->toSql(), 1));

        return $c;
    }

    public function prepareRow(xPDOObject $obj)
    {
        $owner = $this->getProperty('owner', 'coupon');

        $array = $obj->toArray('', true);
        // $this->modx->log(MODX::LOG_LEVEL_ERROR, print_r($array,1));

        // $array['referrer_username'] = '';
        // $array['referrer_fullname'] = '';

        // если у купона не указана скидка, а у акции указана - ставим
        $array['discount'] = ($array['discount'] == '' && isset($array['a_discount'])) ? $array['a_discount'] : $array['discount'];

        // если для ресурса переопределена скидка - ставим
        $array['discount'] = isset($array['r_discount']) ? $array['r_discount'] : $array['discount'];

        // указываем название акции и username/fullname реферрера
        $array['action'] = !empty($array['a_name']) ? $array['a_name'] : '';
        $array['action_ref'] = !empty($array['a_ref']) ? $array['a_ref'] : false;
        if (empty($array['action']) && !empty($array['action_id'])) {
            $action = $obj->Action->toArray('', true);
            $array['action'] = $action['name'];
            $array['action_ref'] = $action['ref'];
        }
        if ($array['action_ref'] && $array['referrer_id']) {
            // $array['referrer_username'] = $obj->Referrer->username;
            // $array['referrer_fullname'] = $obj->ReferrerProfile->fullname;
        }

        // Указываем кол-во безконечность, если поле count пусто
        $array['count'] = ($array['count'] == '') ? $this->modx->lexicon('mspromocode_coupon_unlimited') : $array['count'];

        // Кол-во активаций купона
        $array['orders'] = $this->modx->getCount('mspcOrder', array('coupon_id' => $array['id'])) ?: 0;

        // id, номер и дата заказа
        $array['order_id'] = $array['o_order_id'];
        $array['order_num'] = $array['o_num'];
        $array['order_date'] = $array['o_createdon'];
        unset($array['o_order_id'], $array['o_num'], $array['o_createdon']);

        // если купон для акции и он активирован
        $array['activated'] = false;
        if (!empty($array['action_id']) && $array['count'] == 0 && !$array['active']) {
            $array['activated'] = true;
        }

        // $this->modx->log(1, print_r($array, 1));

        $array['actions'] = array();

        // Edit
        if (!(!empty($array['action_id']) && $array['count'] == 0 && !$array['active'])) {
            $array['actions'][] = array(
                'cls' => '',
                'icon' => 'icon icon-edit',
                'title' => $this->modx->lexicon('mspromocode_coupon_update'),
                //'multiple' => $this->modx->lexicon('mspromocode_coupons_update'),
                'action' => 'updateCoupon',
                'button' => true,
                'menu' => true,
            );
        }

        if ($owner != 'action') {
            if (!$array['active']) {
                $array['actions'][] = array(
                    'cls' => '',
                    'icon' => 'icon icon-toggle-on action-green',
                    'title' => $this->modx->lexicon('mspromocode_coupon_enable'),
                    'multiple' => $this->modx->lexicon('mspromocode_coupons_enable'),
                    'action' => 'enableCoupon',
                    'button' => true,
                    'menu' => true,
                );
            } else {
                $array['actions'][] = array(
                    'cls' => '',
                    'icon' => 'icon icon-toggle-off action-red',
                    'title' => $this->modx->lexicon('mspromocode_coupon_disable'),
                    'multiple' => $this->modx->lexicon('mspromocode_coupons_disable'),
                    'action' => 'disableCoupon',
                    'button' => true,
                    'menu' => true,
                );
            }
        }

        $array['actions'][] = array(
            'cls' => '',
            'icon' => 'icon icon-trash-o action-red',
            'title' => $this->modx->lexicon('mspromocode_coupon_remove'),
            'multiple' => $this->modx->lexicon('mspromocode_coupons_remove'),
            'action' => 'removeCoupon',
            'button' => true,
            'menu' => true,
        );

        return $array;
    }
}

return 'mspcCouponGetListProcessor';
