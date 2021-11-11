<?php
require_once dirname(dirname(dirname(dirname(__FILE__)))) . '/config.core.php';
require_once MODX_CORE_PATH . 'model/modx/modx.class.php';
$modx = new modX();
$modx->initialize('web');

$color = htmlspecialchars($_POST['color']);
$mode = htmlspecialchars($_POST['mode']);
$product = (int) $_POST['product'];

if (!$color || !$product) exit();

// Load main services
// $modx->setLogTarget('FILE');
$modx->setLogLevel(modX::LOG_LEVEL_ERROR);
$modx->getService('error','error.modError');


print $modx->runSnippet('msGallery', [
    'product' => $product,
    'tpl' => $mode == 'card' ? 'stik.msGallery.card' : 'stik.msGallery',
    'where' => [
        'description' => $color,
    ],
]);

@session_write_close(); exit();