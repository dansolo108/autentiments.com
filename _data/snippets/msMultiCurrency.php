id: 76
source: 1
name: msMultiCurrency
category: msMultiCurrency
properties: 'a:5:{s:11:"frontendCss";a:9:{s:4:"name";s:11:"frontendCss";s:4:"desc";s:32:"msmulticurrency_prop_frontendCss";s:4:"type";s:9:"textfield";s:7:"options";a:0:{}s:5:"value";s:37:"[[+assetsUrl]]css/web/default.min.css";s:7:"lexicon";s:26:"msmulticurrency:properties";s:4:"area";s:0:"";s:10:"desc_trans";s:29:"Стиль фронтенда";s:10:"area_trans";s:0:"";}s:10:"frontendJs";a:9:{s:4:"name";s:10:"frontendJs";s:4:"desc";s:31:"msmulticurrency_prop_frontendJs";s:4:"type";s:9:"textfield";s:7:"options";a:0:{}s:5:"value";s:35:"[[+assetsUrl]]js/web/default.min.js";s:7:"lexicon";s:26:"msmulticurrency:properties";s:4:"area";s:0:"";s:10:"desc_trans";s:31:"Скрипт фронтенда";s:10:"area_trans";s:0:"";}s:4:"mode";a:9:{s:4:"name";s:4:"mode";s:4:"desc";s:25:"msmulticurrency_prop_mode";s:4:"type";s:9:"textfield";s:7:"options";a:0:{}s:5:"value";s:6:"normal";s:7:"lexicon";s:26:"msmulticurrency:properties";s:4:"area";s:0:"";s:10:"desc_trans";s:348:"Режим смены валюыты. Может принимать значения: normal - изменение цены через перезагрузку страницы. ajax - изменение цены через ajax загрузку. auto - автоматически определения способа обновления цены.";s:10:"area_trans";s:0:"";}s:6:"symbol";a:9:{s:4:"name";s:6:"symbol";s:4:"desc";s:27:"msmulticurrency_prop_symbol";s:4:"type";s:9:"textfield";s:7:"options";a:0:{}s:5:"value";s:5:"right";s:7:"lexicon";s:26:"msmulticurrency:properties";s:4:"area";s:0:"";s:10:"desc_trans";s:96:"Символ валюты, может принимать два значения, right и left.";s:10:"area_trans";s:0:"";}s:3:"tpl";a:9:{s:4:"name";s:3:"tpl";s:4:"desc";s:24:"msmulticurrency_prop_tpl";s:4:"type";s:9:"textfield";s:7:"options";a:0:{}s:5:"value";s:15:"msMultiCurrency";s:7:"lexicon";s:26:"msmulticurrency:properties";s:4:"area";s:0:"";s:10:"desc_trans";s:54:"Чанк оформления списка валют.";s:10:"area_trans";s:0:"";}}'
static_file: core/components/msmulticurrency/elements/snippets/msMultiCurrency.snippet.php

-----

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