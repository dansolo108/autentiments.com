id: 58
source: 1
name: mspcRefCoupon
category: msPromoCode
properties: "a:5:{s:7:\"user_id\";a:7:{s:4:\"name\";s:7:\"user_id\";s:4:\"desc\";s:24:\"mspromocode_prop_user_id\";s:4:\"type\";s:11:\"numberfield\";s:7:\"options\";a:0:{}s:5:\"value\";i:0;s:7:\"lexicon\";s:22:\"mspromocode:properties\";s:4:\"area\";s:0:\"\";}s:3:\"tpl\";a:7:{s:4:\"name\";s:3:\"tpl\";s:4:\"desc\";s:20:\"mspromocode_prop_tpl\";s:4:\"type\";s:9:\"textfield\";s:7:\"options\";a:0:{}s:5:\"value\";s:174:\"@INLINE <p><b>Код реферального промо-кода</b>: {$coupon}</p><p><b>Количество применений</b>: {$orders_count}</p><ul>{$orders}</ul>\";s:7:\"lexicon\";s:22:\"mspromocode:properties\";s:4:\"area\";s:0:\"\";}s:8:\"tplOrder\";a:7:{s:4:\"name\";s:8:\"tplOrder\";s:4:\"desc\";s:25:\"mspromocode_prop_tplOrder\";s:4:\"type\";s:9:\"textfield\";s:7:\"options\";a:0:{}s:5:\"value\";s:239:\"@INLINE <li><p><b>Пользователь</b>: {$fullname ?: $username}</p><p><b>Сумма заказа</b>: {$order_cost}</p><p><b>Сумма скидки</b>: {$discount_amount}</p><p><b>Дата заказа</b>: {$createdon}</p></li>\";s:7:\"lexicon\";s:22:\"mspromocode:properties\";s:4:\"area\";s:0:\"\";}s:15:\"outputSeparator\";a:7:{s:4:\"name\";s:15:\"outputSeparator\";s:4:\"desc\";s:32:\"mspromocode_prop_outputSeparator\";s:4:\"type\";s:9:\"textfield\";s:7:\"options\";a:0:{}s:5:\"value\";s:1:\"\n\";s:7:\"lexicon\";s:22:\"mspromocode:properties\";s:4:\"area\";s:0:\"\";}s:13:\"toPlaceholder\";a:7:{s:4:\"name\";s:13:\"toPlaceholder\";s:4:\"desc\";s:30:\"mspromocode_prop_toPlaceholder\";s:4:\"type\";s:13:\"combo-boolean\";s:7:\"options\";a:0:{}s:5:\"value\";b:0;s:7:\"lexicon\";s:22:\"mspromocode:properties\";s:4:\"area\";s:0:\"\";}}"
static_file: core/components/mspromocode/elements/snippets/snippet.ref_coupon.php

-----

$sp = &$scriptProperties;

/* @var pdoFetch $pdoFetch */
$fqn = $modx->getOption('pdoFetch.class', null, 'pdotools.pdofetch', true);
$path = $modx->getOption('pdotools_class_path', null, MODX_CORE_PATH . 'components/pdotools/model/', true);
if ($pdoClass = $modx->loadClass($fqn, $path, false, true)) {
    $pdoFetch = new $pdoClass($modx, $sp);
} else {
    return false;
}

$user_id = $sp['user_id'] ? $sp['user_id'] : 0;
$user_id = empty($user_id) ? $modx->user->id : $user_id;
if (!empty($user_id)) {
    $where['referrer_id'] = $user_id;
} else {
    return false;
}

$tplOrder = $sp['tplOrder'] ? $sp['tplOrder']
    : '@INLINE <li><p><b>Пользователь</b>: {$fullname ?: $username}</p><p><b>Сумма заказа</b>: {$order_cost}</p><p><b>Сумма скидки</b>: {$discount_amount}</p><p><b>Дата заказа</b>: {$createdon}</p></li>';
$tpl = $sp['tpl'] ? $sp['tpl']
    : '@INLINE <p><b>Код реферального промо-кода</b>: {$coupon}</p><p><b>Количество применений</b>: {$orders_count}</p><ul>{$orders}</ul>';
$outputSeparator = isset($sp['outputSeparator']) ? $sp['outputSeparator'] : "\n";
$toPlaceholder = $sp['toPlaceholder'] ? $sp['toPlaceholder'] : false;
$sp['totalVar'] = $sp['totalVar'] ? $sp['totalVar'] : 'total';

unset($sp['user_id']);
unset($sp['tplOrder']);
unset($sp['tpl']);
unset($sp['outputSeparator']);
unset($sp['toPlaceholder']);

$orders = array();
$output = '';
if ($coupon = $modx->getObject('mspcCoupon', $where)) {
    $class = 'mspcOrder';
    $loadModels = array('mspromocode' => MODX_CORE_PATH . 'components/mspromocode/model/');
    $select = array(
        'mspcOrder' => '
            mspcOrder.order_id as order_id,
            mspcOrder.discount_amount as discount_amount
        ',
        'msOrder' => '
            msOrder.user_id as user_id,
            msOrder.createdon as createdon,
            msOrder.num as order_num,
            msOrder.cost as order_cost,
            msOrder.cart_cost as order_cart_cost
        ',
        'modUser' => '
            modUser.username as username
        ',
        'modUserProfile' => '
            modUserProfile.fullname as fullname,
            modUserProfile.email as email,
            modUserProfile.phone as phone
        ',
        'msOrderStatus' => '
            msOrderStatus.name as status_name,
            msOrderStatus.description as status_description
        ',
    );
    $leftJoin = array(
        'msOrder' => array(
            'class' => 'msOrder',
            'alias' => 'msOrder',
            'on' => 'msOrder.id = mspcOrder.order_id',
        ),
        'modUser' => array(
            'class' => 'modUser',
            'alias' => 'modUser',
            'on' => 'modUser.id = msOrder.user_id',
        ),
        'modUserProfile' => array(
            'class' => 'modUserProfile',
            'alias' => 'modUserProfile',
            'on' => 'modUserProfile.internalKey = msOrder.user_id',
        ),
        'msOrderStatus' => array(
            'class' => 'msOrderStatus',
            'alias' => 'msOrderStatus',
            'on' => 'msOrderStatus.id = msOrder.status',
        ),
    );
    $where = array(
        'mspcOrder.coupon_id' => $coupon->id,
    );
    if (!empty($sp['createdon_from']) && !empty($sp['createdon_to'])) {
        $where[] = '(msOrder.createdon BETWEEN "' .
                   date('Y-m-d 00:00:00', $sp['createdon_from']) .
                   '" AND "' .
                   date('Y-m-d 23:59:59', $sp['createdon_to']) .
                   '")';
    }
    if (!empty($sp['status'])) {
        $status = $sp['status'];
        if (!is_array($status)) {
            $status = $modx->fromJSON($sp['status']);
        }
        if (!is_array($status)) {
            $status = array_map('trim', explode(',', $sp['status']));
        }
        if (is_array($status)) {
            $where[]['msOrderStatus.id:IN'] = $status;
        }
    }
    unset($sp['createdon_from']);
    unset($sp['createdon_to']);
    unset($sp['status']);

    $pdoFetch->setConfig(array_merge(array(
        'class' => $class,
        'loadModels' => $modx->toJSON($loadModels),
        'select' => $modx->toJSON($select),
        'leftJoin' => $modx->toJSON($leftJoin),
        'where' => $modx->toJSON($where),
        'groupby' => $class . '.id',
        'return' => 'data',
    ), $sp), false);

    $rows = $pdoFetch->run();

    $data = array(
        'coupon' => $coupon->code,
        'coupon_id' => $coupon->id,
        'orders_count' => (int)$modx->getPlaceholder($sp['totalVar']),
        'orders' => $rows,
    );
    // print_r($data);die;

    if ($data['orders_count']) {
        foreach ($data['orders'] as $order) {
            $orders[] = $pdoFetch->getChunk($tplOrder, $order);
        }
    }

    $output = $pdoFetch->getChunk($tpl, array_merge($data, array(
        'orders' => implode($outputSeparator, $orders),
    )));

    if (!empty($toPlaceholder)) {
        $modx->setPlaceholder($toPlaceholder, $output);

        return '';
    }
}

return $output;