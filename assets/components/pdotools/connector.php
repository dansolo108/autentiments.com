<?php
define("MODX_API_MODE",true);
require_once $_SERVER['DOCUMENT_ROOT'] . '/index.php';
// Switch context if needed
if (!empty($_REQUEST['pageId'])) {
    if ($resource = $modx->getObject('modResource', ['id' => (int)$_REQUEST['pageId']])) {
        if ($resource->get('context_key') !== 'web') {
            $modx->switchContext($resource->get('context_key'));
        }
        $modx->resource = $resource;
    }
}

// Run snippet
if (!empty($_REQUEST['hash']) && !empty($_SESSION['pdoPage'][$_REQUEST['hash']])) {
    $scriptProperties = $_SESSION['pdoPage'][$_REQUEST['hash']];
    $_GET = $_POST;

    // For ClientConfig and other similar plugins
    $modx->invokeEvent('OnHandleRequest');

    $modx->runSnippet('pdoPage', $scriptProperties);
}
