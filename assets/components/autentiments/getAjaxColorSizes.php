<?php
require_once dirname(__FILE__, 4) . '/config.core.php';
require_once MODX_CORE_PATH . 'components/gitmodx/model/gitmodx/gitmodx.class.php';
$modx = new gitModx();
$modx->initialize();

$color = htmlspecialchars($_POST['color']);
$product_id = (int)$_POST['product_id'];
$modifications = $modx->runSnippet('getModifications', [
    'where'=>[
        'Modification.product_id' => $product_id,
        'color'=>$color,
        'Modification.hide'=>0,
    ],
    'details'=>[
        'color','size'
    ],
    'sortby'=>['size'=>'ASC'],
    'groupby'=>['Modification.product_id','size'],
    'tpl'=>'product.size',
]);
exit($modifications);
