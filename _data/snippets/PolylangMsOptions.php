id: 83
source: 1
name: PolylangMsOptions
category: Polylang
properties: 'a:3:{s:7:"options";a:9:{s:4:"name";s:7:"options";s:4:"desc";s:21:"polylang_prop_options";s:4:"type";s:9:"textfield";s:7:"options";a:0:{}s:5:"value";s:0:"";s:7:"lexicon";s:19:"polylang:properties";s:4:"area";s:0:"";s:10:"desc_trans";s:71:"Список опций для вывода, через запятую.";s:10:"area_trans";s:0:"";}s:3:"tpl";a:9:{s:4:"name";s:3:"tpl";s:4:"desc";s:17:"polylang_prop_tpl";s:4:"type";s:9:"textfield";s:7:"options";a:0:{}s:5:"value";s:21:"tpl.PolylangMsOptions";s:7:"lexicon";s:19:"polylang:properties";s:4:"area";s:0:"";s:10:"desc_trans";s:192:"Имя чанка для оформления ресурса. Если не указан, то содержимое полей ресурса будет распечатано на экран.";s:10:"area_trans";s:0:"";}s:7:"product";a:9:{s:4:"name";s:7:"product";s:4:"desc";s:21:"polylang_prop_product";s:4:"type";s:11:"numberfield";s:7:"options";a:0:{}s:5:"value";s:0:"";s:7:"lexicon";s:19:"polylang:properties";s:4:"area";s:0:"";s:10:"desc_trans";s:133:"Идентификатор товара. Если не указан, используется id текущего документа.";s:10:"area_trans";s:0:"";}}'
static_file: core/components/polylang/elements/snippets/PolylangMsOptions.snippet.php

-----

/**
 * Polylang
 * @package polylang
 * @var modX $modx
 * @var Polylang $polylang
 * @var PolylangTools $tools
 * @var array $scriptProperties
 */

$polylang = $modx->getService('polylang', 'Polylang');
$tools = $polylang->getTools();

$tpl = $modx->getOption('tpl', $scriptProperties, 'tpl.PolylangMsOptions');
if (!empty($input) && empty($product)) {
    $product = $input;
}
if (!empty($name) && empty($options)) {
    $options = $name;
}

$product = !empty($product) && $product != $modx->resource->id
    ? $modx->getObject('msProduct', array('id' => $product))
    : $modx->resource;
if (!($product instanceof msProduct)) {
    return "[msOptions] The resource with id = {$product->id} is not instance of msProduct.";
}

$isCurrentDefaultLanguage = $tools->isCurrentDefaultLanguage();
$names = array_map('trim', explode(',', $options));
$options = array();

foreach ($names as $name) {
    if (!empty($name) && $option = $product->get($name)) {
        if ($isCurrentDefaultLanguage) {
            $defaultOption = $option;
        } else {
            $defaultOption = $product->get('polylang_original_' . $name);
        }
        if (!is_array($option)) {
            $option = array($option);
            $defaultOption = array($defaultOption);
        }

        if (!empty($option[0])) {
            $options[$name] = array_combine($defaultOption, $option);
        }
    }
}

/** @var pdoTools $pdoTools */
$pdoTools = $modx->getService('pdoTools');

return $pdoTools->getChunk($tpl, array(
    'id' => $product->id,
    'options' => $options,
));