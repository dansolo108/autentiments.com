id: 74
source: 1
name: msMultiCurrencyPrice
category: msMultiCurrency
properties: 'a:5:{s:3:"cid";a:9:{s:4:"name";s:3:"cid";s:4:"desc";s:24:"msmulticurrency_prop_cid";s:4:"type";s:9:"textfield";s:7:"options";a:0:{}s:5:"value";s:1:"0";s:7:"lexicon";s:26:"msmulticurrency:properties";s:4:"area";s:0:"";s:10:"desc_trans";s:15:"ID валюты";s:10:"area_trans";s:0:"";}s:6:"format";a:9:{s:4:"name";s:6:"format";s:4:"desc";s:27:"msmulticurrency_prop_format";s:4:"type";s:13:"combo-boolean";s:7:"options";a:0:{}s:5:"value";b:1;s:7:"lexicon";s:26:"msmulticurrency:properties";s:4:"area";s:0:"";s:10:"desc_trans";s:35:"Форматировать цену";s:10:"area_trans";s:0:"";}s:3:"pid";a:9:{s:4:"name";s:3:"pid";s:4:"desc";s:24:"msmulticurrency_prop_pid";s:4:"type";s:9:"textfield";s:7:"options";a:0:{}s:5:"value";s:1:"0";s:7:"lexicon";s:26:"msmulticurrency:properties";s:4:"area";s:0:"";s:10:"desc_trans";s:19:"ID продукта";s:10:"area_trans";s:0:"";}s:5:"price";a:9:{s:4:"name";s:5:"price";s:4:"desc";s:26:"msmulticurrency_prop_price";s:4:"type";s:9:"textfield";s:7:"options";a:0:{}s:5:"value";s:1:"0";s:7:"lexicon";s:26:"msmulticurrency:properties";s:4:"area";s:0:"";s:10:"desc_trans";s:8:"Цена";s:10:"area_trans";s:0:"";}s:4:"name";a:9:{s:4:"name";s:4:"name";s:4:"desc";s:25:"msmulticurrency_prop_name";s:4:"type";s:9:"textfield";s:7:"options";a:0:{}s:5:"value";s:0:"";s:7:"lexicon";s:26:"msmulticurrency:properties";s:4:"area";s:0:"";s:10:"desc_trans";s:25:"msmulticurrency_prop_name";s:10:"area_trans";s:0:"";}}'
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

return $msmc->getPrice($price, $pid, $cid, $course, $format);