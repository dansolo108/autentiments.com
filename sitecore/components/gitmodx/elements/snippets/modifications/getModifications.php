<?php
/** @var modX $modx */
/** @var array $scriptProperties */

$modification_id = (int) $modx->getOption('id', $scriptProperties, null);
$details = $modx->getOption('details', $scriptProperties, []);
if(is_string($details)){
    $details = explode(',',$details);
}
/** @var pdoFetch $pdoFetch */
$fqn = $modx->getOption('pdoFetch.class', null, 'pdotools.pdofetch', true);
$path = $modx->getOption('pdofetch_class_path', null, MODX_CORE_PATH . 'components/pdotools/model/', true);
if ($pdoClass = $modx->loadClass($fqn, $path, false, true)) {
    $pdoFetch = new $pdoClass($modx, $scriptProperties);
} else {
    return false;
}
$where = [];
$leftJoin = [
    'msProduct'=>[
        'class'=>'msProduct',
        'on'=>'Modification.product_id = msProduct.id',
    ],
];
$select = [
    'Modification'=>'`Modification`.`id`, `Modification`.`product_id`,  `Modification`.`price`, `Modification`.`old_price`, `Modification`.`hide`',
    'msProduct' => !empty($includeContent)
        ? $modx->getSelectColumns('msProduct', 'msProduct','',['id'],true)
        : $modx->getSelectColumns('msProduct', 'msProduct', '', ['id','content'], true),
];
$groupby = [
  'Modification.id'
];

foreach($details as $detail){
    $tableName = 'Detail'.$detail;
    $leftJoin[$tableName]= [
        'class'=>'ModificationDetail',
        'on'=>"Modification.id = {$tableName}.modification_id AND {$tableName}.name = '{$detail}'"
    ];
    $select[$tableName] = $tableName.'.value as '.$detail;
}
$default = [
    'class'=>'Modification',
    'where'=>$where,
    'leftJoin'=>$leftJoin,
    'select'=>$select,
    'groupby' => implode(', ', $groupby),
    'limit'=> 10,
    'return'=>'data'
];
$pdoFetch->setConfig(array_merge($default,$scriptProperties));
$result = $pdoFetch->run();
$output = '';
foreach ($result as $key => &$item){
    if(in_array('Цвет', $details)){
        $pdoFetch->setConfig([
            'class'=>'msProductFile',
            'where'=>[
                'product_id'=>$item['product_id'],
                'description'=> $item['Цвет']
            ],
            'sortby'=>['rank'=>'ASC'],
            'limit'=>0,
        ],false);
        $files = $pdoFetch->run();
        $item['files'] = $files;
    }
    $item['idx'] = $key;
    if(!empty($tpl)) {
        $output .= $pdoFetch->getChunk($tpl, array_merge(array_diff_key($scriptProperties,$default),$item));
    }
}
if(!empty($tpl)){
    return $output;
}else{
    return $result;
}

