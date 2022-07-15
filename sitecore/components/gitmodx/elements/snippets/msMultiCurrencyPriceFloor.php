<?php
/**
 * msMultiCurrencyPrice
 * @package msmulticurrency
 * @var modX $modx
 * @var array $scriptProperties
 */

/** @var MsMC $msmc */
$msmc = $modx->getService('msmulticurrency', 'MsMC');

if (!is_object($msmc) || !($msmc instanceof MsMC)) return $price;

$price = $price ?: 0;
$productId = $pid ?: 0;
$currencyId = $cid ?: 0;
$course = $course ?: 0.0;
$isFormat = $format ?: true;
$isBaseCurrency = $isBaseCurrency ?: true;

$price = is_numeric($price) ? $price : floatval(preg_replace('~\s+~s', '', $price));
$newPrice = $price;
$currency = array();
$userCurrency = array();
$baseCurrencyId = $modx->getOption('msmulticurrency.base_currency', null, 0, true);
$setId = $modx->getOption('msmulticurrency.base_currency_set', null, 1, true);

if (!empty($course)) {
    $course = floatval($course);
    $newPrice = empty($isBaseCurrency) ? ($price * $course) : ($price / $course);
} else if ($userCurrency = $msmc->getUserCurrencyData(array(), $setId)) {
    $currency = $userCurrency;
    if (!empty($currencyId) && $currencyId != $currency['id']) {
        $currency = $msmc->getCurrencyById($currencyId, $setId);
    }
    if (!empty($currency)) {
        if (empty($isBaseCurrency)) {
            $newPrice = $price * $userCurrency['val'];
        } else if ($baseCurrencyId != $currency['id']) {
            $newPrice = $price / $currency['val'];
        }
    }
}

// округляем в меньшую сторону
if ($currency['id'] != 1) {
    $newPrice = floor($newPrice ?: $price);
}

$precision = $currency['precision'];
return $isFormat ? $msmc->formatPrice($newPrice, array($precision, '.', ' ')) : $msmc->formatPrice($newPrice, array($precision, '.', ''));