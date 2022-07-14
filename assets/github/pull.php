<?php `git pull`;
define('MODX_API_MODE', true);
require_once dirname(dirname(__DIR__)) . '/index.php';
/** @var $modx gitModx */
$modx->cacheManager->refresh();
