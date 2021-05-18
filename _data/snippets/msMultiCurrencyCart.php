id: 77
source: 1
name: msMultiCurrencyCart
category: msMultiCurrency
properties: 'a:6:{s:10:"includeTVs";a:9:{s:4:"name";s:10:"includeTVs";s:4:"desc";s:19:"ms2_prop_includeTVs";s:4:"type";s:9:"textfield";s:7:"options";a:0:{}s:5:"value";s:0:"";s:7:"lexicon";s:20:"minishop2:properties";s:4:"area";s:0:"";s:10:"desc_trans";s:182:"Список ТВ параметров для выборки, через запятую. Например: "action,time" дадут плейсхолдеры [[+action]] и [[+time]].";s:10:"area_trans";s:0:"";}s:13:"includeThumbs";a:9:{s:4:"name";s:13:"includeThumbs";s:4:"desc";s:22:"ms2_prop_includeThumbs";s:4:"type";s:9:"textfield";s:7:"options";a:0:{}s:5:"value";s:0:"";s:7:"lexicon";s:20:"minishop2:properties";s:4:"area";s:0:"";s:10:"desc_trans";s:307:"Список размеров превьюшек для выборки, через запятую. Например: "small,medium" дадут плейслолдеры [[+small]] и [[+medium]]. Картинки должны быть заранее сгенерированы в галерее товара.";s:10:"area_trans";s:0:"";}s:7:"showLog";a:9:{s:4:"name";s:7:"showLog";s:4:"desc";s:16:"ms2_prop_showLog";s:4:"type";s:13:"combo-boolean";s:7:"options";a:0:{}s:5:"value";b:0;s:7:"lexicon";s:20:"minishop2:properties";s:4:"area";s:0:"";s:10:"desc_trans";s:182:"Показывать дополнительную информацию о работе сниппета. Только для авторизованных в контексте "mgr".";s:10:"area_trans";s:0:"";}s:13:"toPlaceholder";a:9:{s:4:"name";s:13:"toPlaceholder";s:4:"desc";s:22:"ms2_prop_toPlaceholder";s:4:"type";s:9:"textfield";s:7:"options";a:0:{}s:5:"value";s:0:"";s:7:"lexicon";s:20:"minishop2:properties";s:4:"area";s:0:"";s:10:"desc_trans";s:172:"Если не пусто, сниппет сохранит все данные в плейсхолдер с этим именем, вместо вывода не экран.";s:10:"area_trans";s:0:"";}s:3:"tpl";a:9:{s:4:"name";s:3:"tpl";s:4:"desc";s:12:"ms2_prop_tpl";s:4:"type";s:9:"textfield";s:7:"options";a:0:{}s:5:"value";s:10:"tpl.msCart";s:7:"lexicon";s:20:"minishop2:properties";s:4:"area";s:0:"";s:10:"desc_trans";s:72:"Чанк оформления для каждого результата";s:10:"area_trans";s:0:"";}s:14:"currencySymbol";a:9:{s:4:"name";s:14:"currencySymbol";s:4:"desc";s:14:"currencySymbol";s:4:"type";s:9:"textfield";s:7:"options";a:0:{}s:5:"value";s:12:"symbol_right";s:7:"lexicon";N;s:4:"area";s:0:"";s:10:"desc_trans";s:14:"currencySymbol";s:10:"area_trans";s:0:"";}}'
static_file: core/components/msmulticurrency/elements/snippets/msMultiCurrencyCart.snippet.php

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
    'msProduct.id:IN' => array(),
);
foreach ($cart as $entry) {
    $where['msProduct.id:IN'][] = $entry['id'];
}
$where['msProduct.id:IN'] = array_unique($where['msProduct.id:IN']);

// Include products properties
$leftJoin = array(
    'Data' => array(
        'class' => 'msProductData',
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
    'Data' => $modx->getSelectColumns('msProductData', 'Data', '', array('id'), true),
    'Vendor' => $modx->getSelectColumns('msVendor', 'Vendor', 'vendor.', array('id'), true),
);

// Include products thumbnails
if (!empty($includeThumbs)) {
    $thumbs = array_map('trim', explode(',', $includeThumbs));
    if (!empty($thumbs[0])) {
        foreach ($thumbs as $thumb) {
            $leftJoin[$thumb] = array(
                'class' => 'msProductFile',
                'on' => "`{$thumb}`.product_id = msProduct.id AND `{$thumb}`.parent != 0 AND `{$thumb}`.path LIKE '%/{$thumb}/%' AND `{$thumb}`.rank = 0",
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

$default = array(
    'class' => 'msProduct',
    'where' => $where,
    'leftJoin' => $leftJoin,
    'select' => $select,
    'sortby' => 'msProduct.id',
    'sortdir' => 'ASC',
    'groupby' => 'msProduct.id',
    'limit' => 0,
    'return' => 'data',
    'nestedChunkPrefix' => 'minishop2_',
);
// Merge all properties and run!
$pdoFetch->setConfig(array_merge($default, $scriptProperties), false);

$tmp = $pdoFetch->run();
$rows = array();
foreach ($tmp as $row) {
    $rows[$row['id']] = $row;
}

// Process products in cart
$products = array();
$total = array('count' => 0, 'weight' => 0, 'cost' => 0, 'discount' => 0);
$baseCurrencyId = $modx->getOption('msmulticurrency.base_currency', null, 0, true);
$cartUserCurrency = $modx->getOption('msmulticurrency.cart_user_currency', null, 0, true);
$userCurrencyData = $msmc->getUserCurrencyData();
$currencyId = $userCurrencyData['id'];
$currency = $userCurrencyData[$currencySymbol];

foreach ($cart as $key => $entry) {
    if (!isset($rows[$entry['id']])) {
        continue;
    }
    $product = $rows[$entry['id']];

    if ($currencyId !== $baseCurrencyId) {
        $entry['price'] = $msmc->getPrice($entry['price'], $product['id'], $currencyId, 0, false);
        $product['price'] = $msmc->getPrice($product['price'], $product['id'], $currencyId, 0, false);
        $product['old_price'] = $msmc->getPrice($product['old_price'], $product['id'], $currencyId, 0, false);
    }

    $product['key'] = $key;
    $product['count'] = $entry['count'];
    $old_price = $product['old_price'];
    if ($product['price'] > $entry['price']) {
        $old_price = $product['price'];
    }
    $discount_price = $old_price > 0 ? $old_price - $entry['price'] : 0;

    $product['old_price'] = $miniShop2->formatPrice($old_price);
    $product['price'] = $miniShop2->formatPrice($entry['price']);
    $product['weight'] = $miniShop2->formatWeight($entry['weight']);
    $product['cost'] = $miniShop2->formatPrice($entry['count'] * $entry['price']);
    $product['discount_price'] = $miniShop2->formatPrice($discount_price);
    $product['discount_cost'] = $miniShop2->formatPrice($entry['count'] * $discount_price);

    // Additional properties of product in cart
    if (!empty($entry['options']) && is_array($entry['options'])) {
        $product['options'] = $entry['options'];
        foreach ($entry['options'] as $option => $value) {
            $product['option.' . $option] = $value;
        }
    }

    // Add option values
    $options = $modx->call('msProductData', 'loadOptions', array($modx, $product['id']));
    $products[] = array_merge($product, $options);

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
    'currency' => $currency,
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