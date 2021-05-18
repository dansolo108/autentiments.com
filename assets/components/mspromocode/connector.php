<?php

/** @noinspection PhpIncludeInspection */
require_once dirname(dirname(dirname(dirname(__FILE__)))).'/config.core.php';

/** @noinspection PhpIncludeInspection */
require_once MODX_CORE_PATH.'config/'.MODX_CONFIG_KEY.'.inc.php';

/** @noinspection PhpIncludeInspection */
require_once MODX_CONNECTORS_PATH.'index.php';

/** @var msPromoCode $mspc */
$mspc = $modx->getService('mspromocode', 'msPromoCode', $modx->getOption('mspromocode_core_path', null, $modx->getOption('core_path').'components/mspromocode/').'model/mspromocode/');
$modx->lexicon->load('mspromocode:default');

// handle request
$corePath = $modx->getOption('mspromocode_core_path', null, $modx->getOption('core_path').'components/mspromocode/');
$path = $modx->getOption('processorsPath', $mspc->config, $corePath.'processors/');
$modx->request->handleRequest(array(
    'processors_path' => $path,
    'location' => '',
));
