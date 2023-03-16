<?php
/** @var $modx modX */
/** @var modNamespace $ns */
$ns = $modx->getObject("modNamespace","modmaxma");
/** @var modMaxma $maxma */
$maxma = $modx->getService("modmaxma","modMaxma",$ns->getCorePath()."/model/");
$response = $maxma->calculateCurrentOrder();
if($response && $response["calculationResult"]["bonuses"]){
    $maxToApply = $response["calculationResult"]["bonuses"]["maxToApply"];
    if(!empty($tpl)){
        /** @var pdoFetch $pdoFetch */
        $pdoFetch = $modx->getService("pdoFetch");
        $output = $pdoFetch->parseChunk($tpl,["maxToApply"=>$maxToApply]);
        return $output;
    }
    return $maxToApply;
}
return null;