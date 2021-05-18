id: 73
source: 1
name: msMultiCurrencyPrices
category: msMultiCurrency
properties: 'a:4:{s:6:"format";a:9:{s:4:"name";s:6:"format";s:4:"desc";s:27:"msmulticurrency_prop_format";s:4:"type";s:13:"combo-boolean";s:7:"options";a:0:{}s:5:"value";b:1;s:7:"lexicon";s:26:"msmulticurrency:properties";s:4:"area";s:0:"";s:10:"desc_trans";s:35:"Форматировать цену";s:10:"area_trans";s:0:"";}s:3:"pid";a:9:{s:4:"name";s:3:"pid";s:4:"desc";s:24:"msmulticurrency_prop_pid";s:4:"type";s:9:"textfield";s:7:"options";a:0:{}s:5:"value";s:1:"0";s:7:"lexicon";s:26:"msmulticurrency:properties";s:4:"area";s:0:"";s:10:"desc_trans";s:19:"ID продукта";s:10:"area_trans";s:0:"";}s:3:"tpl";a:9:{s:4:"name";s:3:"tpl";s:4:"desc";s:31:"msmulticurrency_prop_tpl_prices";s:4:"type";s:9:"textfield";s:7:"options";a:0:{}s:5:"value";s:21:"msMultiCurrencyPrices";s:7:"lexicon";s:26:"msmulticurrency:properties";s:4:"area";s:0:"";s:10:"desc_trans";s:81:"Чанк оформления списка цен товара в валютах.";s:10:"area_trans";s:0:"";}s:6:"symbol";a:9:{s:4:"name";s:6:"symbol";s:4:"desc";s:27:"msmulticurrency_prop_symbol";s:4:"type";s:9:"textfield";s:7:"options";a:0:{}s:5:"value";s:5:"right";s:7:"lexicon";s:26:"msmulticurrency:properties";s:4:"area";s:0:"";s:10:"desc_trans";s:96:"Символ валюты, может принимать два значения, right и left.";s:10:"area_trans";s:0:"";}}'
static_file: core/components/msmulticurrency/elements/snippets/msMultiCurrencyPrices.snippet.php

-----

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