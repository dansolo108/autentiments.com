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
$active = $order->get()["delivery"];
// get deliveries
$deliveries = $modx->getCollection("msDelivery",["active"=>1]);
$output = "";
/** @var $delivery msDelivery */
foreach($deliveries as &$delivery){
    $cost = $delivery->getCost($order);
    if($cost === null)
        continue;
    $delivery = $delivery->toArray();
    if(is_array($cost)){
        $delivery["max"] = $cost[2];
        $delivery["min"] = $cost[1];
        $cost = $cost[0];
    }
    $delivery["cost"] = $cost;
    if($active == $delivery["id"]){
        $delivery["checked"] = true;
    }
    if(!empty($tpl)){
        $output .= $pdoFetch->parseChunk($tpl,$delivery);
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

if(!empty($tpl)){
    if(trim($output) == "" && !empty($emptyTpl))
        return $pdoFetch->parseChunk($emptyTpl);

    return $output;
}else{
    return $deliveries;
}