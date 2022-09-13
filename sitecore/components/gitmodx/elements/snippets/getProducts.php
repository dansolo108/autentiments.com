<?php
/** @var modX $modx */
/** @var array $scriptProperties */
/** @var miniShop2 $miniShop2 */
/** @var pdoFetch $pdoFetch */
$miniShop2 = $modx->getService('miniShop2');
$miniShop2->initialize($modx->context->key);
$fqn = $modx->getOption('pdoFetch.class', null, 'pdotools.pdofetch', true);
$path = $modx->getOption('pdofetch_class_path', null, MODX_CORE_PATH . 'components/pdotools/model/', true);
if ($pdoClass = $modx->loadClass($fqn, $path, false, true)) {
    $pdoFetch = new $pdoClass($modx, $scriptProperties);
} else {
    return false;
}
$pdoFetch->addTime('pdoTools loaded.');

if (isset($parents) && $parents === '') {
    $scriptProperties['parents'] = $modx->resource->id;
}

if (!empty($returnIds)) {
    $scriptProperties['return'] = 'ids';
}

if ($scriptProperties['return'] === 'ids') {
    $scriptProperties['returnIds'] = true;
}

// Start build "where" expression
$where = array(
    'msProduct.class_key' => 'msProduct',
    'Modification.hide:!='=>1,
);

// Add grouping
$groupby = [
    'Modification.product_id',
    'DetailColor.value'
];

// Join tables
$leftJoin = [
    'msProduct'=>[
        'class'=>'msProduct',
        'on'=>'Modification.product_id = msProduct.id',
    ],
    'DetailColor' => [
        'class' => 'ModificationDetail',
        'on' => "DetailColor.modification_id = Modification.id AND DetailColor.name = 'Цвет'"
    ],
];
$rightJoin = [
    'File'=> [
        'class' => 'msProductFile',
        'on' => 'File.product_id = Modification.product_id AND DetailColor.value = File.description'
    ],
];
$select =
    array(
    'Modification'=>'`Modification`.`id` AS `modification_id`,  `Modification`.`price`, `Modification`.`old_price`, `Modification`.`hide`',
    'msProduct' => !empty($includeContent)
        ? $modx->getSelectColumns('msProduct', 'msProduct','')
        : $modx->getSelectColumns('msProduct', 'msProduct', '', ['content'], true),
    'Detail'=>"DetailColor.value AS color",
    'File'=>'File.url AS image',
);

// Include thumbnails
if (!empty($includeThumbs)) {
    $thumbs = array_map('trim', explode(',', $includeThumbs));
    foreach ($thumbs as $thumb) {
        if (empty($thumb)) {
            continue;
        }
        $leftJoin[$thumb] = array(
            'class' => 'msProductFile',
            'on' => "`{$thumb}`.product_id = msProduct.id AND `{$thumb}`.rank = 0 AND `{$thumb}`.path LIKE '%/{$thumb}/%'",
        );
        $select[$thumb] = "`{$thumb}`.url as `{$thumb}`";
        $groupby[] = "`{$thumb}`.url";
    }
}

// Include linked products
$innerJoin = array();
if (!empty($link) && !empty($master)) {
    $innerJoin['Link'] = array(
        'class' => 'msProductLink',
        'on' => 'msProduct.id = Link.slave AND Link.link = ' . $link,
    );
    $where['Link.master'] = $master;
} elseif (!empty($link) && !empty($slave)) {
    $innerJoin['Link'] = array(
        'class' => 'msProductLink',
        'on' => 'msProduct.id = Link.master AND Link.link = ' . $link,
    );
    $where['Link.slave'] = $slave;
}

// Add user parameters
foreach (array('where', 'leftJoin', 'innerJoin', 'select', 'groupby') as $v) {
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

// Add filters by options
$joinedOptions = array();
if (!empty($scriptProperties['optionFilters'])) {
    $filters = json_decode($scriptProperties['optionFilters'], true);
    foreach ($filters as $key => $value) {
        $components = explode(':', $key, 2);

        if (count($components) === 2) {
            if (in_array(strtolower($components[0]), ['or', 'and'])) {
                list($operator, $key) = $components;
            }
        }

        $option = preg_replace('#\:.*#', '', $key);
        $key = str_replace($option, $option . '.value', $key);

        if (!in_array($option, $joinedOptions)) {
            $leftJoin[$option] = array(
                'class' => 'msProductOption',
                'on' => "`{$option}`.product_id = Data.id AND `{$option}`.key = '{$option}'",
            );
            $joinedOptions[] = $option;
        }

        $index = isset($operator) && in_array(strtolower($operator), ['or', 'and'], true)
            ? sprintf('%s:%s', strtoupper($operator), $key)
            : $key;
        $where[$index] = $value;
    }
}

// Add sort by options
if (!empty($scriptProperties['sortbyOptions'])) {
    $sorts = array_map('trim', explode(',', $scriptProperties['sortbyOptions']));
    foreach ($sorts as $sort) {
        $sort = explode(':', $sort);
        $option = $sort[0];
        if (preg_match("#\b{$option}\b#", $scriptProperties['sortby'], $matches)) {
            $type = 'string';
            if (isset($sort[1])) {
                $type = $sort[1];
            }
            switch ($type) {
                case 'number':
                case 'decimal':
                    $sortbyOptions = "CAST(`{$option}`.`value` AS DECIMAL(13,3))";
                    break;
                case 'int':
                case 'integer':
                    $sortbyOptions = "CAST(`{$option}`.`value` AS UNSIGNED INTEGER)";
                    break;
                case 'date':
                case 'datetime':
                    $sortbyOptions = "CAST(`{$option}`.`value` AS DATETIME)";
                    break;
                default:
                    $sortbyOptions = "`{$option}`.`value`";
                    break;
            }
            $scriptProperties['sortby'] = preg_replace("#\b{$option}\b#", $sortbyOptions, $scriptProperties['sortby']);
            $groupby[] = "`{$option}`.value";
        }

        if (!in_array($option, $joinedOptions)) {
            $leftJoin[$option] = array(
                'class' => 'msProductOption',
                'on' => "`{$option}`.product_id = Data.id AND `{$option}`.key = '{$option}'",
            );
            $joinedOptions[] = $option;
        }

    }
}

$default = array(
    'class' => 'Modification',
    'where' => $where,
    'leftJoin' => $leftJoin,
    'rightJoin'=>$rightJoin,
    'select' => $select,
    //'sortby' => ["stikRemains.price"=>"DESC","msProduct.id"=>'RAND()'],
    'groupby' => implode(', ', $groupby),
    'return' => 'data',
    'nestedChunkPrefix' => 'minishop2_',
);
// Merge all properties and run!
$pdoFetch->setConfig(array_merge($default, $scriptProperties), false);
$rows = $pdoFetch->run();
//if(!empty($showLog)){
//    $pdoFetch->setConfig(array_merge($default, $scriptProperties,['return'=>'sql']), false);
//    $sql = $pdoFetch->run();
//    $modx->log(1,var_export(['sql'=>$sql,'scriptProperties'=>array_merge($default, $scriptProperties)],1));
//}
// Process rows
$output = array();
if (!empty($rows) && is_array($rows)) {
    $opt_time = 0;
    foreach ($rows as $k => $row) {
        $row['price'] = $miniShop2->formatPrice($row['price']);
        $row['old_price'] = $miniShop2->formatPrice($row['old_price']);
        $row['weight'] = $miniShop2->formatWeight($row['weight']);
        $row['idx'] = $pdoFetch->idx++;
        $opt_time_start = microtime(true);
        $options = $modx->call('msProductData', 'loadOptions', array($modx, $row['id']));
        $row = array_merge($row, $options);
        $opt_time += microtime(true) - $opt_time_start;

        $tpl = $pdoFetch->defineChunk($row);
        $output[] = $pdoFetch->getChunk($tpl, $row);
    }
    $pdoFetch->addTime('Time to load products options', $opt_time);
}

$log = '';
if ($modx->user->hasSessionContext('mgr') && !empty($showLog)) {
    $log .= '<pre class="msProductsLog">' . print_r($pdoFetch->getTime(), 1) . '</pre>';
}

// Return output
if (is_string($rows)) {
    $modx->setPlaceholder('msProducts.log', $log);
    if (!empty($toPlaceholder)) {
        $modx->setPlaceholder($toPlaceholder, $rows);
    } else {
        return $rows;
    }
} elseif (!empty($toSeparatePlaceholders)) {
    $output['log'] = $log;
    $modx->setPlaceholders($output, $toSeparatePlaceholders);
} else {
    if (empty($outputSeparator)) {
        $outputSeparator = "\n";
    }
    $output['log'] = $log;
    $output = implode($outputSeparator, $output);

    if (!empty($tplWrapper) && (!empty($wrapIfEmpty) || !empty($output))) {
        $output = $pdoFetch->getChunk($tplWrapper, array(
            'output' => $output,
        ));
    }

    if (!empty($toPlaceholder)) {
        $modx->setPlaceholder($toPlaceholder, $output);
    } else {
        return $output;
    }
}