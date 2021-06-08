id: 72
source: 1
name: msMultiCurrencyGetOrder
category: msMultiCurrency
properties: 'a:6:{s:14:"currencySymbol";a:9:{s:4:"name";s:14:"currencySymbol";s:4:"desc";s:14:"currencySymbol";s:4:"type";s:9:"textfield";s:7:"options";a:0:{}s:5:"value";s:12:"symbol_right";s:7:"lexicon";N;s:4:"area";s:0:"";s:10:"desc_trans";s:14:"currencySymbol";s:10:"area_trans";s:0:"";}s:10:"includeTVs";a:9:{s:4:"name";s:10:"includeTVs";s:4:"desc";s:19:"ms2_prop_includeTVs";s:4:"type";s:9:"textfield";s:7:"options";a:0:{}s:5:"value";s:0:"";s:7:"lexicon";s:20:"minishop2:properties";s:4:"area";s:0:"";s:10:"desc_trans";s:182:"Список ТВ параметров для выборки, через запятую. Например: "action,time" дадут плейсхолдеры [[+action]] и [[+time]].";s:10:"area_trans";s:0:"";}s:13:"includeThumbs";a:9:{s:4:"name";s:13:"includeThumbs";s:4:"desc";s:22:"ms2_prop_includeThumbs";s:4:"type";s:9:"textfield";s:7:"options";a:0:{}s:5:"value";s:0:"";s:7:"lexicon";s:20:"minishop2:properties";s:4:"area";s:0:"";s:10:"desc_trans";s:307:"Список размеров превьюшек для выборки, через запятую. Например: "small,medium" дадут плейслолдеры [[+small]] и [[+medium]]. Картинки должны быть заранее сгенерированы в галерее товара.";s:10:"area_trans";s:0:"";}s:7:"showLog";a:9:{s:4:"name";s:7:"showLog";s:4:"desc";s:16:"ms2_prop_showLog";s:4:"type";s:13:"combo-boolean";s:7:"options";a:0:{}s:5:"value";b:0;s:7:"lexicon";s:20:"minishop2:properties";s:4:"area";s:0:"";s:10:"desc_trans";s:182:"Показывать дополнительную информацию о работе сниппета. Только для авторизованных в контексте "mgr".";s:10:"area_trans";s:0:"";}s:13:"toPlaceholder";a:9:{s:4:"name";s:13:"toPlaceholder";s:4:"desc";s:22:"ms2_prop_toPlaceholder";s:4:"type";s:9:"textfield";s:7:"options";a:0:{}s:5:"value";s:0:"";s:7:"lexicon";s:20:"minishop2:properties";s:4:"area";s:0:"";s:10:"desc_trans";s:172:"Если не пусто, сниппет сохранит все данные в плейсхолдер с этим именем, вместо вывода не экран.";s:10:"area_trans";s:0:"";}s:3:"tpl";a:9:{s:4:"name";s:3:"tpl";s:4:"desc";s:12:"ms2_prop_tpl";s:4:"type";s:9:"textfield";s:7:"options";a:0:{}s:5:"value";s:14:"tpl.msGetOrder";s:7:"lexicon";s:20:"minishop2:properties";s:4:"area";s:0:"";s:10:"desc_trans";s:72:"Чанк оформления для каждого результата";s:10:"area_trans";s:0:"";}}'
static_file: core/components/msmulticurrency/elements/snippets/msMultiCurrencyGetOrder.snippet.php

-----

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

/** @var MsMC $msmc */
$msmc = $modx->getService('msmulticurrency', 'MsMC');

$tpl = $modx->getOption('tpl', $scriptProperties, 'tpl.msGetOrder');

if (empty($id) && !empty($_GET['msorder'])) {
    $id = (int)$_GET['msorder'];
}
if (empty($id)) {
    return;
}
/** @var msOrder $order */
if (!$order = $modx->getObject('msOrder', compact('id'))) {
    return $modx->lexicon('ms2_err_order_nf');
}
$canView = (!empty($_SESSION['minishop2']['orders']) && in_array($id, $_SESSION['minishop2']['orders'])) ||
    $order->get('user_id') == $modx->user->id || $modx->user->hasSessionContext('mgr') || !empty($scriptProperties['id']);
if (!$canView) {
    return '';
}

// Select ordered products
$where = array(
    'msOrderProduct.order_id' => $id,
);

// Include products properties
$leftJoin = array(
    'msProduct' => array(
        'class' => 'msProduct',
        'on' => 'msProduct.id = msOrderProduct.product_id',
    ),
    'Data' => array(
        'class' => 'msProductData',
        'on' => 'msProduct.id = Data.id',
    ),
    'Vendor' => array(
        'class' => 'msVendor',
        'on' => 'Data.vendor = Vendor.id',
    ),
);

// Select columns
$select = array(
    'msProduct' => !empty($includeContent)
        ? $modx->getSelectColumns('msProduct', 'msProduct')
        : $modx->getSelectColumns('msProduct', 'msProduct', '', array('content'), true),
    'Data' => $modx->getSelectColumns('msProductData', 'Data', '', array('id'),
            true) . ',`Data`.`price` as `original_price`',
    'Vendor' => $modx->getSelectColumns('msVendor', 'Vendor', 'vendor.', array('id'), true),
    'OrderProduct' => $modx->getSelectColumns('msOrderProduct', 'msOrderProduct', '', array('id'), true) . ', `msOrderProduct`.`id` as `order_product_id`',
);

// Include products thumbnails
if (!empty($includeThumbs)) {
    $thumbs = array_map('trim', explode(',', $includeThumbs));
    if (!empty($thumbs[0])) {
        foreach ($thumbs as $thumb) {
            $leftJoin[$thumb] = array(
                'class' => 'msProductFile',
                'on' => "`{$thumb}`.product_id = msProduct.id AND `{$thumb}`.parent != 0 AND `{$thumb}`.path LIKE '%/{$thumb}/%'",
            );
            $select[$thumb] = "`{$thumb}`.url as '{$thumb}'";
        }
        $pdoFetch->addTime('Included list of thumbnails: <b>' . implode(', ', $thumbs) . '</b>.');
    }
}

// Add user parameters
foreach (array('where', 'leftJoin', 'select') as $v) {
    if (!empty($scriptProperties[$v])) {
        $tmp = $scriptProperties[$v];
        if (!is_array($tmp)) {
            $tmp = json_decode($tmp, true);
        }
        if (is_array($tmp)) {
            $$v = array_merge($$v, $tmp);
        }
    }
    unset($scriptProperties[$v]);
}
$pdoFetch->addTime('Conditions prepared');

// Tables for joining
$default = array(
    'class' => 'msOrderProduct',
    'where' => $where,
    'leftJoin' => $leftJoin,
    'select' => $select,
    'joinTVsTo' => 'msProduct',
    'sortby' => 'msOrderProduct.id',
    'sortdir' => 'asc',
    'groupby' => 'msOrderProduct.id',
    'fastMode' => false,
    'limit' => 0,
    'return' => 'data',
    'decodeJSON' => true,
    'nestedChunkPrefix' => 'minishop2_',
);
// Merge all properties and run!
$pdoFetch->setConfig(array_merge($default, $scriptProperties), true);
$rows = $pdoFetch->run();

$properties = $order->get('properties');
if (is_array($properties)) {
    $currencyData = $modx->getOption('msmc', $properties, array(), true);
} else {
    $currencyData = $msmc->getBaseCurrency();
}

$currencyId = $currencyData['id'];
$course = $currencyData['course'];
$currency = $currencyData[$currencySymbol];
$baseCurrencyId = $currencyData['base_currency_id'];
$cartUserCurrency = $currencyData['cart_user_currency'];

$products = array();
$cart_count = 0;
foreach ($rows as $product) {

    $product['original_price'] = $msmc->getPrice($product['original_price'], 0, $currencyId, $course, false);
    $product['old_price'] = $msmc->getPrice($product['old_price'], 0, $currencyId, $course, false);
    
    if (!$cartUserCurrency && $currencyId != $baseCurrencyId) {
        $product['price'] = $msmc->getPrice($product['price'], 0, $currencyId, $course, false);
        $product['cost'] = $product['price'] * $product['count'];
    } else {

    }

    $product['old_price'] = $miniShop2->formatPrice(
        $product['original_price'] > $product['price']
            ? $product['original_price']
            : $product['old_price']
    );

    $product['price'] = $miniShop2->formatPrice($product['price']);
    $product['cost'] = $miniShop2->formatPrice($product['cost']);
    $product['weight'] = $miniShop2->formatWeight($product['weight']);

    $product['id'] = (int)$product['id'];
    if (empty($product['name'])) {
        $product['name'] = $product['pagetitle'];
    } else {
        $product['pagetitle'] = $product['name'];
    }

    // Additional properties of product
    if (!empty($product['options']) && is_array($product['options'])) {
        foreach ($product['options'] as $option => $value) {
            $product['option.' . $option] = $value;
        }
    }

    // Add option values
    $options = $modx->call('msProductData', 'loadOptions', array($modx, $product['id']));
    $products[] = array_merge($product, $options);

    // Count total
    $cart_count += $product['count'];
}


$cost = $order->get('cost');
$cartCost = $order->get('cart_cost');
$deliveryCost = $order->get('delivery_cost');

if (!$cartUserCurrency && $currencyId != $baseCurrencyId) {
    $cost = $msmc->getPrice($cost, 0, $currencyId, $course, false);
    $cartCost = $msmc->getPrice($cartCost, 0, $currencyId, $course, false);
    $deliveryCost = $msmc->getPrice($deliveryCost, 0, $currencyId, $course, false);
}

$pls = array_merge($scriptProperties, array(
    'currency' => $currency,
    'order' => $order->toArray(),
    'products' => $products,
    'user' => ($tmp = $order->getOne('User'))
        ? array_merge($tmp->getOne('Profile')->toArray(), $tmp->toArray())
        : array(),
    'address' => ($tmp = $order->getOne('Address'))
        ? $tmp->toArray()
        : array(),
    'delivery' => ($tmp = $order->getOne('Delivery'))
        ? $tmp->toArray()
        : array(),
    'payment' => ($payment = $order->getOne('Payment'))
        ? $payment->toArray()
        : array(),
    'total' => array(
        'cost' => $miniShop2->formatPrice($cost),
        'cart_cost' => $miniShop2->formatPrice($cartCost),
        'delivery_cost' => $miniShop2->formatPrice($deliveryCost),
        'weight' => $miniShop2->formatWeight($order->get('weight')),
        'cart_weight' => $miniShop2->formatWeight($order->get('weight')),
        'cart_count' => $cart_count,
    ),
));

// add "payment" link
if ($payment and $class = $payment->get('class')) {
    $status = $modx->getOption('payStatus', $scriptProperties, '1');
    $status = array_map('trim', explode(',', $status));
    if (in_array($order->get('status'), $status)) {
        $miniShop2->loadCustomClasses('payment');
        if (class_exists($class)) {
            /** @var msPaymentHandler|PayPal $handler */
            $handler = new $class($order);
            if (method_exists($handler, 'getPaymentLink')) {
                $link = $handler->getPaymentLink($order);
                $pls['payment_link'] = $link;
            }
        }
    }
}

$output = $pdoFetch->getChunk($tpl, $pls);

if ($modx->user->hasSessionContext('mgr') && !empty($showLog)) {
    $output .= '<pre class="msGetOrderLog">' . print_r($pdoFetch->getTime(), true) . '</pre>';
}

if (!empty($toPlaceholder)) {
    $modx->setPlaceholder($toPlaceholder, $output);
} else {
    return $output;
}