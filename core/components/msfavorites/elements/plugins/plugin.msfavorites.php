<?php

/** @var array $scriptProperties */
/** @var msFavorites $msFavorites */
if ($msFavorites = $modx->getService('msfavorites.msFavorites', '', MODX_CORE_PATH . 'components/msfavorites/model/')) {
    return $msFavorites->processEvent($modx->event, $scriptProperties);
}