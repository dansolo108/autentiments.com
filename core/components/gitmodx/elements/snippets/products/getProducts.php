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

$scriptProperties['where']['Modification.hide'] = 0;
$scriptProperties['where']['msProduct.deleted'] = 0;
$scriptProperties['where']['msProduct.published'] = 1;
$scriptProperties['where']['msProduct.class_key'] = 'msProduct';
$rows = $modx->runSnippet('getModifications',array_merge($scriptProperties,
    [
        'tpl'=>'',
        'details'=> ['color','size'],
        'sortby'=>['Modification.id','Modification.sort_index'=>"DESC"],
        'groupby'=>['Modification.product_id','color'],
    ]));
$output = array();
if (!empty($rows) && is_array($rows)) {
    $opt_time = 0;
    foreach ($rows as $k => $row) {
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