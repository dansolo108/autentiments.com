id: 81
source: 1
name: PolylangLinks
category: Polylang
properties: 'a:9:{s:12:"activeClass ";a:9:{s:4:"name";s:12:"activeClass ";s:4:"desc";s:26:"polylang_prop_active_class";s:4:"type";s:9:"textfield";s:7:"options";a:0:{}s:5:"value";s:6:"active";s:7:"lexicon";s:19:"polylang:properties";s:4:"area";s:0:"";s:10:"desc_trans";s:26:"polylang_prop_active_class";s:10:"area_trans";s:0:"";}s:3:"css";a:9:{s:4:"name";s:3:"css";s:4:"desc";s:17:"polylang_prop_css";s:4:"type";s:9:"textfield";s:7:"options";a:0:{}s:5:"value";s:0:"";s:7:"lexicon";s:19:"polylang:properties";s:4:"area";s:0:"";s:10:"desc_trans";s:253:"Если вы хотите использовать собственные стили - укажите путь к ним  здесь, или очистите параметр и загрузите их вручную через шаблон сайта.";s:10:"area_trans";s:0:"";}s:2:"js";a:9:{s:4:"name";s:2:"js";s:4:"desc";s:16:"polylang_prop_js";s:4:"type";s:9:"textfield";s:7:"options";a:0:{}s:5:"value";s:53:"{assets_url}components/polylang/js/web/default.min.js";s:7:"lexicon";s:19:"polylang:properties";s:4:"area";s:0:"";s:10:"desc_trans";s:256:"Если вы хотите использовать собственные скрипты - укажите путь к ним здесь, или очистите параметр и загрузите их вручную через шаблон сайта.";s:10:"area_trans";s:0:"";}s:13:"languageGroup";a:9:{s:4:"name";s:13:"languageGroup";s:4:"desc";s:28:"polylang_prop_language_group";s:4:"type";s:9:"textfield";s:7:"options";a:0:{}s:5:"value";s:0:"";s:7:"lexicon";s:19:"polylang:properties";s:4:"area";s:0:"";s:10:"desc_trans";s:25:"Группа языков";s:10:"area_trans";s:0:"";}s:4:"mode";a:9:{s:4:"name";s:4:"mode";s:4:"desc";s:18:"polylang_prop_mode";s:4:"type";s:9:"textfield";s:7:"options";a:0:{}s:5:"value";s:8:"dropdown";s:7:"lexicon";s:19:"polylang:properties";s:4:"area";s:0:"";s:10:"desc_trans";s:71:"Вид вывода. Доступные значения:dropdown;list.";s:10:"area_trans";s:0:"";}s:6:"scheme";a:9:{s:4:"name";s:6:"scheme";s:4:"desc";s:20:"polylang_prop_scheme";s:4:"type";s:9:"textfield";s:7:"options";a:0:{}s:5:"value";s:0:"";s:7:"lexicon";s:19:"polylang:properties";s:4:"area";s:0:"";s:10:"desc_trans";s:40:"Схема формирования URL.";s:10:"area_trans";s:0:"";}s:10:"showActive";a:9:{s:4:"name";s:10:"showActive";s:4:"desc";s:25:"polylang_prop_show_active";s:4:"type";s:13:"combo-boolean";s:7:"options";a:0:{}s:5:"value";b:0;s:7:"lexicon";s:19:"polylang:properties";s:4:"area";s:0:"";s:10:"desc_trans";s:68:" Показывать ли ссылку текущего языка.";s:10:"area_trans";s:0:"";}s:3:"tpl";a:9:{s:4:"name";s:3:"tpl";s:4:"desc";s:17:"polylang_prop_tpl";s:4:"type";s:9:"textfield";s:7:"options";a:0:{}s:5:"value";s:17:"tpl.PolylangLinks";s:7:"lexicon";s:19:"polylang:properties";s:4:"area";s:0:"";s:10:"desc_trans";s:192:"Имя чанка для оформления ресурса. Если не указан, то содержимое полей ресурса будет распечатано на экран.";s:10:"area_trans";s:0:"";}s:7:"trigger";a:9:{s:4:"name";s:7:"trigger";s:4:"desc";s:21:"polylang_prop_trigger";s:4:"type";s:9:"textfield";s:7:"options";a:0:{}s:5:"value";s:15:"polylang-toggle";s:7:"lexicon";s:19:"polylang:properties";s:4:"area";s:0:"";s:10:"desc_trans";s:82:"Название класса у ссылки переключения языка.";s:10:"area_trans";s:0:"";}}'
static_file: core/components/polylang/elements/snippets/PolylangLinks.snippet.php

-----

/**
 * Polylang
 * @package polylang
 * @var modX $modx
 * @var Polylang $polylang
 * @var PolylangTools $tools
 * @var array $scriptProperties
 * @var string $scheme
 * @var string $activeClass
 * @var bool $showActive
 */

$polylang = $modx->getService('polylang', 'Polylang');
$tools = $polylang->getTools();
$current = array();
$languages = array();
$classKey = 'PolylangLanguage';
$defaultLanguageGroup = $modx->getOption('polylang_default_language_group');
$languageGroup = $modx->getOption('languageGroup', $scriptProperties, $defaultLanguageGroup, true);

if (empty($scheme)) {
    $scheme = $modx->getOption('link_tag_scheme', null, -1, true);
}

$q = $modx->newQuery($classKey);
$q->where(array('`active`' => 1));
if ($languageGroup) {
    $languageGroup = $tools->explodeAndClean($languageGroup);
    $q->where(array('`group`:IN' => $languageGroup));
}
$q->sortby('`rank`', 'ASC');

/* @var PolylangLanguage[] $list */
if ($list = $modx->getCollection($classKey, $q)) {
    foreach ($list as $item) {
        $active = $item->isCurrent();
        $language = array(
            'active' => $active,
            'name' => $item->get('name'),
            'group' => $item->get('group'),
            'classes' => $active ? $activeClass : '',
            'link' => $item->makeUrl($modx->resource, $scheme),
            'culture_key' => $item->get('culture_key'),
            'currency_id' => $item->get('currency_id'),
        );
        if ($active) {
            $current = $language;
            if (!$showActive) continue;
        }
        $languages[] = $language;
    }
}

$config = array(
    'actionUrl' => $polylang->config['actionUrl'],
    'trigger' => $modx->getOption('trigger', $scriptProperties, 'polylang-toggle', true),
);

$modx->regClientStartupHTMLBlock('<script> var polylangConfig = ' . $modx->toJSON($config) . ';</script>');

if (!empty($css)) {
    $modx->regClientCSS($tools->preparePath($css));
}

if (!empty($js)) {
    $modx->regClientScript($tools->preparePath($js));
}

return $tools->getPdoTools()->getChunk($tpl, array(
    'mode' => $mode,
    'current' => $current,
    'languages' => $languages,
    'showActive' => $showActive,
));