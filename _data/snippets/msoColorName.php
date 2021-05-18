id: 84
source: 1
name: msoColorName
description: 'msoColorName snippet to show color name by id'
category: msOptionHexColor
properties: 'a:0:{}'
static_file: core/components/msoptionhexcolor/elements/snippets/msocolorname.php

-----

if ($input === '') return;
$id = (int) $input;
$modx->lexicon->load('core:default');
$output = $modx->lexicon('no');
if (!$id) {
    return $output;
}
if (!$options) {
    $options = 'name';
}
$key = 'msoptionhexcolor/'.$options.'/' . $id;
if (!$output = $modx->cacheManager->get($key)) {
    $msOptionHexColor = $modx->getService('msOptionHexColor', 'msOptionHexColor', MODX_CORE_PATH . 'components/msoptionhexcolor/model/', $scriptProperties);
    if (!$msOptionHexColor) {
        return 'Could not load msOptionHexColor class!';
    }
    if ($color = $modx->getObject('msHexColor', $id)) {
        $output = $color->get($options);
    }
    $modx->cacheManager->set($key, $output);
}
return $output;