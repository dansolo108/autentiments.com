<?php
define('MODX_API_MODE', true);
require_once dirname(__FILE__, 5) . '/index.php';
/** @var $modx gitModx */
$modx->setLogTarget('ECHO');
$modx->setLogLevel(MODX_LOG_LEVEL_INFO);
$modx->cacheManager->refresh();