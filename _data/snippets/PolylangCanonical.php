id: 79
source: 1
name: PolylangCanonical
category: Polylang
properties: 'a:3:{s:6:"scheme";a:9:{s:4:"name";s:6:"scheme";s:4:"desc";s:20:"polylang_prop_scheme";s:4:"type";s:9:"textfield";s:7:"options";a:0:{}s:5:"value";s:4:"full";s:7:"lexicon";s:19:"polylang:properties";s:4:"area";s:0:"";s:10:"desc_trans";s:40:"Схема формирования URL.";s:10:"area_trans";s:0:"";}s:3:"tpl";a:9:{s:4:"name";s:3:"tpl";s:4:"desc";s:17:"polylang_prop_tpl";s:4:"type";s:9:"textfield";s:7:"options";a:0:{}s:5:"value";s:140:"@INLINE {if $current?}<link  rel="canonical"  href="{$url}"/>{$.const.PHP_EOL}{/if}<link  rel="alternate" hreflang="{$lang}" href="{$url}"/>";s:7:"lexicon";N;s:4:"area";s:0:"";s:10:"desc_trans";s:192:"Имя чанка для оформления ресурса. Если не указан, то содержимое полей ресурса будет распечатано на экран.";s:10:"area_trans";s:0:"";}s:13:"languageGroup";a:9:{s:4:"name";s:13:"languageGroup";s:4:"desc";s:28:"polylang_prop_language_group";s:4:"type";s:9:"textfield";s:7:"options";a:0:{}s:5:"value";s:0:"";s:7:"lexicon";s:19:"polylang:properties";s:4:"area";s:0:"";s:10:"desc_trans";s:25:"Группа языков";s:10:"area_trans";s:0:"";}}'
static_file: core/components/polylang/elements/snippets/PolylangCanonical.snippet.php

-----

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

$languages = $modx->getCollection('PolylangLanguage', array('culture_key:IN' => $keys));

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