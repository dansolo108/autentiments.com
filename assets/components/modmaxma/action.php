<?php
$path = __DIR__;
while (!file_exists($path . '/config.core.php') && (strlen($path) > 1)) {
    $path = dirname($path);
}
define("MODX_API_MODE",true);
require_once  $path. '/config.core.php';
/** @noinspection PhpIncludeInspection */
require_once MODX_CORE_PATH . 'config/' . MODX_CONFIG_KEY . '.inc.php';
require_once MODX_BASE_PATH . 'index.php';
/** @var $modx modX */
if($_SERVER['REQUEST_METHOD'] == "POST"){
    /** @var miniShop2 $ms2 */
    $ms2 = $modx->getService("minishop2");

    $result = $ms2->handleRequest("order/add",["key"=>$_POST["modmaxma_action"],"value"=>$_POST["value"]]);
    @session_write_close(); exit(is_string($result)?:json_encode($result));
}
if($_SERVER['REQUEST_METHOD'] == "GET"){
    $result = [
        "message"=>"",
        "success"=>false,
    ];
    switch ($_GET["modmaxma_action"]){
        case "order_bonuses":
            $bonuses = $modx->runSnippet("getOrderBonuses");
            if(is_int($bonuses)){
                $result["message"] = "";
                $result["data"] = $bonuses;
                $result["success"] = true;
            }else{
                $result["message"] = "Ошибка при вычислении бонусов";
            }
            break;
    }

    @session_write_close(); exit(json_encode($result));
}

