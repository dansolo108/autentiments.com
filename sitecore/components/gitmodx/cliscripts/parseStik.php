<?php
define('MODX_API_MODE', true);
require_once dirname(__FILE__, 5) . '/index.php';
/** @var $modx gitModx */
$modx->setLogTarget('ECHO');
$modx->setLogLevel(MODX_LOG_LEVEL_DEBUG);
/** @var pdoFetch $pdoFetch */
$fqn = $modx->getOption('pdoFetch.class', null, 'pdotools.pdofetch', true);
$path = $modx->getOption('pdofetch_class_path', null, MODX_CORE_PATH . 'components/pdotools/model/', true);
if ($pdoClass = $modx->loadClass($fqn, $path, false, true)) {
    $pdoFetch = new $pdoClass($modx);
} else {
    return false;
}
$fqn = $modx->getOption('pdoFetch.class', null, 'pdotools.pdofetch', true);
$path = $modx->getOption('pdofetch_class_path', null, MODX_CORE_PATH . 'components/pdotools/model/', true);
if ($pdoClass = $modx->loadClass($fqn, $path, false, true)) {
    $pdoFetch = new $pdoClass($modx);
} else {
    return false;
}
$pdoFetch->setConfig([
    'class' => 'stikRemains',
    'where'=>'stikRemains.store_id = 1',
    'leftJoin' => [
        'stikRemains2' => [
            'class' => 'stikRemains',
            'on' => 'stikRemains.product_id = stikRemains2.product_id AND stikRemains.color = stikRemains2.color AND stikRemains.size = stikRemains2.size AND stikRemains2.store_id = 2'
        ]
    ],
    'limit' => 0,
    'select' => [
        $modx->getSelectColumns('stikRemains', 'stikRemains'),
        'stikRemains2.remains as remainsMSC',
        'stikRemains.remains as remainsSPB',
    ],
    'groupby' => 'stikRemains.product_id,stikRemains.color,stikRemains.size',
    'return' => 'data'
]);
$stikRemains = $pdoFetch->run();
foreach($stikRemains as $stikRemain){
    $stikRemains = $pdoFetch->run();
    /** @var Modification $modification */
    $modification = $modx->newObject('Modification');
    $modification->fromArray($stikRemain);
    $modification->set('code','');
    /** @var ModificationRemain $remainsSpb */
    $remainsSpb = $modx->newObject('ModificationRemain');
    $remainsSpb->fromArray(['store_id'=>1,'remains'=>$stikRemain['remainsSPB']]);
    $modification->addMany($remainsSpb);
    /** @var ModificationRemain $remainsMsc */
    $remainsMsc = $modx->newObject('ModificationRemain');
    $remainsMsc->fromArray(['store_id'=>2,'remains'=>$stikRemain['remainsMSC']]);
    $modification->addMany($remainsMsc);
    /** @var ModificationDetail $color */
    $color = $modx->newObject('ModificationDetail');
    $color->set('type_id','1');
    $color->set('value',$stikRemain['color']);
    $modification->addMany($color);
    /** @var ModificationDetail $size */
    $size = $modx->newObject('ModificationDetail');
    $size->set('type_id','2');
    $size->set('value',$stikRemain['size']);
    $modification->addMany($size);
    if(!$modification->save()){
        logError($modification);
        continue;
    }
}
function logError($obj){
    global $modx;
    $modx->log(MODX_LOG_LEVEL_ERROR,'error '.get_class($obj).' values: \n'.var_export($obj->toArray(),1));
}