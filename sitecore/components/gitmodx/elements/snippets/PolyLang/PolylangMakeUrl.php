<?php
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