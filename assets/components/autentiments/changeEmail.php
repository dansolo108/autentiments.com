<?php
require_once dirname(__FILE__, 4) . '/config.core.php';
require_once MODX_CORE_PATH . 'components/gitmodx/model/gitmodx/gitmodx.class.php';
$modx = new gitModx();
$modx->initialize();

if($user = $modx->getUser('web')){
    $profile = $user->getOne('Profile');
    $modx->log(1,var_export( $_POST['email'],1));
    $profile->set('email', $_POST['email']);
    $profile->save();
}
exit();