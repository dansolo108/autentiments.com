<?php

if (!$amo = $modx->getService('amocrm', 'amoCRM', $modx->getOption('amocrm_core_path', null,
        $modx->getOption('core_path') . 'components/amocrm/') . 'model/amocrm/', array())
) {
    return 'Could not load amoCRM class!';
}
$ms2 = $modx->getService('minishop2');
$ids = array();
$q = $modx->newQuery('amoCRMLead');
$q->leftJoin('msOrder', 'msOrder', 'amoCRMLead.order = msOrder.id');
$q->where(array('msOrder.createdon:>=' => '2019-01-31'));
$q->select('amoCRMLead.order_id as id');
$q->prepare();
$q->stmt->execute();
$links = $q->stmt->fetchAll(PDO::FETCH_NAMED);
foreach ($links as $l) {
    $ids[] = $l['id'];
}
// echo print_r($ids, 1);

$amoLeads = $amo->getLeads($ids);
// echo print_r(array_keys($amoLeads), 1);
$deleted = array_diff($ids, array_keys($amoLeads));
// echo print_r($deleted, 1);

$ids = array();
$q = $modx->newQuery('msOrder');
$q->leftJoin('amoCRMLead', 'amoCRMLead', 'amoCRMLead.order = msOrder.id');
$q->where(array('amoCRMLead.order_id:IN' => $deleted));
$q->select('amoCRMLead.order as id');
$q->prepare();
$q->stmt->execute();
$links = $q->stmt->fetchAll(PDO::FETCH_NAMED);
foreach ($links as $l) {
    $ids[] = $l['id'];
}
echo print_r($ids, 1);