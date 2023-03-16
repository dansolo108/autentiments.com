<?php
/** @var $modx modX */
/** @var modNamespace $ns */
$ns = $modx->getObject("modNamespace","modmaxma");
/** @var modMaxma $maxma */
$maxma = $modx->getService("modmaxma","modMaxma",$ns->getCorePath()."/model/");
$response = $maxma->calculateCurrentOrder();
if($response && $response["calculationResult"]["bonuses"]){
    $collected = $response["calculationResult"]["bonuses"]["collected"];
    if(!empty($tpl)){
        /** @var pdoFetch $pdoFetch */
        $pdoFetch = $modx->getService("pdoFetch");
        $output = $pdoFetch->parseChunk($tpl,["collected"=>$collected]);
        return $output;
    }
    return $collected;
}
return null;