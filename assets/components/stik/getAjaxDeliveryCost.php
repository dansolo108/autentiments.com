<?php
// ini_set('error_reporting', E_ALL);
// ini_set('display_errors', 1);
// ini_set('display_startup_errors', 1);

// $isAjax = !empty($_SERVER['HTTP_X_REQUESTED_WITH']) && $_SERVER['HTTP_X_REQUESTED_WITH'] == 'XMLHttpRequest';

// if (!$isAjax) @session_write_close(); exit;

require_once dirname(dirname(dirname(dirname(__FILE__)))) . '/config.core.php';
require_once MODX_CORE_PATH . 'components/gitmodx/model/gitmodx/gitmodx.class.php';
$modx = new gitModx();
$modx->initialize('web');

$language = htmlspecialchars($_GET['language']);
$language = $language ? $language : 'ru';

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
/** @var miniShop2 $ms2 */
$ms2 = $modx->getService('minishop2');
$ms2->initialize('web');
/** @var msCartHandlerCustom $cart */
$cart = $ms2->cart;
$cartStatus = $cart->status();
print $modx->runSnippet('ms2DeliveryCost', [
    'language' => $language,
    'cost' => $cartStatus['total_cost'],
    'tpl' => 'tpl.ms2DeliveryCost',
    'required' => 'country,city,index',
]);

@session_write_close(); exit();