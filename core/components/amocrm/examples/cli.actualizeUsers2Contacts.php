<?php

$startTime = microtime(true);

define('MODX_API_MODE', true);
require_once 'www/index.php';

// Включаем обработку ошибок
$modx->getService('error', 'error.modError');
$modx->setLogLevel(modX::LOG_LEVEL_ERROR);
$modx->setLogTarget('FILE');
//$modx->setLogTarget(XPDO_CLI_MODE ? 'ECHO' : 'HTML');
$modx->error->message = null; // Обнуляем переменную

error_reporting(E_ALL);
$err = "";

function getAmoLinks(modX $modx, $limit, $offset)
{
    $q = $modx->newQuery('amoCRMUser');
    $q->limit($limit, $offset);
    $q->select('amoCRMUser.id as id, amoCRMUser.user as user, amoCRMUser.user_id as user_id');
    $q->prepare();
    $q->stmt->execute();
    return $q->stmt->fetchAll(PDO::FETCH_ASSOC);
}

function removeAloneLinks(modX $modx, $ids = array())
{
    if (empty($ids)) {
        $q = $modx->newQuery('amoCRMUser');
        $q->leftJoin('modUserProfile', 'modUserProfile', 'amoCRMUser.user = modUserProfile.internalKey');
        $q->where(array('modUserProfile.id IS null'));
        $q->select('amoCRMUser.id as id');
        $q->prepare();
        $q->stmt->execute();
        $links = $q->stmt->fetchAll(PDO::FETCH_NAMED);
        foreach ($links as $l) {
            $ids[] = $l['id'];
        }
    }
    if (!empty($ids)) {
        return $modx->removeCollection('amoCRMUser', array('id:IN' => $ids));
    }
    return true;
}

function getAloneProfiles(modX $modx, $limit, $offset)
{
    $q = $modx->newQuery('modUserProfile');
    $q->leftJoin('amoCRMUser', 'amoCRMUser', 'amoCRMUser.user = modUserProfile.internalKey');
    $q->where(array('amoCRMUser.user IS null'));
    $q->limit($limit, $offset);
    $q->select(array('modUserProfile' => '*'));
    $q->prepare();
    $q->stmt->execute();
    return $q->stmt->fetchAll(PDO::FETCH_ASSOC);
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
$brokenLinksCount = 0;

removeAloneLinks($modx);

/** @var array $links */
while ($offset < $stop and $links = getAmoLinks($modx, $limit, $offset)) {

    echoLog(PHP_EOL . PHP_EOL . '[NEW ROUND ' . $round . '] [' . date('H:i:s') . '] OFFSET: ' . $offset . ', COUNT LINKS: ' . count($links) . PHP_EOL. PHP_EOL);
    $linksNormal = array();
    $brokenLinks = array();

    foreach ($links as $link) {
        $linksNormal[ $link['user_id'] ] = $link;
    }

    $amoIds = array_keys($linksNormal);
    echoLog('[' . date('H:i:s') . '] CONTACTS FROM AMO. REQUESTED: ' . count($amoIds) .PHP_EOL);
    $amoUsers = $amo->getContacts($amoIds);
    echoLog('[' . date('H:i:s') . '] CONTACTS FROM AMO. RECEIVED: ' . count($amoUsers) . PHP_EOL);

    foreach ($amoUsers as $amoUser) {
        unset($linksNormal[ $amoUser['id'] ]);
        }

    foreach ($linksNormal as $brokenLink) {
        $brokenLinks[] = $brokenLink['id'];
    }

    if (!empty($brokenLinks)) {
        echoLog('[' . date('H:i:s') . '] REMOVING BROKEN LINKS TO AMO CONTACTS (ids):  ' . implode(', ', $brokenLinks) . PHP_EOL);
        removeAloneLinks($modx, $brokenLinks);
        $brokenLinksCount += count($brokenLinks);
//        $offset -= count($brokenLinks);
    }

    $offset += count($amoUsers);
    $round++;
    usleep(100000);
}

echoLog(PHP_EOL . PHP_EOL . '[ADDING ALONE PROFILES]' . PHP_EOL. PHP_EOL);


$offset = 0;
$round = 1;
$limit = 200;
$prevCount = 0;
$failRound = 0;
$addedProfiles = 0;
/** @var modUserProfile[] $profiles */
while($profiles = getAloneProfiles($modx, $limit, $offset) and $failRound < 1) {
    $currentCount = count($profiles);
    echoLog(PHP_EOL . PHP_EOL . '[NEW ROUND ' . $round . '] [' . date('H:i:s') . '] OFFSET: ' . $offset . ', COUNT PROFILES: ' . $currentCount . PHP_EOL. PHP_EOL);

    if ($prevCount < $limit and $prevCount == $currentCount) {
        $failRound++;
    } else {
        $prevCount = $currentCount;
        $failRound = 0;
    }

    foreach ($profiles as $profile) {
        if (filter_var($profile['email'], FILTER_VALIDATE_EMAIL) !== false) {
            echoLog('ADDING CONTACT WITH EMAIL: ' . $profile['email'] . PHP_EOL);
            $amoId = $amo->addContact(
                array(
                    'name' => $profile['fullname'],
                    'email' => $profile['email'],
                    'phone' => $profile['phone'],
                ),
                $profile['internalKey']);
            if ($amoId) {
                $addedProfiles++;
            }
            usleep(100000);
        }
    }
    $round++;
}

echoLog(PHP_EOL . PHP_EOL);
echoLog('[ROUNDS] ' . ($round - 1) . PHP_EOL);
echoLog('[COUNT BROKEN] ' . $brokenLinksCount . PHP_EOL);
echoLog('[ADDED PROFILES] ' . $addedProfiles . PHP_EOL);
echoLog('[MAX MEMORY] ' . round(memory_get_peak_usage() / 1024 / 1024, 3) . 'M' . PHP_EOL);
echoLog('[TIME] ' . round(microtime(true) - $startTime, 3) . ' s' . PHP_EOL);
echoLog(PHP_EOL . PHP_EOL);