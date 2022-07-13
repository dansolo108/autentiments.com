<?php
if ($input === '') return;
$name = mb_strtolower($input);
$modx->lexicon->load('core:default');
$output = $input;
if (!$name) {
    return $output;
}
$key = 'msoptionhexcolor/list';
if (!$msHexColor = $modx->cacheManager->get($key)) {
    $msOptionHexColor = $modx->getService('msOptionHexColor', 'msOptionHexColor', MODX_CORE_PATH . 'components/msoptionhexcolor/model/', $scriptProperties);
    if (!$msOptionHexColor) {
        return 'Could not load msOptionHexColor class!';
    }
    $q = $modx->newQuery('msHexColor');
    $q->select('msHexColor.id,msHexColor.name,msHexColor.hex');
    $q->sortby('name','ASC');
    if ($q->prepare() && $q->stmt->execute()) {
        $msHexColor = $q->stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    $modx->cacheManager->set($key, $msHexColor);
}

foreach ($msHexColor as $color) {
    if (mb_strtolower($color['name']) == $name) {
        if ($return_id) {
            $output = $color['id'];
        } else {
            $output = $color['hex'];
        }
    }
}

return $output;