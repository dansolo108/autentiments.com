<?php

//ini_set('display_errors', 1);
//ini_set('error_reporting', -1);

if (empty($_REQUEST['service']) OR $_REQUEST['service'] !== 'msfavorites') {
    die('Access denied');
}
if (empty($_REQUEST['action'])) {
    die('Access denied');
}

if (empty($_REQUEST['list'])) {
    $_REQUEST['list'] = 'default';
}
if (empty($_REQUEST['class'])) {
    $_REQUEST['class'] = 'resource';
}
if (empty($_REQUEST['key'])) {
    $_REQUEST['key'] = '';
}
if (empty($_REQUEST['method'])) {
    $_REQUEST['method'] = 'get';
}
foreach (['list', 'class', 'key', 'method'] as $k) {
    $_REQUEST[$k] = trim(mb_strtolower($_REQUEST[$k], 'utf-8'));
}

/** @noinspection PhpIncludeInspection */
require dirname(dirname(dirname(__DIR__))) . '/index.php';