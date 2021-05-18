id: 82
source: 1
name: PolylangMakeUrl
category: Polylang
properties: 'a:2:{s:2:"id";a:9:{s:4:"name";s:2:"id";s:4:"desc";s:16:"polylang_prop_id";s:4:"type";s:11:"numberfield";s:7:"options";a:0:{}s:5:"value";s:1:"0";s:7:"lexicon";s:19:"polylang:properties";s:4:"area";s:0:"";s:10:"desc_trans";s:18:"ID ресурса.";s:10:"area_trans";s:0:"";}s:6:"scheme";a:9:{s:4:"name";s:6:"scheme";s:4:"desc";s:20:"polylang_prop_scheme";s:4:"type";s:9:"textfield";s:7:"options";a:0:{}s:5:"value";s:0:"";s:7:"lexicon";s:19:"polylang:properties";s:4:"area";s:0:"";s:10:"desc_trans";s:40:"Схема формирования URL.";s:10:"area_trans";s:0:"";}}'
static_file: core/components/polylang/elements/snippets/PolylangMakeUrl.snippet.php

-----

/**
 * Polylang
 * @package polylang
 * @var modX $modx
 * @var Polylang $polylang
 * @var PolylangTools $tools
 * @var array $scriptProperties
 * @var int $id
 * @var string $scheme
 */

$polylang = $modx->getService('polylang', 'Polylang');
$tools = $polylang->getTools();
$id = $id ? $id : $modx->resource->get('id');
if (empty($scheme)) {
    $scheme = $modx->getOption('link_tag_scheme', null, -1, true);
}
if ($language = $tools->detectLanguage(true)) {
    $url = $language->makeUrl($id, $scheme);
} else {
    $url = $modx->makeUrl($id,'','', $scheme);
}
return $url;