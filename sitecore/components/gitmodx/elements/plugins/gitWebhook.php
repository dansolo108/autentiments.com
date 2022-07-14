<?php
/** @var $modx gitModx */
if($modx->event->name != 'OnHandleRequest') return;

$alias = $modx->context->getOption('request_param_alias', 'q');
if (!isset($_REQUEST[$alias])) {return;}
// А если есть - работаем дальше
$request = $_REQUEST[$alias];
$tmp = explode('/', $request);

if ($tmp[0] == 'github') {
    $modx->log(1,'test');

}
else{
    return;
}