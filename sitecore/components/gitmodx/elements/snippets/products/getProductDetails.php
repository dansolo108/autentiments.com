<?php
/** @var modX $modx */
/** @var array $scriptProperties */

$modification_id = (int) $modx->getOption('id', $scriptProperties, null);
$details = $modx->getOption('details', $scriptProperties, []);
if(is_string($details)){
    $details = explode(',',$details);
}
if(count($details) == 0)
    return '';
/** @var pdoFetch $pdoFetch */
$fqn = $modx->getOption('pdoFetch.class', null, 'pdotools.pdofetch', true);
$path = $modx->getOption('pdofetch_class_path', null, MODX_CORE_PATH . 'components/pdotools/model/', true);
if ($pdoClass = $modx->loadClass($fqn, $path, false, true)) {
    $pdoFetch = new $pdoClass($modx, $scriptProperties);
} else {
    return false;
}
$output = [];
foreach($details as $detail){
    $tableName = 'Detail'.$detail;
    $default = [
        'class'=>'msProduct',
        'where'=>[
            'msProduct.class_key' => 'msProduct',
            'Modification.hide:!='=>1,
        ],
        'leftJoin'=>[
            'Modification'=>[
                'class'=>'Modification',
                'on'=>'Modification.product_id = msProduct.id',
            ],
            $tableName=>[
                'class'=>'ModificationDetail',
                'on'=>"Modification.id = {$tableName}.modification_id AND {$tableName}.name = '{$detail}'"
            ]
        ],
        'select'=>[
            $tableName => $tableName.'.value as '.$detail,
        ],
        'groupby' => implode(', ', ['msProduct.id']),
        'limit'=> 10,
        'return'=>'data'
    ];
    $pdoFetch->setConfig(array_merge($default,$scriptProperties));
    $result = $pdoFetch->run();
    $result
}

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

