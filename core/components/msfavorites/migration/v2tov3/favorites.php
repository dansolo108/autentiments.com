<?php

/** @noinspection PhpIncludeInspection */
include_once dirname(__DIR__) . '/connect.php';
include_once __DIR__ . '/inject.php';

/** @var msFavorites $msFavorites */
if (!$msFavorites = $modx->getService('msfavorites.msFavorites', '', MODX_CORE_PATH . 'components/msfavorites/model/')) {
    exit('msFavorites not found');
}

if (!$modx->loadClass('msFavorite')) {
    exit();
}
if (!$modx->loadClass('msfFavoriteObject')) {
    exit();
}

// удаляем временные
$modx->removeCollection('msFavorite', [
    'temporary' => true,
]);

$idx = $positive = $negative = 0;
/** @var xPDOObject[] $instances */
$instances = $modx->getIterator('msFavorite');
if ($instances) {
    foreach ($instances as $instance) {
        $idx++;

        $pls = [
            'type'      => 'resource',
            'list'      => $instance->get('list'),
            'key'       => $instance->get('rid'),
            'uid'       => $instance->get('uid'),
            'temp'      => $instance->get('temporary'),
            'createdon' => $instance->get('timestamp'),
            'extra'     => $instance->get('properties'),
        ];

        $list = $modx->getObject('msfFavoriteList', $pls);
        $key = $modx->getObject('msfFavoriteKey', $pls);

        if (!$list OR !$key) {
            $negative++;
            continue;
        }

        /** @var msfFavoriteObject $object */
        $object = $modx->getObject('msfFavoriteObject', ['lid' => $list->get('id'), 'kid' => $key->get('id'), 'uid' => $pls['uid']]);
        if ($object) {
            $object->set('anon', 0);
            $object->set('temp', 0);
            $object->set('createdon', $pls['createdon']);
            $object->set('extra', $pls['extra']);
            if ($object->save()) {
                $positive++;
            }
        }

    }
}

if (!XPDO_CLI_MODE) {
    echo '<pre>';
}
echo "\nImport complete in " . number_format(microtime(true) - $modx->startTime, 7) . " s\n";
echo "\nTotal idx:	$idx\n";
echo "Positive:	$positive\n";
echo "Negative:	$negative\n";
if (!XPDO_CLI_MODE) {
    echo '</pre>';
}



/*
 * include MODX_CORE_PATH .'components/msfavorites/migration/v2tov3/favorites.php';
 */