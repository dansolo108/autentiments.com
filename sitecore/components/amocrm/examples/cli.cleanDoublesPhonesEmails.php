<?php
/**
 * Created by PhpStorm.
 * User: mvoevodskiy
 * Date: 25.02.19
 * Time: 0:44
 */

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

$queryPhone = '';
/**
 * Список ID контактов amoCRM, которые необходимо пропустить
 */
$skipIds = array();
/**
 * Список полей, которые очищаются от дублей
 */
$cleanFields = array(1014757, 1014759);
/**
 * Список полей, которые необходимо привести к каноническому виду российского мобильного номера телефона
 */
$phoneFields = array(1014757);

$maxRounds = 100;
$round = 1;
$limit = 500;
$offset = 0;

$contacts = $amo->getContacts(array(), $queryPhone, array(), $limit, $offset);
//file_put_contents('contacts_before_del_doubles_' . $offset , '-' . ($offset+$limit) . '.json', $modx->toJSON($contacts));


$countUpdated = 0;
$countTotal = 0;
$i = 0;
while (!empty($contacts) and $round <= $maxRounds) {
    echoLog('FETCHED ' . count($contacts) . ' CONTACTS FROM AMO WITH OFFSET ' . $offset . PHP_EOL);
    $countTotal += count($contacts);
    foreach ($contacts as $contact) {
        $i++;
        echoLog('CONTACT ID: ' . $contact['id'] . '. ');
        if (in_array($contact['id'], $skipIds)) {
            echoLog('SKIPPING...' . PHP_EOL);
            continue;
        }
        echoLog(PHP_EOL);
        $changed = false;
        $logBefore = 'CUSTOM FIELDS BEFORE UNSET: ' . print_r($contact['custom_fields'], 1) . PHP_EOL;
//        echoLog($logBefore);
        foreach ($contact['custom_fields'] as & $cf) {
            $fId = $cf['id'];
            $rightValues = array();
            if (in_array($fId, $cleanFields)) {
                foreach ($cf['values'] as $k => & $v) {
                    $value = &$v['value'];
//                    $value = mb_strtolower($value);
                    if (in_array($fId, $phoneFields)) {
                        if (substr_count($value, '+') > 1) {
                            $value = substr($value, 0, strpos($value, '+', 2));
                        }
                        $value = $amo->tools->normalizeRusPhone($value);
                    }

                    if (in_array($fId, $phoneFields) and !empty($queryPhone) and $amo->tools->normalizeRusPhone($v['value']) == $queryPhone) {
                        unset($cf['values'][$k]);
                        $changed = true;
                    }

                    if (!in_array($value, $rightValues)) {
                        $rightValues[] = $value;
                    } else {
                        unset($cf['values'][$k]);
                        $changed = true;
                    }
                }
                $cf['values'] = array_values($cf['values']);
            }
        }
        if ($changed) {
            echoLog($logBefore);

            if (!empty($contact['tags'])) {
                $tags = '';
                foreach ($contact['tags'] as $tag) {
                    $tags .= $tag['name'] . ', ';
                }
                if (!empty($tags)) {
                    $contact['tags'] = $tags;
                }
            }

            echoLog('CONTACT AFTER UNSET: ' . print_r($contact, 1) . PHP_EOL);

            $data = array('update' => array($contact));
            $result = $amo->sendRequest('/api/v2/contacts', $data);
            echoLog('UPDATE RESULT: ' . print_r($result, 1) . PHP_EOL);
            $countUpdated++;
            usleep(100000);
        }
//    $i < 10 ? $i++ : exit();

    }
    $round++;

    $offset += count($contacts);
    $contacts = $amo->getContacts(array(), $queryPhone, array(), $limit, $offset);
//    file_put_contents('contacts_before_del_doubles_' . $offset , '-' . ($offset+$limit) . '.json', $modx->toJSON($contacts));

}

echoLog(PHP_EOL . PHP_EOL);
echoLog('[ROUNDS] ' . ($round - 1) . PHP_EOL);
echoLog('[COUNT TOTAL] ' . $countTotal . PHP_EOL);
echoLog('[COUNT UPDATED] ' . $countUpdated . PHP_EOL);
echoLog('[MAX MEMORY] ' . round(memory_get_peak_usage() / 1024 / 1024, 3) . 'M' . PHP_EOL);
echoLog('[TIME] ' . round(microtime(true) - $startTime, 3) . ' s' . PHP_EOL);
echoLog(PHP_EOL . PHP_EOL);