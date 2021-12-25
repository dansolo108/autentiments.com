id: 110
name: cityRemainsTable
properties: 'a:0:{}'

-----

$pdo = $modx->getService('pdoFetch');
$sizes = $modx->resource->get('size');
$colors = $modx->resource->get('color');
$resource_id = $modx->resource->get('id');
$remains = $pdo->getCollection('stikRemains', ['product_id' => $resource_id, 'store_id' => $id]);

if (!function_exists('check_n_size_color')) {
    function check_n_size_color($remains, $size, $color) {
        foreach ($remains as $k => $remain) {
            if ($remain['size'] == $size && $remain['color'] == $color) {
                return $k;
            }
        }
    }
}

$tpl_header = '<th class="au-info-size__td"></th>';
$tpl_rows = '';
if ($sizes && $colors) {
    foreach ($sizes as $k => $v) {
        $tpl_header .= '<th class="au-info-size__td">' . $v . '</th>';
    }
    
    foreach ($colors as $color) {
        $colorId = $modx->runSnippet('msoGetColor', ['input' => $color, 'return_id' => true]);
        $tpl_rows .= '<tr class="au-info-size__tr"><td class="au-info-size__td">' . $modx->lexicon('stik_color_' . $colorId) . '</td>';
        foreach ($sizes as $size) {
            $key = check_n_size_color($remains, $size, $color);
            $tpl_rows .= '<td class="au-info-size__td">' . ((isset($remains[$key]['remains']) && $remains[$key]['remains'] > 0) ? '+' : '-') . '</td>';
        }
        $tpl_rows .= '</tr>';
    }
}

$table = '<table class="au-info-size__table au-location-remains__table">
    <tr class="au-info-size__tr">
        '.$tpl_header.'
    </tr>
    '.$tpl_rows.'
</table>';

print $table;