<?php
define('MODX_API_MODE', true);
require_once dirname(__DIR__, 3) . '/index.php';
/** @var $modx gitModx */
$modx->setLogTarget('HTML');
$modx->setLogLevel(MODX_LOG_LEVEL_INFO);

$input = json_decode(file_get_contents('php://input'),1);