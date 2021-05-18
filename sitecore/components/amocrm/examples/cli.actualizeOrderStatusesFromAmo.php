<?php

$startTime = microtime(true);

define('MODX_API_MODE', true);
define('AMOCRM_WEBHOOK_MODE', true);

$dir = dirname(__FILE__);
$subdirs = array('', 'www');
$subdir = '';

for ($i = 0; $i <= 10; $i++) {
    foreach ($subdirs as $subdir) {
        $path = $dir . '/' . $subdir;
        if (file_exists($path) and file_exists($path . 'index.php')) {
            require_once $path . 'index.php';
            break 2;
        }
    }
    $dir = dirname($dir . '/');
}

// Включаем обработку ошибок
$modx->getService('error', 'error.modError');
$modx->setLogLevel(modX::LOG_LEVEL_ERROR);
$modx->setLogTarget('FILE');
//$modx->setLogTarget(XPDO_CLI_MODE ? 'ECHO' : 'HTML');
$modx->error->message = null; // Обнуляем переменную
/** @var miniShop2 $ms2 */
$ms2 = $modx->getService('minishop2');

error_reporting(E_ALL);
$err = "";

/**
 * @param modX $modx
 * @param $limit
 * @param $offset
 *
 * @return msOrder[]|null
 */
function getMsOrders(modX $modx, $limit, $offset)
{
    $start = date('Y-m-d H:i:s', strtotime('-20 days'));
    $q = $modx->newQuery('msOrder');
    $q->leftJoin('amoCRMLead', 'amoCRMLead', 'msOrder.id = amoCRMLead.order');
    $q->limit($limit, $offset);
    $q->select('msOrder.*, amoCRMLead.order_id as lead_id');
    $q->where(array('msOrder.createdon:>' => $start));
    $q->prepare();
    return $modx->getCollection('msOrder', $q);
}

/**
 * @param modX $modx
 * @param $limit
 * @param $offset
 *
 * @return msOrder[]|null
 */
function getStatuses(modX $modx)
{
    $statuses = [];
    $q = $modx->newQuery('amoCRMOrderStatus');
    $q->select('amoCRMOrderStatus.status as ms_status, amoCRMOrderStatus.status_id as amo_status');
    $q->prepare();
    $q->stmt->execute();
    $tmp = $q->stmt->fetchAll(PDO::FETCH_ASSOC);
    foreach ($tmp as $status) {
        $statuses[ $status['amo_status'] ] = $status;
    }
    return $statuses;
}

function echoLog($msg)
{
    echo $msg;
    ob_flush();
}


/** @var amoCRM $amo */
if (!$amo = $modx->getService('amocrm', 'amoCRM', $modx->getOption('amocrm_core_path', null,
        $modx->getOption('core_path') . 'components/amocrm/') . 'model/amocrm/', array())
) {
    return 'Could not load amoCRM class!';
}

$limit = 500;
$offset = 0;
$stop = 1000000;
$round = 1;
$brokenStatusesCount = 0;
$statuses = getStatuses($modx);

while ($offset < $stop and $orders = getMsOrders($modx, $limit, $offset)) {

    $ordersNormal = [];
    echoLog(PHP_EOL . PHP_EOL . '[NEW ROUND ' . $round . '] [' . date('H:i:s') . '] OFFSET: ' . $offset . ', COUNT ORDERS: ' . count($orders) . PHP_EOL. PHP_EOL);

    foreach ($orders as $order) {
        /** @var msOrder[] $ordersNormal */
        $ordersNormal[ $order->get('lead_id') ] = $order;
    }

    $amoIds = array_keys($ordersNormal);
    echoLog('[' . date('H:i:s') . '] LEADS FROM AMO. REQUESTED: ' . count($amoIds) .PHP_EOL);
    $amoLeads = $amo->getLeads($amoIds);
    echoLog('[' . date('H:i:s') . '] LEADS FROM AMO. RECEIVED: ' . count($amoLeads) . PHP_EOL);

    foreach ($amoLeads as $amoLead) {
//        echoLog('AMO LEAD ID ' . $amoLead['id'] . ' ISSET? ' . isset($ordersNormal[ $amoLead['id'] ])
//            . ', AMO STATUS ' . $amoLead['status_id'] . ' ISSET? ' . isset($statuses[ $amoLead['status_id'] ])
//            . ' MS ORDER STATUS: ' . $ordersNormal[ $amoLead['id'] ]->get('status') . PHP_EOL);
        if (isset($ordersNormal[ $amoLead['id'] ])
            and isset($statuses[ $amoLead['status_id'] ])
            and $ordersNormal[ $amoLead['id'] ]->get('status') != $statuses[ $amoLead['status_id'] ]['ms_status']
        ) {
            $orderId = $ordersNormal[ $amoLead['id'] ]->get('id');
            $msStatus = $ordersNormal[ $amoLead['id'] ]->get('status');
            $newMsStatus = $statuses[ $amoLead['status_id'] ]['ms_status'];
            $amoStatus = $statuses[ $amoLead['status_id'] ]['ms_status'];
            $ms2->changeOrderStatus($orderId, $newMsStatus);
            echoLog('ORDER ID: ' . $orderId . ', STATUS ID: ' . $msStatus . ', NEW STATUS: ' . $newMsStatus . PHP_EOL);
            $brokenStatusesCount++;
        }

    }

    $offset += $limit;
    $round++;
    usleep(100000);
}



echoLog(PHP_EOL . PHP_EOL);
echoLog('[ROUNDS] ' . ($round - 1) . PHP_EOL);
echoLog('[COUNT BROKEN] ' . $brokenStatusesCount . PHP_EOL);
echoLog('[MAX MEMORY] ' . round(memory_get_peak_usage() / 1024 / 1024, 3) . 'M' . PHP_EOL);
echoLog('[TIME] ' . round(microtime(true) - $startTime, 3) . ' s' . PHP_EOL);
echoLog(PHP_EOL . PHP_EOL);