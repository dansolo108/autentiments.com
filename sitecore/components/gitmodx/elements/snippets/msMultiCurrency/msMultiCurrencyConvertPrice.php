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

$format = isset($scriptProperties['format']) ? $scriptProperties['format'] : 1;

return $msmc->convertPriceToBaseCurrency($price, $cid, $setId, $format);