<?php `git pull`;
define('MODX_API_MODE', true);
require_once dirname(dirname(__DIR__)) . '/index.php';
/** @var $modx gitModx */
$modx->cacheManager->refresh();
$modx->log(1,print_r(file_get_contents('php://input'),1));
$modx->log(1,print_r($_GET,1));

