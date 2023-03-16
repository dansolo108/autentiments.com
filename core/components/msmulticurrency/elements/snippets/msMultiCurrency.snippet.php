<?php
/**
 * msMultiCurrency
 * @package msmulticurrency
 * @var modX $modx
 * @var array $scriptProperties
 */

/** @var MsMC $msmc */
$msmc = $modx->getService('msmulticurrency', 'MsMC');

if (!is_object($msmc) || !($msmc instanceof MsMC)) return '';

if (!$pdoTools = $msmc->getPdoToolsInstance()) return '';

$v = 4;
$setId = $setId ? $setId : $msmc->config['baseCurrencySetId'];
$userCurrencyId = $msmc->getUserCurrency();
$currencies = $msmc->getCurrencies();
$baseCurrencyId = $modx->getOption('msmulticurrency.base_currency', null, 0, true);
$cartUserCurrency = $modx->getOption('msmulticurrency.cart_user_currency', null, 0, true);

$userCurrency = isset($currencies[$userCurrencyId]) ? $currencies[$userCurrencyId] : array();

$config = array(
    'actionUrl' => $msmc->config['actionUrl'],
    'cartUserCurrency' => $modx->getOption('msmulticurrency.cart_user_currency', null, 0, true),
    'setId' => $setId,
    'ctx' => $modx->context->key,
    'cultureKey' => $modx->getOption('cultureKey'),
    'userCurrencyId' => $userCurrencyId,
    'baseCurrencyId' => $baseCurrencyId,
    'cartUserCurrency' => $cartUserCurrency,
    'course' => $userCurrency['val'],
    'mode' => $mode,
    'symbol' => $symbol,
);

if ($css = trim($scriptProperties['frontendCss'])) {

    $css = str_replace('[[+assetsUrl]]', $msmc->config['assetsUrl'], $css);
    $modx->regClientHTMLBlock('<link rel="stylesheet" href="' . $css . '?v=' . $v . '" />');
}

if ($js = trim($scriptProperties['frontendJs'])) {

    $js = str_replace('[[+assetsUrl]]', $msmc->config['assetsUrl'], $js);
    $modx->regClientScript($js . '?v=' . $v);
}

$modx->regClientStartupHTMLBlock('<script> var msMultiCurrencyConfig = ' . $modx->toJSON($config) . ';</script>');

return $pdoTools->getChunk($tpl, array(
    'setId' => $setId,
    'userCurrencyId' => $userCurrencyId,
    'userCurrency' => $userCurrency,
    'currencies' => $currencies,
));