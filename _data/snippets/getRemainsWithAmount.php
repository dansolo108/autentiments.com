id: 85
name: getRemainsWithAmount
category: stik
properties: 'a:0:{}'

-----

$stikProductRemains = $modx->getService('stik', 'stikProductRemains', $modx->getOption('core_path').'components/stik/model/', $scriptProperties);
if (!($stikProductRemains instanceof stikProductRemains)) return '';

/** @var array $scriptProperties */

$product_id = (int) $modx->getOption('id', $scriptProperties, null);
if ( empty($product_id) ) $product_id = $modx->resource->get('id');

$resource = $modx->getObject('modResource', $product_id);
$resource_sizes = $resource->get('size');

if (!$color) {
    $resource_colors = $resource->get('color');
    $color = $resource_colors[0];
}

$remains = $modx->getCollection('stikRemains', array(
    'product_id' => $product_id,
    'color' => $color,
    // 'store_id:IN' => [$modx->getOption('stikpr_stores_ids')],
));

$pdoTools = $modx->getService('pdoTools');

$options = $option = $rows = [];

foreach ($remains as $remain) {
    $option[trim($remain->get('size'))] += $remain->get('remains');
}

$sortSizes = $stikProductRemains->getSortSizes();

// восстанавливаем порядок сортировки размеров и добавляем недостающие
foreach ($resource_sizes as $k => $v) {
    $rows[$v] = $option[$v];
}

// сортируем, как указано в компоненте
uksort($rows, function($a, $b) use ($sortSizes) {
    return array_search($a, $sortSizes) - array_search($b, $sortSizes);
});

return $pdoTools->getChunk($tpl, array(
    'id' => $product_id,
    'options' => $rows,
));