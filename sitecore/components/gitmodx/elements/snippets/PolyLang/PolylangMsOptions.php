<?php
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