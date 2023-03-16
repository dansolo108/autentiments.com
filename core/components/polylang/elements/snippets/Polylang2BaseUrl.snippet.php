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

$url = $modx->getOption('url', $scriptProperties);

if (!empty($input) && empty($url)) {
    $url = $input;
}

if (empty($url) || $tools->isCurrentDefaultLanguage()) return $url;

$cultureKey = $modx->getOption('cultureKey');
$defaultSiteUrl = $modx->getOption('polylang_default_site_url', null, MODX_SITE_URL, true);
$defaultSiteUrl = preg_quote($defaultSiteUrl, '/');

return preg_replace(array(
    "/^(\/)?({$cultureKey}\/)/",
    "/({$defaultSiteUrl})({$cultureKey}\/)/",
), array('$1', '$1'), $url);