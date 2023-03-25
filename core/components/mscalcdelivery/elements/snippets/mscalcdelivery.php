<?php
/** @var modX $modx */
/** @var array $scriptProperties */
/** @var modNamespace $ns */
$ns = $modx->getObject("modNamespace",["name"=>"mscalcdelivery"]);
/** @var msCalcDelivery $msCalcDelivery */
$msCalcDelivery = $modx->getService('msCalcDelivery','msCalcDelivery',$ns->getCorePath()."model/",$scriptProperties);
if (!$msCalcDelivery)
    return 'Could not load msCalcDelivery class!';

/** @var miniShop2 $ms2 */
$ms2 = $modx->getService('miniShop2');
if (!$ms2)
    return 'Could not load minishop class!';

$ms2->initialize();
/** @var pdoFetch $pdoFetch */
$pdoFetch = $modx->getService("pdoFetch");
if (!$pdoFetch)
    return 'Could not load pdoFetch class!';
//vars
$emptyTpl = $msCalcDelivery->config["emptyTpl"];
$tpl = $msCalcDelivery->config["tpl"];
$order = $ms2->order;
$orderData = $order->get();
$active = $orderData["delivery"];
$payment = $orderData["payment"];
// get deliveries
$q = ["active"=>1];
if($payment){
    $q["payment"] = $payment;
}
$deliveries = $modx->getCollection("msDelivery",$q);
$output = "";
/** @var $delivery msDelivery */
foreach($deliveries as &$delivery){
    $cost = $delivery->getCost($order);
    if($cost === null)
        continue;
    $deliveryArr = $delivery->toArray();
    if($delivery->handler instanceof PickupPointsInterface){
        $deliveryArr["hasPickupPoints"] = true;
    }
    if(is_array($cost)){
        $deliveryArr["max"] = $cost[2];
        $deliveryArr["min"] = $cost[1];
        $cost = $cost[0];
    }
    $deliveryArr["cost"] = $cost;
    if($active == $deliveryArr["id"]){
        $deliveryArr["checked"] = true;
    }
    if(!empty($tpl)){
        $output .= $pdoFetch->parseChunk($tpl,$deliveryArr);
    }
}
// готовим js конфиг на фронт
$js_settings = [
    'action_url'=>$msCalcDelivery->config["action_url"],
];
// переопределяем на то что получили
$js_settings = array_merge($js_settings,array_intersect($js_settings,$scriptProperties));
if(!empty($selectors) && is_array($selectors) && count($selectors) > 0){
    $js_settings["selectors"] = $selectors;
}
$data = json_encode($js_settings);
$modx->regClientHTMLBlock(
    "<script>
    document.addEventListener('miniShop2Initialize',e=>{
        window.miniShop2.msCalcDelivery = new msCalcDelivery({$data});
    })
    </script>");
$modx->regClientStartupScript('https://api-maps.yandex.ru/2.1/?lang=ru_RU');
if(!empty($tpl)){
    if(trim($output) == "" && !empty($emptyTpl))
        return $pdoFetch->parseChunk($emptyTpl);

    return $output;
}else{
    return $deliveries;
}
