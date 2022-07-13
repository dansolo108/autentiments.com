<?php
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