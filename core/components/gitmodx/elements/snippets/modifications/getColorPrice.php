<?php

/** @var array $scriptProperties */
/** @var gitModx $modx */
$id = (int) $modx->getOption('id', $scriptProperties, null);
$color = (string) $modx->getOption('color', $scriptProperties, null);
$tpl = $modx->getOption('tpl', $scriptProperties, '');

if (empty($id) ) $id = $modx->resource->get('id');

$modification = $modx->runSnippet('getModifications',[
    'where'=>[
        'Modification.product_id'=>$id,
        'Modification.hide'=>false,
    ],
    'having'=>[
        'color'=>$color
    ],
    'details'=>['color'],
    'groupby'=>'Modification.product_id, color',
    'select'=>[
        'DetailСolor'=>'value as color',
        'Modification'=>'price, old_price'
    ],
    'tpl'=>$tpl
]);
if($tpl){
    return $modification;
}
else{
    if(count($modification) > 0){
        return $modification[0];
    }
}
return '';