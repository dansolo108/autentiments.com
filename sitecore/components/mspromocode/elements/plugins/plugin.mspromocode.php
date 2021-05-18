<?php

/** @var msPromoCode $mspc */
/** @var array $sp */
$sp = &$scriptProperties;

$ctx = isset($_REQUEST['ctx']) ? $_REQUEST['ctx'] : $modx->context->key;

$path = MODX_CORE_PATH . 'components/mspromocode/model/mspromocode/';
$mspc = $modx->getService('mspromocode', 'msPromoCode', $path, array_merge($sp, array('ctx' => $ctx)));
if (!is_object($mspc)) {
    return '';
}

$eventName = $modx->event->name;
if (method_exists($mspc, $eventName) && $mspc->active) {
    $mspc->$eventName($sp);
}