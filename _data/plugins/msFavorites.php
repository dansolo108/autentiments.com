id: 13
source: 1
name: msFavorites
category: msFavorites
properties: null
static_file: core/components/msfavorites/elements/plugins/plugin.msfavorites.php

-----

/** @var array $scriptProperties */
/** @var msFavorites $msFavorites */
if ($msFavorites = $modx->getService('msfavorites.msFavorites', '', MODX_CORE_PATH . 'components/msfavorites/model/')) {
    return $msFavorites->processEvent($modx->event, $scriptProperties);
}