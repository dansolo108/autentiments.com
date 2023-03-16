<?php

/** @var array $scriptProperties */
/** @var msFavorites $msFavorites */
if (!$msFavorites = $modx->getService('msfavorites.msFavorites', '', MODX_CORE_PATH . 'components/msfavorites/model/')) {
    return 'Could not load msFavorites class!';
}
/** @var pdoFetch $pdoFetch */
if (!$modx->loadClass('pdofetch', MODX_CORE_PATH . 'components/pdotools/model/pdotools/', false, true)) {
    return false;
}

if (!empty($returnIds)) {
    $return = 'ids';
}
if ($return === 'ids') {
    $returnIds = 1;
}

if (!isset($list) OR $list == '') {
    $list = 'default';
}
if (!isset($type) OR $type == '') {
    $type = 'resource';
}

$list = is_array($list) ? $list : array_map('trim', explode(',', $list));
$rows = $modx->runSnippet('msFavorites.objects', array_merge($scriptProperties, ['return' => 'data', 'list' => $list, 'type' => $type, 'groupKey' => 'list']));
$pls = [
    'list'  => [],
    'total' => [],
];
foreach ($list as $l) {
    $keys = !empty($rows[$l]) ? array_column($rows[$l], 'key') : [];
    $pls['list'][$l] = !empty($keys) ? implode(',', $keys) : '-0';
    $pls['total'][$l] = count($keys);
}
$msFavorites->setPlaceholders($pls);

$log = '';
if ($modx->user->hasSessionContext('mgr') && !empty($showLog)) {
    $log .= '<pre class="msFavoritesLog">' . print_r($pdoFetch->getTime(), 1) . '</pre>';
}
$modx->setPlaceholder('msFavorites.log', $log);

$output = [];
switch ($return) {
    case 'data':
        $output = $pls;
        break;
    case 'json':
        $output = json_encode($pls, true);
        break;
    case 'ids':
    default:
        $output = reset($pls['list']);
        if (!empty($toPlaceholder)) {
            $modx->setPlaceholder($toPlaceholder, $output);
            $output = '';
        }
        break;
}

return $output;