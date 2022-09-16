<?php
require_once dirname(__FILE__, 4) . '/config.core.php';
require_once MODX_CORE_PATH . 'components/gitmodx/model/gitmodx/gitmodx.class.php';
$modx = new gitModx();
$modx->initialize();

$color = htmlspecialchars($_POST['color']);
$mode = htmlspecialchars($_POST['mode']);
$product_id = (int) $_POST['product_id'];
if (!$color || !$product_id) exit();

// Load main services
// $modx->setLogTarget('FILE');
$modx->setLogLevel(modX::LOG_LEVEL_ERROR);
$modx->getService('error','error.modError');

$output = $modx->runSnippet('msGallery', [
    'product' => $product_id,
    'tpl' => $mode == 'card' ? 'stik.msGallery.card' : 'stik.msGallery',
    'where' => [
        'description' => $color,
    ],
]);
exit($output);