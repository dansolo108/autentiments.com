<?php

if (empty($_REQUEST['mspc_action'])) {
    exit('Access denied!');
}
@session_cache_limiter('nocache');

// Подключаем MODX
do {
    $dir = dirname(!empty($file) ? dirname($file) : __FILE__);
    $file = $dir.'/index.php';
    $i = isset($i) ? --$i : 10;
} while ($i && !file_exists($file));

if (!file_exists($file)) {
    exit('Access denied!');
}
require_once $file;
