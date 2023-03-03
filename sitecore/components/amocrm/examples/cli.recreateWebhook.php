<?php

$startTime = microtime(true);

define('MODX_API_MODE', true);

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

error_reporting(E_ALL);
$err = "";

function echoLog($msg)
{
    global $printLog;
    if ($printLog) {
        echo $msg;
        ob_flush();
    }
}

/** @var amoCRM $amo */
if (!$amo = $modx->getService('amocrm', 'amoCRM', $modx->getOption('amocrm_core_path', null,
        $modx->getOption('core_path') . 'components/amocrm/') . 'model/amocrm/', array())
) {
    return 'Could not load amoCRM class!';
}

if (!$webhookHandler = $amo->tools->getWebhook()) {
    return 'Could not load webhook handler';
}

$urls = array(
    MODX_SITE_URL . 'assets/components/amocrm/webhook.php',
);

$arguments = $argv;
array_shift($arguments);
$printLog = (bool) array_shift($arguments);

if (!empty($arguments)) {
    $urls = $arguments;
}
$needCheck = false;
//$events = array(
//    amoCRMWebhook::EVENT_UPDATE_LEAD,
//    amoCRMWebhook::EVENT_STATUS_LEAD,
//);

$webhooks = $webhookHandler->getWebhookFromAmo($urls);
echoLog('INITIAL FETCH WEBHOOKS: ' . print_r($webhooks, 1) . PHP_EOL);

foreach ($webhooks as $webhook) {
    echoLog('WEBHOOK ' . $webhook['url'] . ' IS ' . ($webhook['disabled'] ? 'DISABLED. ENABLING' : 'ENABLED. SKIPPING') . PHP_EOL);
    if ($webhook['disabled']) {

        $unsubResult = $webhookHandler->unsubscribeWebhookInAmo($webhook['url'], $webhook['events']);
        echoLog('UNSUBSCRIBE RESULT: ' . print_r($unsubResult, 1) . PHP_EOL);

        $subResult = $webhookHandler->subscribeWebhookInAmo($webhook['url'], $webhook['events']);
        echoLog('SUBSCRIBE RESULT: ' . print_r($subResult, 1) . PHP_EOL);

        $needCheck = true;
    }
}
if ($needCheck) {
    $webhooks = $webhookHandler->getWebhookFromAmo($urls);
    echoLog('CONTROL FETCH WEBHOOK: ' . print_r($webhooks, 1) . PHP_EOL);
}

echoLog(PHP_EOL . PHP_EOL);
echoLog('[TIME] ' . round(microtime(true) - $startTime, 3) . ' s' . PHP_EOL);
echoLog(PHP_EOL . PHP_EOL);