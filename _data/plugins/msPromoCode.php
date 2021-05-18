id: 14
source: 1
name: msPromoCode
category: msPromoCode
properties: null
static_file: core/components/mspromocode/elements/plugins/plugin.mspromocode.php

-----

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