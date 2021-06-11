id: 103
source: 1
name: msMultiCurrencyPriceFloor
description: 'Модифицированная функция getPrice из главного класса. Округляет Евро в меньшую сторону'
category: msMultiCurrency
properties: 'a:5:{s:3:"cid";a:7:{s:4:"name";s:3:"cid";s:4:"desc";s:24:"msmulticurrency_prop_cid";s:4:"type";s:9:"textfield";s:7:"options";a:0:{}s:5:"value";s:1:"0";s:7:"lexicon";s:26:"msmulticurrency:properties";s:4:"area";s:0:"";}s:6:"format";a:7:{s:4:"name";s:6:"format";s:4:"desc";s:27:"msmulticurrency_prop_format";s:4:"type";s:13:"combo-boolean";s:7:"options";a:0:{}s:5:"value";b:1;s:7:"lexicon";s:26:"msmulticurrency:properties";s:4:"area";s:0:"";}s:4:"name";a:7:{s:4:"name";s:4:"name";s:4:"desc";s:25:"msmulticurrency_prop_name";s:4:"type";s:9:"textfield";s:7:"options";a:0:{}s:5:"value";s:0:"";s:7:"lexicon";s:26:"msmulticurrency:properties";s:4:"area";s:0:"";}s:3:"pid";a:7:{s:4:"name";s:3:"pid";s:4:"desc";s:24:"msmulticurrency_prop_pid";s:4:"type";s:9:"textfield";s:7:"options";a:0:{}s:5:"value";s:1:"0";s:7:"lexicon";s:26:"msmulticurrency:properties";s:4:"area";s:0:"";}s:5:"price";a:7:{s:4:"name";s:5:"price";s:4:"desc";s:26:"msmulticurrency_prop_price";s:4:"type";s:9:"textfield";s:7:"options";a:0:{}s:5:"value";s:1:"0";s:7:"lexicon";s:26:"msmulticurrency:properties";s:4:"area";s:0:"";}}'
static_file: core/components/msmulticurrency/elements/snippets/msMultiCurrencyPrice.snippet.php

-----

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