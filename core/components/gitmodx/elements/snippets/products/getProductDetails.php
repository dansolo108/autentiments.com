<?php
/** @var modX $modx */
/** @var array $scriptProperties */

$details = $modx->getOption('details', $scriptProperties, []);
$id = $modx->getOption('id', $scriptProperties, false);
$tpl = $modx->getOption('tpl', $scriptProperties, false);
$tplWrapper = $modx->getOption('tplWrapper', $scriptProperties, false);
if(is_string($details)){
    $details = explode(',',$details);
}
if(count($details) == 0 || $id === false)
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
    if(is_numeric($detail))
        $c = $detail;
    else if(is_string($detail))
        $c = ['name'=>$detail];
    /** @var DetailType $detailType */
    if($detailType = $modx->getObject('DetailType',$c)) {
        $tableName = 'Detail' . ucfirst($detail);
        $default = [
            'class' => 'msProduct',
            'where' => [
                'msProduct.class_key' => 'msProduct',
                'Modification.hide' => 0,
                'msProduct.id'=>$id,
            ],
            'leftJoin' => [
                'Modification' => [
                    'class' => 'Modification',
                    'on' => 'Modification.product_id = msProduct.id',
                ],
                $tableName => [
                    'class' => 'ModificationDetail',
                    'on' => "Modification.id = {$tableName}.modification_id AND {$tableName}.type_id = {$detailType->get('id')}"
                ]
            ],
            'select' => [
                $tableName => $tableName . '.value as value',
            ],
            'groupby' => implode(', ', ['msProduct.id','value']),
            'limit' => 10,
            'return' => 'data'
        ];
        $pdoFetch->setConfig(array_merge($default, $scriptProperties));
        $fetchResult = $pdoFetch->run();
        $output[$detail] = [];
        foreach ($fetchResult as &$item) {
            $item['idx'] = $pdoFetch->idx++;
            $item['detail'] = $detail;
            if($tpl){
                $item = $pdoFetch->parseChunk($tpl, array_merge(array_diff_key($scriptProperties,$default),$item));
            }
            $output[$detail][] = $item;
        }
        if($tpl && $tplWrapper){
            $output[$detail] = $pdoFetch->parseChunk($tplWrapper, ['output'=>implode('',$output[$detail])]);
        }else if($tpl){
            $output[$detail] = implode('',$output[$detail]);
        }
    }
}

if($tpl){
    if(count($details) == 1){
        return $output[array_keys($output)[0]];
    }
    if($tplWrapper){
        return implode('',$output);
    }
    return $output;
}else{
    if(count($details) == 1){
        return $output[array_keys($output)[0]];
    }
    return $output;
}

