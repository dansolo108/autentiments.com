<?php
/**
 * msMultiCurrencyPrice
 * @package msmulticurrency
 * @var modX $modx
 * @var MsMC $msmc
 * pdoTools $pdoTools
 * @var array $scriptProperties
 */


$msmc = $modx->getService('msmulticurrency', 'MsMC');
if (
    !is_object($msmc) ||
    !($msmc instanceof MsMC) ||
    !$pdoTools = $msmc->getPdoToolsInstance()
) {
    return;
}

$setId = $setId ? $setId : $msmc->config['baseCurrencySetId'];
$pid = $pid ? $pid : $modx->resource->get('id');
$baseCurrencyId = $modx->getOption('msmulticurrency.base_currency', null, 0, true);

$price = isset($scriptProperties['price']) ? $scriptProperties['price'] : 0;
$old_price = isset($scriptProperties['old_price']) ? $scriptProperties['old_price'] : 0;

if (!$price) {
    $classKey = 'msProductData';
    $q = $modx->newQuery($classKey);
    $q->select($modx->getSelectColumns($classKey, $classKey, '', array('price', 'old_price')));
    $q->where(array('id' => $pid));
    if ($q->prepare() && $q->stmt->execute()) {
        if (!$product = $q->stmt->fetch(PDO::FETCH_ASSOC)) return;
        $price = $product['price'];
        $old_price = $product['old_price'];
    }
}

if (!$price) return;

$currencies = $msmc->getCurrencies();

foreach ($currencies as $id => &$currency) {
    $currency['price'] = $msmc->convertPriceToCurrency($price, $id, $setId, $format);
    $currency['old_price'] = $msmc->convertPriceToCurrency($old_price, $id, $setId, $format);
}

return $pdoTools->getChunk($tpl, array(
    'symbol' => 'symbol_' . $symbol,
    'baseCurrencyId' => $baseCurrencyId,
    'userCurrencyId' => $msmc->getUserCurrency(),
    'currencies' => $currencies,
));