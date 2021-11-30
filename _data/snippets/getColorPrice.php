id: 108
name: getColorPrice
category: stik
properties: 'a:0:{}'

-----

$stikProductRemains = $modx->getService('stik', 'stikProductRemains', $modx->getOption('core_path').'components/stik/model/', $scriptProperties);
if (!($stikProductRemains instanceof stikProductRemains)) return '';

/** @var array $scriptProperties */

$product_id = (int) $modx->getOption('id', $scriptProperties, null);
$color = (string) $modx->getOption('color', $scriptProperties, null);

if ( empty($product_id) ) $product_id = $modx->resource->get('id');

if (!$color) {
    $msProduct = $modx->getObject('msProduct', $product_id);
    $resource_colors = $msProduct->get('color');
    $color = $resource_colors[0];
}

$remain = $modx->getObject('stikRemains', array(
    'product_id' => $product_id,
    'color' => $color,
    'price:>' => 0,
    'hide' => 0,
));

if ($remain) {
    $price = $remain->get('price');
    $old_price = $remain->get('old_price');
} else {
    $price = $msProduct->get('price');
    $old_price = $msProduct->get('old_price');
}

$pdoTools = $modx->getService('pdoTools');

return $pdoTools->getChunk($tpl, array(
    'price' => $price,
    'old_price' => $old_price,
));