<?php

/**
 * @var amoCRM $amo
 * @var modX $modx
 */
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
$modx->error->message = null; // Обнуляем переменную

error_reporting(E_ALL);


if (!$amoBase = $modx->getService('amocrm', 'amoCRM', $modx->getOption('amocrm_core_path', null,
        $modx->getOption('core_path') . 'components/amocrm/') . 'model/amocrm/', array())
) {
    return 'Could not load amoCRM class!';
}
/** @var simpleQueue $simpleQueue */
if (!$simpleQueue = $modx->getService('simplequeue', 'simpleQueue', $modx->getOption('simplequeue_core_path', null,
        $modx->getOption('core_path') . 'components/simplequeue/') . 'model/simplequeue/', array())
) {
    return 'Could not load simpleQueue class!';
}

while (true) {

    $result = false;
//    echo 'NEW ROUND. TIME: ' . time() . PHP_EOL;
    $q = $modx->newQuery('sqMessage');
    $q->where(array('service' => amoCRMTools::SQ_SERVICE, 'processed' => 0, 'status:<' => 5));
    $q->sortby('id');
    /** @var sqMessage $tasks */
    if ($task = $modx->getObject('sqMessage', $q)) {
        echo 'TASK ID: ' . $task->get('id') . ', ACTION: ' . $task->get('action') . PHP_EOL;
        $amoParams = array();
        $processed = false;
        $subject = $task->get('subject');
        $properties = $task->get('properties');

        if (isset($properties['amoCRMcomponentParams'])) {
            $amoParams = array_merge($amoBase->config, $properties['amoCRMcomponentParams']);
            unset($properties['amoCRMcomponentParams']);
        }

        $hash = md5($modx->toJSON($amoParams));
        $serviceName = 'amocrm' . $hash;
        if (!$amo = $modx->getService($serviceName, 'amoCRM', $modx->getOption('amocrm_core_path', null,
                $modx->getOption('core_path') . 'components/amocrm/') . 'model/amocrm/', $amoParams)
        ) {
            return 'Could not load amoCRM class!';
        }

        try {

            $amo->auth();

            switch ($task->get('action')) {

                case amoCRMTools::SQ_ACTION_ADD_FORM:
                    $processed = (bool)$amo->addForm($properties);
                    break;

                case amoCRMTools::SQ_ACTION_ADD_ORDER:
                    if (!$amo->tools->getMS2()) {
                        $processed = true;
                        break;
                    }
                    /** @var msOrder $order */
                    if ($order = $modx->getObject('msOrder', $subject)) {
                        $processed = (bool)$amo->addOrder($order);
                    } else {
                        $processed = true;
                    }
                    break;

                case amoCRMTools::SQ_ACTION_ADD_CONTACT:
                    $processed = (bool)$amo->addContact($properties['userData'], $properties['modUserId'],
                        $properties['leads']);
                    break;

                case amoCRMTools::SQ_ACTION_CHANGE_ORDER_STATUS:
                    if (!$amo->tools->getMS2()) {
                        $processed = true;
                        break;
                    }
                    /** @var msOrder $order */
                    if ($msOrder = $modx->getObject('msOrder', $subject)) {

                        if ($modx->getOption('amocrm_update_order_on_change_status')) {
                            $processed = (bool)$amo->addOrder($msOrder);
                        } else {
                            $processed = (bool)$amo->leadsController->changeOrderStatusInAmo($properties['ms2OrderId'],
                                $properties['ms2StatusId']);
                            // if (isset($processed['success']) && $processed['success'] == true) {
                            //     $processed = true;
                            // } else {
                            //     $processed = false;
                            // }
                        }
                    } else {
                        $processed = true;
                    }
                    break;

                case amoCRMTools::SQ_ACTION_WEBHOOK_LEADS_STATUSES:
                    $amo->setWebhookMode(true);
                    $amoBase->setWebhookMode(true);
                    if ($wh = $amoBase->tools->getWebhook()) {
                        sleep(amoCRMTools::WEBHOOK_DELAY_FOR_REPEAT);
                        $q = $modx->newQuery('sqMessage');
                        $q->sortby('id', 'ASC');
                        $q->where(array(
                            'service' => amoCRMTools::SQ_SERVICE,
                            'action' => $task->get('action'),
                            'subject' => $subject,
                            'processed' => 0
                        ));
                        /** @var sqMessage[] $leadTasks */
                        $leadTasks = $modx->getCollection('sqMessage', $q);
                        $leadTask = array_shift($leadTasks);
                        $properties = $leadTask->get('properties');
                        $processed = (boolean)$amo->leadsController->changeOrderStatusInMS2($properties['id'], $properties['status_id']);
                        foreach ($leadTasks as $lt) {
                            $lt->set('processed', $processed);
                            $lt->save();
                        }
                    } else {
                        $processed = true;
                    }
                    break;

                case amoCRMTools::SQ_ACTION_WEBHOOK_UPDATE_USER:
                    $amo->setWebhookMode(true);
                    $amoBase->setWebhookMode(true);
                    if ($wh = $amoBase->tools->getWebhook()) {
                        sleep(amoCRMTools::WEBHOOK_DELAY_FOR_REPEAT);
                        $q = $modx->newQuery('sqMessage');
                        $q->sortby('id', 'ASC');
                        $q->where(array(
                            'service' => amoCRMTools::SQ_SERVICE,
                            'action' => $task->get('action'),
                            'subject' => $subject,
                            'processed' => 0
                        ));
                        /** @var sqMessage[] $contactTasks */
                        $contactTasks = $modx->getCollection('sqMessage', $q);
                        $contactTask = array_shift($contactTasks);
                        $properties = $contactTask->get('properties');
                        $processed = (boolean)$amo->contactsController->updateUserInMODX($properties);;
                        foreach ($contactTasks as $ct) {
                            $ct->set('processed', $processed);
                            $ct->save();
                        }
                    } else {
                        $processed = true;
                    }
                    break;

                default:
                    break;

            }
            unset($modx->services[$serviceName]);
            unset($modx->$serviceName);
        } catch (Exception $ex) {

            $trace = explode("\n", $ex->getTraceAsString());
            // reverse array to make steps line up chronologically
            $trace = array_reverse($trace);
            array_shift($trace); // remove {main}
            array_pop($trace); // remove call to this method
            $length = count($trace);
            $result = array();

            for ($i = 0; $i < $length; $i++) {
                $result[] = ($i + 1) . ')' . substr($trace[$i],
                        strpos($trace[$i], ' ')); // replace '#someNum' with '$i)', set the right ordering
            }

            $trace = implode("\n", $result);

        }

        if ($processed === true) {
            $response = $modx->runProcessor(
                'message/close',
                array('id' => $task->get('id')),
                array('processors_path' => $simpleQueue->config['processorsPath'])
            );
            $modx->error->message = null; // Обнуляем переменную
        } else {
            $task->set('status', $task->get('status') + 1);
            $task->save();
        }

    }

    ob_flush();

    if (date('i') % 2 == 0 and date('s') == '58') {
        exit();
    }
    sleep(1);

}
