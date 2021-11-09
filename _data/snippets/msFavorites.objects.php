id: 55
source: 1
name: msFavorites.objects
category: msFavorites
properties: 'a:6:{s:4:"list";a:7:{s:4:"name";s:4:"list";s:4:"desc";s:21:"msfavorites_prop_list";s:4:"type";s:9:"textfield";s:7:"options";a:0:{}s:5:"value";s:7:"default";s:7:"lexicon";s:22:"msfavorites:properties";s:4:"area";s:0:"";}s:4:"type";a:7:{s:4:"name";s:4:"type";s:4:"desc";s:21:"msfavorites_prop_type";s:4:"type";s:9:"textfield";s:7:"options";a:0:{}s:5:"value";s:8:"resource";s:7:"lexicon";s:22:"msfavorites:properties";s:4:"area";s:0:"";}s:3:"uid";a:7:{s:4:"name";s:3:"uid";s:4:"desc";s:20:"msfavorites_prop_uid";s:4:"type";s:9:"textfield";s:7:"options";a:0:{}s:5:"value";s:0:"";s:7:"lexicon";s:22:"msfavorites:properties";s:4:"area";s:0:"";}s:6:"sortby";a:7:{s:4:"name";s:6:"sortby";s:4:"desc";s:23:"msfavorites_prop_sortby";s:4:"type";s:9:"textfield";s:7:"options";a:0:{}s:5:"value";s:20:"{"createdon": "ASC"}";s:7:"lexicon";s:22:"msfavorites:properties";s:4:"area";s:0:"";}s:9:"returnIds";a:7:{s:4:"name";s:9:"returnIds";s:4:"desc";s:26:"msfavorites_prop_returnIds";s:4:"type";s:13:"combo-boolean";s:7:"options";a:0:{}s:5:"value";b:0;s:7:"lexicon";s:22:"msfavorites:properties";s:4:"area";s:0:"";}s:13:"toPlaceholder";a:7:{s:4:"name";s:13:"toPlaceholder";s:4:"desc";s:30:"msfavorites_prop_toPlaceholder";s:4:"type";s:9:"textfield";s:7:"options";a:0:{}s:5:"value";s:0:"";s:7:"lexicon";s:22:"msfavorites:properties";s:4:"area";s:0:"";}}'
static_file: core/components/msfavorites/elements/snippets/snippet.msfavorites.objects.php

-----

/** @var array $scriptProperties */
/** @var msFavorites $msFavorites */
if (!$msFavorites = $modx->getService('msfavorites.msFavorites', '', MODX_CORE_PATH . 'components/msfavorites/model/')) {
    return 'Could not load msFavorites class!';
}
/** @var pdoFetch $pdoFetch */
if (!$modx->loadClass('pdofetch', MODX_CORE_PATH . 'components/pdotools/model/pdotools/', false, true)) {
    return false;
}
$pdoFetch = new pdoFetch($modx, $scriptProperties);
$pdoFetch->addTime('pdoTools loaded.');


if (!empty($returnIds)) {
    $return = 'ids';
} elseif (!isset($return) OR $return === '') {
    $return = 'data';
}
if ($return === 'ids') {
    $returnIds = 1;
}

if (!isset($list) OR $list == '') {
    $list = 'default';
}
if (!isset($type) OR $type == '') {
    $type = 'resource';
}
if (!isset($uid) OR $uid == '') {
    $uid = $modx->user->isAuthenticated($modx->context->key) ? $modx->user->id : session_id();
}
if (!isset($outputSeparator)) {
    $outputSeparator = "\n";
}
if (!isset($processAll)) {
    $processAll = '';
}
if (!isset($processObjectExtra)) {
    $processObjectExtra = '';
}
if (!isset($groupKey)) {
    $groupKey = '';
}

// Start build "where" expression
$where = [];

if (!empty($list)) {
    $list = is_array($list) ? $list : array_map('trim', explode(',', $list));
    $where['FavoriteList.list:IN'] = $list;
}
if (!empty($type)) {
    $type = is_array($type) ? $type : array_map('trim', explode(',', $type));
    $where['FavoriteList.type:IN'] = $type;
}
if (!empty($uid)) {
    $uid = is_array($uid) ? $uid : array_map('trim', explode(',', $uid));
    $where['msfFavoriteObject.uid:IN'] = $uid;
}

if (!empty($ids)) {
    $ids = is_array($ids) ? $ids : array_map('trim', explode(',', $ids));
    $ids_out = $ids_in = [];
    foreach ($ids as $v) {
        if ($v == '') {
            continue;
        }
        if ($v[0] == '-') {
            $ids_out[] = substr($v, 1);
        } else {
            $ids_in[] = $v;
        }
    }
    if (!empty($ids_in)) {
        $where['FavoriteKey.key:IN'] = $ids_in;
    }
    if (!empty($ids_out)) {
        $where['FavoriteKey.key:NOT IN'] = $ids_out;
    }
}

// Add grouping
$groupby = [];

// Join tables
$leftJoin = [
    'FavoriteList' => ['class' => 'msfFavoriteList'],
    'FavoriteKey'  => ['class' => 'msfFavoriteKey'],
];

$innerJoin = [];

$select = [
    'msfFavoriteObject' => $modx->getSelectColumns('msfFavoriteObject', 'msfFavoriteObject', '', ['lid', 'kid'], true),//
    'FavoriteList'      => $modx->getSelectColumns('msfFavoriteList', 'FavoriteList', '', ['id'], true),
    'FavoriteKey'       => $modx->getSelectColumns('msfFavoriteKey', 'FavoriteKey', '', ['key'], false),
];

if (!empty($processObjectExtra) AND empty($returnIds)) {
    $leftJoin['FavoriteObjectExtra'] = [
        'class' => 'msfFavoriteObjectExtra',
        'on'    => 'FavoriteObjectExtra.lid=msfFavoriteObject.lid AND FavoriteObjectExtra.kid=msfFavoriteObject.kid AND FavoriteObjectExtra.uid=msfFavoriteObject.uid',
    ];
    $select['FavoriteObjectExtra'] = $modx->getSelectColumns('msfFavoriteObjectExtra', 'FavoriteObjectExtra', '', ['extra'], false);
}

// Add user parameters
foreach (['where', 'leftJoin', 'innerJoin', 'select', 'groupby'] as $v) {
    if (!empty($scriptProperties[$v])) {
        $tmp = $scriptProperties[$v];
        if (!is_array($tmp)) {
            $tmp = json_decode($tmp, true);
        }
        if (is_array($tmp)) {
            $$v = array_merge($$v, $tmp);
        }
    }
    unset($scriptProperties[$v]);
}
$pdoFetch->addTime('Conditions prepared');

$config = array_merge([
    'class'             => 'msfFavoriteObject',
    'where'             => $where,
    'leftJoin'          => $leftJoin,
    'innerJoin'         => $innerJoin,
    'select'            => $select,
    'sortby'            => 'createdon',
    'sortdir'           => 'ASC',
    'groupby'           => implode(', ', $groupby),
    'nestedChunkPrefix' => 'msfavorites_',
], $scriptProperties, ['return' => 'data']);
$pdoFetch->setConfig($config, false);
$rows = $pdoFetch->run();


// Process rows
if (!empty($rows) AND is_array($rows)) {
    $rowsGroup = [];
    foreach ($rows as $k => $row) {
        $row['idx'] = $pdoFetch->idx++;

        foreach (['extra'] as $s) {
            $row[$s] = !empty($row[$s]) ? unserialize($row[$s]) : [];
        }

        // group
        if (empty($groupKey)) {
            if (empty($returnIds)) {
                $rows[$k] = $row;
            } else {
                $rows[$k] = $row['key'];
            }
        } else {
            if (!isset($rowsGroup[$row[$groupKey]])) {
                $rowsGroup[$row[$groupKey]] = [];
            }

            if (empty($returnIds)) {
                $rowsGroup[$row[$groupKey]][] = $row;
            } else {
                $rowsGroup[$row[$groupKey]][] = $row['key'];
            }
        }
    }

    // group
    if (!empty($groupKey)) {
        $rows = $rowsGroup;
        if (!empty($returnIds)) {
            $rows = call_user_func_array('array_merge', $rows);
        }
    }

}


$log = '';
if ($modx->user->hasSessionContext('mgr') && !empty($showLog)) {
    $log .= '<pre class="msFavoritesLog">' . print_r($pdoFetch->getTime(), 1) . '</pre>';
}

$output = [];
switch ($return) {
    case 'ids':
        $output = is_string($rows) ? $rows : implode(',', $rows);
        $modx->setPlaceholder('msFavorites.log', $log);
        if (!empty($toPlaceholder)) {
            $modx->setPlaceholder($toPlaceholder, $output);
            $output = '';
        }
        break;
    case 'data':
        $output = $rows;
        break;
    case 'json':
        $output = json_encode($rows, true);
        break;
    default:
        if (!empty($processAll)) {
            $output[] = $pdoFetch->getChunk($tpl, ['rows' => $rows]);
        } else {
            foreach ($rows as $row) {
                $tpl = $pdoFetch->defineChunk($row);
                $output[] = $pdoFetch->getChunk($tpl, $row);
            }
        }

        if (!empty($toSeparatePlaceholders)) {
            $output['log'] = $log;
            $modx->setPlaceholders($output, $toSeparatePlaceholders);
            $output = '';
        } else {
            $output['log'] = $log;
            $output = implode($outputSeparator, $output);
            if (!empty($tplWrapper) && (!empty($wrapIfEmpty) || !empty($output))) {
                $output = $pdoFetch->getChunk($tplWrapper, [
                    'output' => $output,
                ]);
            }
            if (!empty($toPlaceholder)) {
                $modx->setPlaceholder($toPlaceholder, $output);
                $output = '';
            }
        }
        break;
}

return $output;