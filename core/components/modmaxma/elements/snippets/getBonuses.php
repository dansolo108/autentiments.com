<?php
/** @var modX $modx */
/** @var array $scriptProperties */
/** @var modMaxma $maxma */
/** @var modNamespace $ns */
$user = $modx->getAuthenticatedUser("web");
if(!$user){
    return "";
}
/** @var modNamespace $ns */
$ns = $modx->getObject("modNamespace","modmaxma");
/** @var modMaxma $maxma */
$maxma = $modx->getService("modmaxma","modMaxma",$ns->getCorePath()."/model/");
if(!($user instanceof maxmaUser)){
    $user->set("class_key","maxmaUser");
    $user->save();
    $user = $modx->getObject("maxmaUser",$user->get("id"));
}
return  $user->getMaxmaBalance();
