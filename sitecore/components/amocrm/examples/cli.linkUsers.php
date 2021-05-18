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

function getProfiles(modX $modx, $limit = 10, $offset = 0)
{
    $q = $modx->newQuery('modUserProfile');
    $q->leftJoin('amoCRMUser', 'amoCRMUser', 'amoCRMUser.user = modUserProfile.internalKey');
    $q->where(array('amoCRMUser.user IS null'));
    $q->limit($limit, $offset);
    $q->prepare();
// echo $q->toSQL() . PHP_EOL;

    /** @var modUserProfile[] $profiles */
    return $modx->getCollection('modUserProfile', $q);
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
$rounds = 1;
$round = 0;
$fileName = 'broken_links.txt';

//$brokenLinks = file_get_contents($fileName);
//$brokenLinks = explode(',', $brokenLinks);

/** @var modUserProfile[] $profiles */
while ($round < $rounds and $profiles = getProfiles($modx, $limit)) {
    $round++;
    echoLog(PHP_EOL . PHP_EOL . '[NEW ROUND ' . $round . '] [' . date('H:i:s') . '] COUNT PROFILES: ' . count($profiles) . PHP_EOL. PHP_EOL);
    foreach ($profiles as $profile) {
        echoLog($profile->get('internalKey') . PHP_EOL);
        echo $profile->get('email');
        $amo->addContact(
            array(
                'name' => $profile->get('fullname'),
                'email' => $profile->get('email'),
                'phone' => $profile->get('phone')
            ),
            $profile->get('internalKey'));
        usleep(1000);
    }
    sleep(1);
}

echoLog(PHP_EOL . PHP_EOL);
echoLog('[ROUNDS] ' . $round . PHP_EOL);
//echoLog('[COUNT] ' . count($brokenLinks) . PHP_EOL);
echoLog('[TIME] ' . round(microtime(true) - $startTime, 3) . ' s' . PHP_EOL);
echoLog(PHP_EOL . PHP_EOL);