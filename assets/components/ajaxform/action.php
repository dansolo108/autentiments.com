<?php
/** @var modX $modx */
define('MODX_API_MODE', true);
require_once dirname(dirname(dirname(dirname(__FILE__)))) . '/index.php';
$modx->getService('error', 'error.modError');
$modx->setLogLevel(modX::LOG_LEVEL_ERROR);
$modx->setLogTarget('FILE');

// Switch context if need
if (!empty($_REQUEST['pageId'])) {
    if ($resource = $modx->getObject('modResource', (int)$_REQUEST['pageId'])) {
        if ($resource->get('context_key') != 'web') {
            $modx->switchContext($resource->get('context_key'));
        }
        $modx->resource = $resource;
    }
}

//ROISTAT CODE BEGIN
$roistatSend = [
    'roistat' => isset($_COOKIE['roistat_visit']) ? $_COOKIE['roistat_visit'] : null,
    'key' => 'Zjg0YzEyMWQ2MTM4ZTkzNTczMTFiYTRkZmFlZjY4M2I6MjIxMDQ4',
    'title' => 'Новая заявка с сайта',
    'name' => (!empty($_REQUEST['name'])) ? $_REQUEST['name'] : '' ,
    'email' => (!empty($_REQUEST['email'])) ? $_REQUEST['email'] : '' ,
    'phone' => (!empty($_REQUEST['phone'])) ? $_REQUEST['phone'] : '' ,
    'is_skip_sending' => '0',
    'comment' => (!empty($_REQUEST['message'])) ? $_REQUEST['message'] : '',
    'fields' => [
        'Посадочная страница' => '{landingPage}',
        'Источник (Маркер)' => '{source}',
        'Город доставки' => '{city}',
        'utm_source' => '{utmSource}',
        'utm_medium' => '{utmMedium}',
        'utm_campaign' => '{utmCampaign}',
        'utm_term' => '{utmTerm}',
        'utm_content' => '{utmContent}',
    ],
];


$url = "https://cloud.roistat.com/api/proxy/1.0/leads/add?" . http_build_query($roistatSend, '', '&');
$curl = curl_init($url);
curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
curl_setopt($curl, CURLOPT_FOLLOWLOCATION, 1);
curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, 0);
curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, 0);
$result = curl_exec($curl);
curl_close($curl);
//ROISTAT CODE END

/** @var AjaxForm $AjaxForm */
$AjaxForm = $modx->getService('ajaxform', 'AjaxForm', $modx->getOption('ajaxform_core_path', null,
        $modx->getOption('core_path') . 'components/ajaxform/') . 'model/ajaxform/', array());

if (empty($_SERVER['HTTP_X_REQUESTED_WITH']) || $_SERVER['HTTP_X_REQUESTED_WITH'] != 'XMLHttpRequest') {
    $modx->sendRedirect($modx->makeUrl($modx->getOption('site_start'), '', '', 'full'));
} elseif (empty($_REQUEST['af_action'])) {
    echo $AjaxForm->error('af_err_action_ns');
} else {
    echo $AjaxForm->process($_REQUEST['af_action'], array_merge($_FILES, $_REQUEST));
}

@session_write_close();