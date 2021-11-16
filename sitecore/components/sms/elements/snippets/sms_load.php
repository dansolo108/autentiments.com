<?php
$config = [
    'assetsUrl' => MODX_ASSETS_URL,
    'componentUrl' => MODX_ASSETS_URL . 'components/sms/',
    'pageId' => $modx->resource->id,
];
$js = $modx->getOption('js', $scriptProperties, $config['componentUrl'] . 'js/web/default.js');
$config['action'] = $modx->getOption('action', $scriptProperties, $config['componentUrl'] . 'action.php');
$where = $modx->getOption('where', $scriptProperties, '');
if ($where && !is_array($where)) {
    $where = json_decode($where, true);
}
if (is_array($where)) {
    $config = array_merge($where, $config);
}

$modx->regClientStartupHTMLBlock('<link rel="stylesheet" type="text/css" href="' . $config['componentUrl'] . 'css/web/default.css">');
$modx->regClientHTMLBlock("<script>var SMSConfig = " . json_encode($config) . ";</script>");
$modx->regClientScript($js);