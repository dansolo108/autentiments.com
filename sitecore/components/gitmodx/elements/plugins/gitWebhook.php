<?php
/** @var $modx gitModx */
if($modx->event->name != 'OnHandleRequest') return;

$alias = $modx->context->getOption('request_param_alias', 'q');
if (!isset($_REQUEST[$alias])) {return;}
// А если есть - работаем дальше
$request = $_REQUEST[$alias];
$tmp = explode('/', $request);

if ($tmp[0] == 'github') {
    $output = exec('git -C "'.MODX_BASE_PATH.'" pull');
    $modx->log(1,print_r('git -C "'.MODX_BASE_PATH.'" pull',1));
    $modx->log(1,print_r($output,1));

    $modx->cacheManager->refresh();

}
else{
    return;
}