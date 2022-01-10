id: 104
source: 1
name: getAvailableColors
category: miniShop2
properties: 'a:4:{s:7:"options";a:7:{s:4:"name";s:7:"options";s:4:"desc";s:16:"ms2_prop_options";s:4:"type";s:9:"textfield";s:7:"options";a:0:{}s:5:"value";s:0:"";s:7:"lexicon";s:20:"minishop2:properties";s:4:"area";s:0:"";}s:7:"product";a:7:{s:4:"name";s:7:"product";s:4:"desc";s:16:"ms2_prop_product";s:4:"type";s:11:"numberfield";s:7:"options";a:0:{}s:5:"value";s:0:"";s:7:"lexicon";s:20:"minishop2:properties";s:4:"area";s:0:"";}s:16:"sortOptionValues";a:7:{s:4:"name";s:16:"sortOptionValues";s:4:"desc";s:25:"ms2_prop_sortOptionValues";s:4:"type";s:9:"textfield";s:7:"options";a:0:{}s:5:"value";s:0:"";s:7:"lexicon";s:20:"minishop2:properties";s:4:"area";s:0:"";}s:3:"tpl";a:7:{s:4:"name";s:3:"tpl";s:4:"desc";s:12:"ms2_prop_tpl";s:4:"type";s:9:"textfield";s:7:"options";a:0:{}s:5:"value";s:13:"tpl.msOptions";s:7:"lexicon";s:20:"minishop2:properties";s:4:"area";s:0:"";}}'
static_file: core/components/minishop2/elements/snippets/snippet.ms_options.php

-----

/** @var modX $modx */
/** @var array $scriptProperties */

$product = (int) $modx->getOption('id', $scriptProperties, null);
$tpl = $modx->getOption('tpl', $scriptProperties, 'tpl.msOptions');

$product = !empty($product) && $product != $modx->resource->id
    ? $modx->getObject('msProduct', array('id' => $product))
    : $modx->resource;
if (!($product instanceof msProduct)) {
    return "[msOptions] The resource with id = {$product->id} is not instance of msProduct.";
}

if (!function_exists('replace_e_chars')) {
    function replace_e_chars($subject) {
        return str_replace('ё', 'е', $subject);
    }
}

$product_id = $product->get('id');

$colors = $product->get('color');
$colors_flipped = array_flip($colors);
$colors = $available = $hidden = [];

foreach ($colors_flipped as $k => $v) {
    $colors[replace_e_chars($k)] = $v;
}

// активные остатки
$remains = $modx->getCollection('stikRemains', [
    'product_id' => $product_id,
    'hide:!=' => 1,
]);

foreach ($remains as $remain) {
    $available[replace_e_chars($remain->get('color'))] = 0;
}

// скрытые остатки
$hidden_remains = $modx->getCollection('stikRemains', [
    'product_id' => $product_id,
    'hide' => 1,
]);

foreach ($hidden_remains as $hidden_remain) {
    $hidden[replace_e_chars($hidden_remain->get('color'))] = 0;
}

foreach ($colors as $k => $v) {
    $k = replace_e_chars($k);
    // удаляем, если нет в активных остатках или если скрыт хотя бы у одного склада
    if (!isset($available[$k]) || isset($hidden[$k])) {
        unset($colors[$k]);
    }
}

$colors = array_flip($colors);

$options = [];

$options['color'] = $colors;

/** @var pdoTools $pdoTools */
$pdoTools = $modx->getService('pdoTools');

return $pdoTools->getChunk($tpl, array(
    'id' => $product->id,
    'options' => $options,
));