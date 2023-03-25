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
function success($message,$data = []){
    die(json_encode([
        "success"=>true,
        "message"=>$message,
        "data"=>$data,
    ]));
}
function error($message,$data = []){
    die(json_encode([
        "success"=>false,
        "message"=>$message,
        "data"=>$data,
    ]));
}
/** @var $modx modX */
if($_SERVER['REQUEST_METHOD'] == "POST"){
    switch ($_POST["msCalcDelivery_action"]){
        case "getPickupPoints":
            if(empty($_POST["delivery_id"])){
                error("не указан ид доставки");
                break;
            }
            /** @var msDelivery $delivery */
            $delivery = $modx->getObject("msDelivery",$_POST["delivery_id"]);
            if(empty($delivery)){
                error("доставка не найдена");
                break;
            }
            $delivery->loadHandler();
            if(!($delivery->handler instanceof PickupPointsInterface)){
                error("У этой доставки нет пунктов выдачи");
                break;
            }
            /** @var miniShop2 $ms2 */
            $ms2 = $modx->getService("minishop2");
            $ms2->initialize();
            $result = $delivery->handler->getPickupPoints($ms2->order);
            success("",[
                "active"=>$ms2->order->get()["point"]?:"",
                "points"=>$result,
            ]);

        case "getDeliveries":
            $result = $modx->runSnippet('msCalcDelivery');
            success("",$result);
    }
}
@session_write_close();
error("неизвестная ошибка");