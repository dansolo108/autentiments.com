<?php
/**
 * Polylang Connector
 * @package polylang
 */

require_once dirname(dirname(dirname(dirname(__FILE__)))) . '/config.core.php';
require_once MODX_CORE_PATH . 'config/' . MODX_CONFIG_KEY . '.inc.php';
require_once MODX_CONNECTORS_PATH . 'index.php';

$corePath = $modx->getOption('polylang.core_path', null, $modx->getOption('core_path') . 'components/polylang/');
require_once $corePath . 'model/polylang/polylang.class.php';
$modx->polylang = new Polylang($modx);

$modx->lexicon->load('polylang:default', 'polylang:site');

/* handle request */
$path = $modx->getOption('processorsPath', $modx->polylang->config, $corePath . 'processors/');
$modx->request->handleRequest(array(
    'processors_path' => $path,
    'location' => '',
));
