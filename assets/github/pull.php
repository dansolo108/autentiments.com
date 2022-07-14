<?php
define('MODX_API_MODE', true);
require_once dirname(dirname(__DIR__)) . '/index.php';
/** @var $modx gitModx */
$modx->setLogTarget('ECHO');
$modx->setLogLevel(MODX_LOG_LEVEL_INFO);
$output = shell_exec('git -C "'.MODX_BASE_PATH.'" pull');
$modx->cacheManager->refresh();
$modx->log(3,print_r('git -C "'.MODX_BASE_PATH.'" pull',1));
$modx->log(3,print_r($output,1));
echo 'test';
die();