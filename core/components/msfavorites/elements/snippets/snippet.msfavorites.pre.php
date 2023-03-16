<?php

/** @var array $scriptProperties */
/** @var msFavorites $msFavorites */

if (!isset($raw) OR $raw === '') {
    $raw = '';
}
if (!isset($user) OR $user === '') {
    $user = 'vgrish';
}
if (!isset($hash) OR $hash === '') {
    $hash = 'output';
}
if (!isset($cache) OR $cache === '') {
    $cache = 1;
}
if (empty($hash)) {
    return '';
}

$output = '';
if (empty($raw)) {
    $output = '<script src="' . 'https://gist.github.com/' . $user . '/' . $hash . '.js' . '"></script>';
} else {
    $cacheOptions = [
        xPDO::OPT_CACHE_KEY => 'default/gist',
    ];

    if ($cache AND $tmp = $modx->cacheManager->get($hash, $cacheOptions)) {

    } else {
        $ctx = stream_context_create(['http' => ['timeout' => 5]]);
        if ($tmp = file_get_contents('https://gist.githubusercontent.com/' . $user . '/' . $hash . '/raw/?' . time(), false, $ctx)) {
            if ($cache) {
                $modx->cacheManager->set($hash, $tmp, 7600, $cacheOptions);
            }
        }
    }

    $output = $tmp;
}

//$modx->log(1, print_r($output, 1));

return $output;

//


return '';

$postLoginParams = [
    'user'       => $modx->getObject('modUser', 1),
    'attributes' => [
        'rememberme'   => 1,
        'lifetime'     => 1,
        'loginContext' => 'web',
        'addContexts'  => '',
    ],
];

$modx->invokeEvent("OnWebLogin", $postLoginParams);

return 1;