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
$where = [
    'Modification.hide'=>0,
    'msProduct.deleted'=>0,
    'msProduct.published'=>1,
];
$leftJoin = [
    'Modification'=>[
        'class'=>'Modification',
        'on'=>'Modification.product_id = msProduct.id',
    ],
    'Data'=>[
        'class'=>'msProductData',
        'on'=>'Modification.product_id = Data.id',
    ],
    'Remain'=>[
        'class'=>'ModificationRemain',
        'on'=>'Remain.modification_id = Modification.id'
    ],
    'Vendor' => [
        'class' =>'msVendor',
        'on' => 'Data.vendor=Vendor.id'
    ],
];
$select = [
    'Modification'=>$modx->getSelectColumns('Modification', 'Modification',''),
    'msProduct' => !empty($includeContent)
        ? $modx->getSelectColumns('msProduct', 'msProduct','',['id'],true)
        : $modx->getSelectColumns('msProduct', 'msProduct', '', ['id','content'], true),
    'Data' => $modx->getSelectColumns('msProductData', 'Data','',['id','price','old_price','color','size','image'],true),
    'Remain'=>'SUM(Remain.remains) as remains',
    'Vendor' => 'Vendor.name as vendor',
];
$groupby = [
  'Modification.id'
];
if(is_string($scriptProperties['groupby'])){
    $scriptProperties['groupby'] = explode(',',$scriptProperties['groupby']);
}
// Include linked products
$innerJoin = array();
if (!empty($link) && !empty($master)) {
    $innerJoin['Link'] = [
        'class' => 'msProductLink',
        'on' => 'msProduct.id = Link.slave AND Link.link = ' . $link,
    ];
    $where['Link.master'] = $master;
} elseif (!empty($link) && !empty($slave)) {
    $innerJoin['Link'] = [
        'class' => 'msProductLink',
        'on' => 'msProduct.id = Link.master AND Link.link = ' . $link,
    ];
    $where['Link.slave'] = $slave;
}

foreach($details as $key=>$detail){
    /** @var DetailType $detailType */
    $detailType =  $modx->getObject('DetailType',['name'=>$detail]);
    if($detailType){
        $detailTable = 'Detail'.ucfirst($detail);
        $leftJoin[$detailTable]= [
            'class'=>'ModificationDetail',
            'on'=>"Modification.id = {$detailTable}.modification_id AND {$detailTable}.type_id = '{$detailType->get('id')}'"
        ];
        $select[$detailTable] = $detailTable.'.value as '.$detail;
        foreach (['where','having'] as $item) {
            if(!empty($scriptProperties[$item]))
                foreach ($scriptProperties[$item] as $key => $value) {
                    $tmp = explode(':', $key);
                    switch (count($tmp)) {
                        case 3:
                            $varName = $tmp[1];
                            break;
                        case 2:
                            if ($tmp[0] === 'AND' || $tmp[0] === 'OR') {
                                $varName = $tmp[1];
                                break;
                            }
                        default:
                            $varName = $tmp[0];
                            break;
                    }
                    if ($varName === $detail) {
                        $scriptProperties[$item][str_replace($detail, $detailTable . '.value', $key)] = $value;
                        unset($scriptProperties[$item][$key]);
                    }
                }
        }
        if(!empty($scriptProperties['select']))
            foreach ($scriptProperties['select'] as $key => $value) {
                if (is_numeric($key) && $detail === $value) {
                    $scriptProperties['select'][$key] = $detailTable . '.value as ' . $detail;
                }
            }
        if(!empty($scriptProperties['groupby']))
            foreach ($scriptProperties['groupby'] as $key => $value) {
                if (is_numeric($key) && $detail === $value) {
                    $scriptProperties['groupby'][$key] = $detailTable . '.value';
                }
            }

    }
    else{
        if($modx->hasPermission(['edit_chunk','edit_template'])){
            return $detail.' не найден в базе данных';
        }
    }
}
if(is_array($scriptProperties['groupby'])){
    $scriptProperties['groupby'] = implode(',',$scriptProperties['groupby']);
}
if(!empty($scriptProperties['sortby']['size'])){
    $sortDir = $scriptProperties['sortby']['size'];
    $sizes = ['XS/S','M/L','L/XL','XXS','XS','S','M','L','XL','XXL'];
    unset($scriptProperties['sortby']['size']);
    $scriptProperties['sortby']['FIELD(DetailSize.value,"'.implode('","',$sizes).'")'] = $sortDir;
}
$default = [
    'class'=>'msProduct',
    'where'=>$where,
    'leftJoin'=>$leftJoin,
    'innerJoin'=>$innerJoin,
    'select'=>$select,
    'sortby'=>['Modification.id','Modification.sort_index'=>"DESC"],
    'groupby' => implode(', ', $groupby),
    'limit'=> 10,
    'return'=>'data',
    'polyLang'=>false,
];
$pdoFetch->setConfig(array_merge($default,$scriptProperties));
$result = $pdoFetch->run();
if($pdoFetch->config['return'] === 'sql')
    return $result;
$output = '';
$polylang = $modx->getService('polylang', 'Polylang');
$polylangTools = $polylang->getTools();
foreach ($result as $key => &$item){
    $item['idx'] = $pdoFetch->idx++;
    $options = array(
        'class' => $pdoFetch->config['class'],
        'tvPrefix' => !empty($pdoFetch->config['tvPrefix']) ? trim($pdoFetch->config['tvPrefix']) : '',
        'includeTVs' => !empty($pdoFetch->config['includeTVs']) ? $pdoFetch->config['includeTVs'] : '',
        'content_id'=>(int) $item['product_id'],
    );
    $polylangTools->prepareResourceData(function ($key, $value, $context) use (&$item,&$modx) {
        if($key === "size")
            return;
        $item['polylang_original_' . $key] = $item[$key];
        $item[$key] = $value;
    }, $options);
    if($item['product_id'] && $item['color'] && !empty($includeThumbs)){
        $thumbs = array_map('trim', explode(',', $includeThumbs));
        $leftJoin = [];
        $select = [];
        foreach ($thumbs as $thumb) {
            $leftJoin[$thumb] = [
                'class' => 'msProductFile',
                'on' => "msProductFile.id = `{$thumb}`.parent AND `{$thumb}`.path LIKE '%/{$thumb}/%'",
            ];
            $select[$thumb] = "`{$thumb}`.url as '{$thumb}'";
        }
        $default = [
            'class'=>'msProductFile',
            'where'=>[
                'product_id'=>$item['product_id'],
                'description'=> $item['color'],
                'active'=>true,
            ],
            'leftJoin'=>$leftJoin,
            'select'=>$select,
            'groupby'=>'msProductFile.name',
            'sortby'=>['rank'=>'ASC'],
            'limit'=>0,
            'return'=>'data'
        ];
        $pdoFetch->setConfig($default,false);
        $thumbs = $pdoFetch->run();
        if(count($thumbs)){
            $item['thumbs'] = $thumbs;
        }
    }
    if(!empty($tpl)) {
        $output .= $pdoFetch->getChunk($tpl, array_merge(array_diff_key($scriptProperties,$default),$item));
    }
}
if(!empty($tpl)){
    return $output;
}else{
    return $result;
}

