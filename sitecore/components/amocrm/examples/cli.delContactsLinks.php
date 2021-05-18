<?php

$startTime = microtime(true);

define('MODX_API_MODE', true);
require_once 'www/index.php';

// Включаем обработку ошибок
$modx->getService('error', 'error.modError');
$modx->setLogLevel(modX::LOG_LEVEL_FATAL);
$modx->setLogTarget(XPDO_CLI_MODE ? 'ECHO' : 'HTML');
$modx->error->message = null; // Обнуляем переменную

error_reporting(E_ALL);
$err = "";

function getAmoLinks(modX $modx, $limit, $offset)
{
    $q = $modx->newQuery('amoCRMUser');
    $q->limit($limit, $offset);
//    $q->prepare();
//    echo $q->toSQL() . PHP_EOL;
    return $modx->getCollection('amoCRMUser', $q);
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

$limit = 10;
$offset = 0;
$stop = 3000;
$round = 0;
$fileName = 'broken_links.txt';

$brokenLinks = file_get_contents($fileName);
$brokenLinks = explode(',', $brokenLinks);

/** @var amoCRMUser[] $links */
while ($offset < $stop and $links = getAmoLinks($modx, $limit, $offset)) {
    $round++;
    echoLog(PHP_EOL . PHP_EOL . '[NEW ROUND ' . $round . '] [' . date('H:i:s') . '] OFFSET: ' . $offset . ', COUNT LINKS: ' . count($links) . PHP_EOL. PHP_EOL);
    foreach ($links as $link) {
        echoLog($link->get('id') . '==' . $link->get('user_id') . '  ');
        $contacts = $amo->getContacts($link->get('user_id'));
        if (empty($contacts) or !$modx->getCount('modUser', $link->get('user'))) {
            echoLog(PHP_EOL . 'NOT FOUND AMO CONTACT: ' . $link->get('id') . '==' . $link->get('user_id') . PHP_EOL);
            $brokenLinks[] = $link->get('id');
        }
        usleep(1000);
    }
    $offset += $limit;
    file_put_contents($fileName, implode(',', $brokenLinks));
    sleep(1);
}

echoLog(PHP_EOL . PHP_EOL);
echoLog('[ROUNDS] ' . $round . PHP_EOL);
echoLog('[COUNT] ' . count($brokenLinks) . PHP_EOL);
echoLog('[NEXT OFFSET] ' . ($offset) . PHP_EOL);
echoLog('[TIME] ' . round(microtime(true) - $startTime, 3) . ' s' . PHP_EOL);
echoLog(PHP_EOL . PHP_EOL);