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
    switch ($_POST["msCalcDelivery_action"]){
        case "getPickupPoints":
            if(empty($_POST["delivery_id"])){
                $result = [
                    "success"=>false,
                    "message"=>"не указан ид доставки"
                ];
                break;
            }
            /** @var msDelivery $delivery */
            $delivery = $modx->getObject("msDelivery",$_POST["delivery_id"]);
            if(empty($delivery)){
                $result = [
                    "success"=>false,
                    "message"=>"доставка не найдена"
                ];
                break;
            }
            if(!($delivery->handler instanceof PickupPointsInterface)){
                $result = [
                    "success"=>false,
                    "message"=>"У этой доставки нет пунктов выдачи"
                ];
                break;
            }
            /** @var miniShop2 $ms2 */
            $ms2 = $modx->getService("minishop2");
            $ms2->initialize();
            $result = $delivery->handler->getPickupPoints($ms2->order);
            $result = [
                "success"=>true,
                "message"=>"",
                "data"=>$result,
            ];
            $result = json_encode($result);
        case "getDeliveries":
            $result = $modx->runSnippet('msCalcDelivery');
    }
}


@session_write_close(); exit($result);