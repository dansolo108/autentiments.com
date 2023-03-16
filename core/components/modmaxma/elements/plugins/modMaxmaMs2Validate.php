<?php
/** @var $key string */
/** @var $value string|integer */
/** @var $modx gitModx */
/** @var $order msOrderCustom */

if(($key == "promocode" || $key == "bonuses") && $value){
    /** @var modNamespace $ns */
    $ns = $modx->getObject("modNamespace","modmaxma");
    /** @var modMaxma $maxma */
    $maxma = $modx->getService("modmaxma","modMaxma",$ns->getCorePath()."/model/");

    if($key == "promocode"){
        $response = $maxma->calculateCurrentOrder(0,$value);
        if(!$response){
            $modx->event->_output = "empty error";
            return;
        }
        $response = $response["calculationResult"];
        if(!$response["promocode"]["applied"]){
            $modx->event->_output = $response["promocode"]["error"]["description"];
            return;
        }
    }else{
        $response = $maxma->calculateCurrentOrder("auto");
        if(!$response){
            $modx->event->_output = "empty error";
            return;
        }
        $response = $response["calculationResult"];
        if($response["bonuses"]["error"]){
            $modx->event->_output = $response["bonuses"]["error"]["description"];
            return;
        }
        $value = $response["bonuses"]["applied"];
    }
    $modx->event->returnedValues["value"] = $value;
    $modx->event->returnedValues["key"] = $key;
}
