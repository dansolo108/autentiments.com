<?php
// ini_set('error_reporting', E_ALL);
// ini_set('display_errors', 1);
// ini_set('display_startup_errors', 1);
if ( file_exists(dirname(dirname(dirname(dirname(__FILE__)))).'/config.core.php') ) {
	require_once dirname(dirname(dirname(dirname(__FILE__)))) . '/config.core.php';
} else {
	require_once dirname(dirname(dirname(dirname(dirname(__FILE__))))) . '/config.core.php';
}
require_once MODX_CORE_PATH . 'config/' . MODX_CONFIG_KEY . '.inc.php';
require_once MODX_CONNECTORS_PATH . 'index.php';

$corePath = $modx->getOption('stikproductremains.core_path', null, $modx->getOption('core_path') . 'components/stik/');
require_once $corePath . 'model/stikproductremains.class.php';
$modx->stikProductRemains = new stikProductRemains($modx);

$modx->lexicon->load(array('stikproductremains:default', 'stikproductremains:manager'));

/* handle request */
$path = $modx->getOption('processorsPath', $modx->stikProductRemains->config, $corePath . 'processors/');
$modx->request->handleRequest(array(
	'processors_path' => $path,
	'location' => '',
));
