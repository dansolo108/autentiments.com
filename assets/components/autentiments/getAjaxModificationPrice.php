<?php
require_once dirname(__FILE__, 4) . '/config.core.php';
require_once MODX_CORE_PATH . 'components/gitmodx/model/gitmodx/gitmodx.class.php';
$modx = new gitModx();
$modx->initialize();

$id = (int)$_POST['id'];
$modification = $modx->runSnippet('getModifications', [
    'where'=>[
        'Modification.id' => $id,
    ],
    'details'=>[
        'color','size'
    ],
    'groupby'=>['Modification.id'],
]);
exit(json_encode($modification[0]));
