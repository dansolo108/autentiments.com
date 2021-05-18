<?php
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