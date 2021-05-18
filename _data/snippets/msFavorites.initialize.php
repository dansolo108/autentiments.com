id: 54
source: 1
name: msFavorites.initialize
category: msFavorites
properties: 'a:3:{s:8:"frontCss";a:7:{s:4:"name";s:8:"frontCss";s:4:"desc";s:25:"msfavorites_prop_frontCss";s:4:"type";s:9:"textfield";s:7:"options";a:0:{}s:5:"value";s:0:"";s:7:"lexicon";s:22:"msfavorites:properties";s:4:"area";s:0:"";}s:7:"frontJs";a:7:{s:4:"name";s:7:"frontJs";s:4:"desc";s:24:"msfavorites_prop_frontJs";s:4:"type";s:9:"textfield";s:7:"options";a:0:{}s:5:"value";s:0:"";s:7:"lexicon";s:22:"msfavorites:properties";s:4:"area";s:0:"";}s:9:"actionUrl";a:7:{s:4:"name";s:9:"actionUrl";s:4:"desc";s:26:"msfavorites_prop_actionUrl";s:4:"type";s:9:"textfield";s:7:"options";a:0:{}s:5:"value";s:24:"[[+assetsUrl]]action.php";s:7:"lexicon";s:22:"msfavorites:properties";s:4:"area";s:0:"";}}'
static_file: core/components/msfavorites/elements/snippets/snippet.msfavorites.initialize.php

-----

/** @var array $scriptProperties */
/** @var msFavorites $msFavorites */
if (!$msFavorites = $modx->getService('msfavorites.msFavorites', '', MODX_CORE_PATH . 'components/msfavorites/model/')) {
    return 'Could not load msFavorites class!';
}
$msFavorites->initialize($modx->context->key, $scriptProperties);