<?php
/** @var modX $modx */
$sp = &$scriptProperties;

$ctx = isset($_REQUEST['ctx']) ? $_REQUEST['ctx'] : $modx->context->key;

$path = MODX_CORE_PATH . 'components/autentiments/model/autentiments/';
$autentiments = $modx->getService('autentiments');
if (!is_object($autentiments)) {
    return '';
}

$eventName = $modx->event->name;
if (method_exists($autentiments, $eventName)) {
    $autentiments->$eventName($sp);
}

