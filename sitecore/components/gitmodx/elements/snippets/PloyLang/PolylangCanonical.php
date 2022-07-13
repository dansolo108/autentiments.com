<?php
/**
 * Polylang
 * @package polylang
 * @var modX $modx
 * @var Polylang $polylang
 * @var PolylangTools $tools
 * @var array $scriptProperties
 * @var string $tpl
 * @var string $scheme
 */

$polylang = $modx->getService('polylang', 'Polylang');
$tools = $polylang->getTools();
$id = $modx->resource->get('id');
$defaultLanguage = $tools->getDefaultLanguage();
$currentLanguage = $modx->getOption('cultureKey');

if (empty($scheme)) {
    $scheme = $modx->getOption('link_tag_scheme', null, -1, true);
}

$keys = $tools->getResourceLanguageKeys($id);
$keys[] = $tools->getDefaultLanguage();

$languages = $modx->getCollection('PolylangLanguage', array('active' => 1));

if ($languages) {
    $total = count($languages);
    foreach ($languages as $language) {
        $data = array(
            'total' => $total,
            'url' => $language->makeUrl($id, $scheme),
            'lang' => $language->get('culture_key'),
            'default' => $language->get('culture_key') == $defaultLanguage ? 1 : 0,
            'current' => $language->get('culture_key') == $currentLanguage ? 1 : 0,
        );
        $link = $tools->getPdoTools()->getChunk($tpl, $data);
        $modx->regClientStartupHTMLBlock($link);
    }
}