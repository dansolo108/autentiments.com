<?php
define('MODX_API_MODE', true);
if (file_exists(dirname(dirname(dirname(dirname(__FILE__)))) . '/index.php')) {
    require_once dirname(dirname(dirname(dirname(__FILE__)))) . '/index.php';
} else {
    require_once dirname(dirname(dirname(dirname(dirname(__FILE__))))) . '/index.php';
}

$modx->getRequest();
$post = $modx->request->parameters['POST'];
$response = ['success' => false, 'message' => ''];
$modx->getService('sms', 'sms', MODX_CORE_PATH . 'components/sms/model/sms/');
$modx->sms->initialize();
$modx->sms->mode = 'user';
$modx->sms->values = $post;
$mode = $post['mode'] ? $post['mode'] : 'code';
$tpl = $post['tpl'] ? $post['tpl'] : '';
switch ($post['type']) {
    case "sendCode":
        $response = $modx->sms->sendCode($tpl, $post['phone'], $mode);
        break;
    case "checkCode":
        $response = $modx->sms->checkCode($post['phone'], $post['code'], $mode);
        if ($response['success']) {
            $modx->sms->activateCode($post['phone'], $post['code'], $mode);
        }
        break;
}
exit(is_array($response) ? json_encode($response) : $response);