<?php
require_once dirname(__FILE__, 4) . '/config.core.php';
require_once MODX_CORE_PATH . 'components/gitmodx/model/gitmodx/gitmodx.class.php';
$modx = new gitModx();
$modx->initialize();

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

print $modx->runSnippet('msCart', [
    'tpl' => 'cart.ajax',
    'includeThumbs' => 'cart',
]);
exit();