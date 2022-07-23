<?php
require_once dirname(dirname(dirname(dirname(__FILE__)))) . '/config.core.php';
require_once MODX_CORE_PATH . 'components/gitmodx/model/gitmodx/gitmodx.class.php';
$modx = new gitModx();
$modx->initialize('web');

$color = htmlspecialchars($_POST['color']);
$mode = htmlspecialchars($_POST['mode']);
$product = (int) $_POST['product'];

if (!$color || !$product) exit();

// Load main services
// $modx->setLogTarget('FILE');
$modx->setLogLevel(modX::LOG_LEVEL_ERROR);
$modx->getService('error','error.modError');

$output = $modx->runSnippet('msGallery', [
    'product' => $product,
    'tpl' => $mode == 'card' ? 'stik.msGallery.card' : 'stik.msGallery',
    'where' => [
        'description' => $color,
    ],
]);

if ($mode == 'card') {
    $tmp = $output;
    $prices = $modx->runSnippet('getColorPrice', [
        'id' => $product,
        'color' => $color,
        'tpl' => 'stik.cardPrices.tpl'
    ]);
    $output = json_encode([
        'gallery' => $tmp,
        'prices' => $prices,
    ]);
}

print $output;

@session_write_close(); exit();