<?php
header('Content-Type: application/json; charset=UTF-8');
define('MODX_API_MODE', true);
require_once dirname(dirname(dirname(dirname(__FILE__)))) . '/index.php';

$modx->getService('error','error.modError');
$modx->setLogLevel(modX::LOG_LEVEL_ERROR);
$modx->setLogTarget('FILE');

/** @var MsMC $msmc */
$msmc = $modx->getService('msmulticurrency','MsMC',$modx->getOption('msmulticurrency.core_path',null, $modx->getOption('core_path').'components/msmulticurrency/').'model/msmulticurrency/', array());

if ($_SERVER['HTTP_X_REQUESTED_WITH'] != 'XMLHttpRequest') {
    $modx->sendRedirect($modx->makeUrl($modx->getOption('site_start'),'','','full'));
}
elseif (!empty($_REQUEST['controller'])) {
        $out = array('result'=>false);
        if($controller = $msmc->loadController(ucfirst($_REQUEST['controller']))){
            unset($_REQUEST['controller']);
            $out =  $controller->run($_REQUEST);
        } else {
            $out['error'] = array('controller'=>'invalid load controller');
        }
        echo $modx->toJSON($out);
}
@session_write_close();

/**
 * Example Ajax request
 *
 *
    $.ajax({
        dataType: 'json',
        type: 'POST',
        cache: false,
        url: '/assets/components/msmulticurrency/action.php',
        data: {
            controller: 'controller_name',  //required
            action: 'create',               //required; default: create|update|data|list|remove
            name: 'my_name',
            email:'my_email'
        },
        success: function (e) {
            if (e.result == true) {
                console.log(e.data);
            } else {
                for(var key in e.error){
                    console.log(e.error[key]);
                }
            }
        },
        error: function (e) {
            console.log('error',e);
        }
    });
 *
 *
 */