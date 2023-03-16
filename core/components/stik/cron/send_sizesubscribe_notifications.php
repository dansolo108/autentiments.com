<?php
// Connect
define('MODX_API_MODE', true);
require_once dirname(dirname(dirname(dirname(__FILE__)))) . '/config/config.inc.php';
require_once MODX_BASE_PATH . 'index.php';

// Load main services
$modx->setLogTarget(XPDO_CLI_MODE ? 'ECHO' : 'HTML');
$modx->setLogLevel($is_debug ? modX::LOG_LEVEL_INFO : modX::LOG_LEVEL_ERROR);
$modx->getService('error','error.modError');
$modx->lexicon->load('minishop2:default');
$modx->lexicon->load('minishop2:manager');

// Time limit
set_time_limit(600);
$tmp = 'Trying to set time limit = 600 sec: ';
$tmp .= ini_get('max_execution_time') == 600 ? 'done' : 'error';
$modx->log(modX::LOG_LEVEL_INFO,  $tmp);

if (XPDO_CLI_MODE) {
	$update_old = @$argv[1];
}

$stik = $modx->getService('stik', 'stik', MODX_CORE_PATH . 'components/stik/model/', []);
if (!$stik) {
    return 'Could not load stik class!';
}

$where = [
    'status' => 0,
    'active' => 1
];

if ($update_old == 'old') {
    $where['createdon:<='] = date('Y-m-d h:i:s', strtotime('-3 month'));
} else {
    $where['createdon:>='] = date('Y-m-d h:i:s', strtotime('-3 month'));
}

$objects = $modx->getIterator('stikSizesubscriber', $where);

$success = 0;
$errors = 0;

foreach ($objects as $object) {
	$remains = $modx->getObject('stikRemains', array(
		'product_id' => $object->get('product_id'),
		'store_id' => 1,
		'size' => $object->get('size'),
		'color' => $object->get('color'),
		'remains:>' => 0,
	));
    
    if ($remains) {
        foreach($remains as $remain) {
            $q = $modx->newQuery('msProduct');
            $q->leftJoin('msProductData', 'Data');
            $q->where([
                'msProduct.id' => $object->get('product_id'),
                'msProduct.published' => 1,
                'msProduct.deleted:!=' => 1,
                'Data.soon:!=' => 1, // чекбокс Скоро
            ]);
            $product = $modx->getObject('msProduct', $q);
            if ($product) {
                if ($object->get('email')) {
                    $modx->setOption('cultureKey', $object->get('language'));
                    $modx->lexicon->load($object->get('language') . ':polylang:site');
                    
                    $subject = $modx->lexicon('stik_ss_email_size_in_stock_subject');
                    $chunk = 'sizeSubscribeEmailTpl';
                    $email = $stik->sendEmail($object->get('email'), $subject, $chunk, $object->toArray());
                }
                if ($email === true) {
                    $object->set('status', 1);
                    $object->set('sendedon', time());
                    $object->save();
                    $success++;
                } else {
                    $modx->log(modX::LOG_LEVEL_INFO,  "stik ERROR: " . $message . "\nID: " . $object->get('id') . "\nEmail: " . $object->get('email'));
                    $errors++;
                }
            }
            break; // только первое совпадение размера
        }
    }
}


if (!XPDO_CLI_MODE) {echo '<pre>';}
echo "\nOperation complete in ".number_format(microtime(true) - $modx->startTime, 7) . " s\n";
echo "Sended:	$success\n";
echo "Errors:	$errors\n";
if (!XPDO_CLI_MODE) {echo '</pre>';}