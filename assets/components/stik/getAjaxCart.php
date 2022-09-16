<?php
// ini_set('error_reporting', E_ALL);
// ini_set('display_errors', 1);
// ini_set('display_startup_errors', 1);

// $isAjax = !empty($_SERVER['HTTP_X_REQUESTED_WITH']) && $_SERVER['HTTP_X_REQUESTED_WITH'] == 'XMLHttpRequest';

// if (!$isAjax) @session_write_close(); exit;

require_once dirname(__FILE__, 4) . '/config.core.php';
require_once MODX_CORE_PATH . 'components/gitmodx/model/gitmodx/gitmodx.class.php';
$modx = new gitModx();
$modx->initialize('web');

$language = htmlspecialchars($_POST['language']);
$language = $language ? $language : 'ru';
$mode = $_POST['mode']; // тип корзины сайдбар/полная

$modx->setOption('cultureKey', $language);
$modx->lexicon->load($language . ':polylang:site');
$modx->lexicon->load($language . ':minishop2:default');

// для правильной генерации ссылок
$polylang = $modx->getService('polylang', 'Polylang');
$tools = $polylang->getTools();
$PolylangLanguage = $modx->getObject('PolylangLanguage', array(
    'active' => 1,
    'culture_key' => $language
));
$tools->setLanguage($PolylangLanguage);

// Load main services
// $modx->setLogTarget('FILE');
$modx->setLogLevel(modX::LOG_LEVEL_ERROR);
$modx->getService('error','error.modError');

print $modx->runSnippet('msCart', [
    'tpl' => 'stik.msCart.ajax',
    'includeThumbs' => 'cart',
]);

@session_write_close(); exit();