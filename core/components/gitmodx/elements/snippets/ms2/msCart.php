<?php
/** @var modX $modx */
/** @var array $scriptProperties */
/** @var miniShop2 $miniShop2 */
$miniShop2 = $modx->getService('miniShop2');
$miniShop2->initialize($modx->context->key);
/** @var pdoFetch $pdoFetch */
$fqn = $modx->getOption('pdoFetch.class', null, 'pdotools.pdofetch', true);
$path = $modx->getOption('pdofetch_class_path', null, MODX_CORE_PATH . 'components/pdotools/model/', true);
if ($pdoClass = $modx->loadClass($fqn, $path, false, true)) {
    $pdoFetch = new $pdoClass($modx, $scriptProperties);
} else {
    return false;
}
$pdoFetch->addTime('pdoTools loaded.');

$tpl = $modx->getOption('tpl', $scriptProperties, 'tpl.msCart');
$cart = $miniShop2->cart->get();
$status = $miniShop2->cart->status();

// Do not show empty cart when displaying order details
if (!empty($_GET['msorder'])) {
    return '';
} elseif (empty($status['total_count'])) {
    return $pdoFetch->getChunk($tpl);
}

// Select cart products
$where = array(
    'Modification.id:IN' => array(),
);
foreach ($cart as $entry) {
    $where['Modification.id:IN'][] = $entry['id'];
}
$where['Modification.id:IN'] = array_unique($where['Modification.id:IN']);

$tmp = $modx->runSnippet('getModifications',array_merge(['where'=>$where,'details'=>['color','size']], $scriptProperties,['tpl'=>'']));

$rows = array();
foreach ($tmp as $row) {
    $rows[$row['id']] = $row;
}
// Process products in cart
$products = array();
$total = array('count' => 0, 'weight' => 0, 'cost' => 0, 'discount' => 0);
foreach ($cart as $key => $entry) {
    if (!isset($rows[$entry['id']])) {
        continue;
    }
    $modification = $rows[$entry['id']];

    $modification['key'] = $key;
    $modification['count'] = $entry['count'];
    $old_price = $modification['old_price'];
    if ($modification['price'] > $entry['price'] && $modification['price'] > $old_price) {
        $old_price = $modification['price'];
    }
    $discount_price = $old_price > 0 ? $old_price - $entry['price'] : 0;

    $modification['old_price'] = $miniShop2->formatPrice($old_price);
    $modification['price'] = $miniShop2->formatPrice($entry['price']);
    $modification['weight'] = $miniShop2->formatWeight($entry['weight']);
    $modification['cost'] = $miniShop2->formatPrice($entry['count'] * $entry['price']);
    $modification['discount_price'] = $miniShop2->formatPrice($discount_price);
    $modification['discount_cost'] = $miniShop2->formatPrice($entry['count'] * $discount_price);
    // Additional properties of product in cart
    if (!empty($entry['options']) && is_array($entry['options'])) {
        $product['options'] = $entry['options'];
        foreach ($entry['options'] as $option => $value) {
            $product['option.' . $option] = $value;
        }
    }
    // Add option values
    $options = $modx->call('msProductData', 'loadOptions', array($modx, $modification['product_id']));
    $products[] = array_merge($modification, $options);

    // Count total
    $total['count'] += $entry['count'];
    $total['cost'] += $entry['count'] * $entry['price'];
    $total['weight'] += $entry['count'] * $entry['weight'];
    $total['discount'] += $entry['count'] * $discount_price;
}
$total['cost'] = $miniShop2->formatPrice($total['cost']);
$total['discount'] = $miniShop2->formatPrice($total['discount']);
$total['weight'] = $miniShop2->formatWeight($total['weight']);

$output = $pdoFetch->getChunk($tpl, array(
    'total' => $total,
    'products' => $products,
));

if ($modx->user->hasSessionContext('mgr') && !empty($showLog)) {
    $output .= '<pre class="msCartLog">' . print_r($pdoFetch->getTime(), true) . '</pre>';
}

if (!empty($toPlaceholder)) {
    $modx->setPlaceholder($toPlaceholder, $output);
} else {
    return $output;
}