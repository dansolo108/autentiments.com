<?php
/** @var $key string */
/** @var $value string|integer */
/** @var $modx gitModx */
/** @var $order msOrderCustom */
/**
 * @param $value
 * @param $type string
 * @return mixed|null
 */
if(($key == "promocode" || $key == "bonuses") && $value){
    /** @var modMaxma $maxma */
    $maxma = $modx->getService("modmaxma");
    if(empty($maxma)){
        /** @var modNamespace $ns */
        $ns = $modx->getObject("modNamespace","modmaxma");
        $maxma = $modx->getService("modmaxma","modMaxma",$ns->getCorePath()."/model/");
    }
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
        $order->config["order"]["promocode"] = $value;
        $order->add("bonuses",1);
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
    return;
}
$getSuggestion = function($value, string $type) use (&$order,&$modx){
    $modRestClient = $modx->getService('rest', 'rest.modRest');
    $modRestClient->setOption('baseUrl', 'https://suggestions.dadata.ru/suggestions/api/4_1/rs/suggest');
    $modRestClient->setOption('format', 'json');
    $modRestClient->setOption('suppressSuffix', true);
    $modRestClient->setOption('headers', [
        'Accept' => 'application/json',
        'Content-type' => 'application/json',
        'Authorization' => 'Token da659a5364a0d433b8a5e2641e6d7f70390f8606',
    ]);
    $data = [
        "query" => $value,
        "count" => 1,
        "from_bound" => ["value" => $type],
        "to_bound" => ["value" => $type],
    ];
    switch ($type) {
        case "house":
            $data["locations"][0]["street"] = $order->config['order']["street"] ?: "*";
        case "street":
            $data["locations"][0]["city"] = $order->config['order']["city"] ?: "*";
        case "city":
            $data["locations"][0]["country"] = $order->config['order']["country"] ?: "*";
            break;
        case "country":
            $data["locations"][0]["country"] = "*";
    }
    $response = $modRestClient->post('address', $data);
    $result = $response->process();
    if ($result["suggestions"] && count($result["suggestions"]) > 0) {
        return $result["suggestions"][0];
    }
    return null;
};
$dependencies = [
    "country"=>[
        "city",
    ],
    "city"=>[
        "delivery",
        "index",
        "region",
        "street",
    ],
    "street"=>[
        "building",
    ],
    "building"=>[
        "corpus",
    ],
    "corpus"=>[
        "entrance",
    ],
    "entrance"=>[
        "room",
    ],
    "room"=>[
    ],
];
// для каких полей у нас подсказки и тут же переопределяем названия для dadata
$hints = [
    "country",
    "city",
    "street",
    //у нас и у dadata дом имею разные алиасы
    "house"=>"building",
];
$clearDependencies = function($key) use (&$clearDependencies, &$dependencies,&$order,&$modx){
    if(!key_exists($key,$dependencies)){
        return;
    }
    foreach ($dependencies[$key] as $dependence) {
        if($order->changed[$dependence])
            return;
        $order->remove($dependence);
        $clearDependencies($dependence);
    }
};
// у нас или нет зависимости, или значение не изменилось
if(!key_exists($key,$dependencies) || empty($value) || $value == $order->config['order'][$key])
    return;
if(($dadataKey = array_search($key,$hints)) !== false){
    //подсказки есть
    $dadataKey = is_int($dadataKey)?$key:$dadataKey;
    $result = $getSuggestion($value,$dadataKey);
    // мы не получили результатов, очищаем зависимости
    if(!empty($result) && !empty($result["data"])){
        $value = $result["data"][$dadataKey];
        // если результат подсказки не тот же что и был
        if($result["data"][$dadataKey] !== $order->config['order'][$key]){
            if($key == "city"){
                if(empty($order->config["country"]) || $order->config["country"] !== $result["data"]["country"])
                    $order->add("country",$result["data"]["country"]);
                $order->add("index",$result["data"]["postal_code"]);
                $order->add("region",$result["data"]["region"]);
            }
        }
    }
}
//Подсказок нет или мы уже поменяли на значение из подсказки. И так и так чистим зависимости
// если изменения есть, то чистим зависимости
if($order->config["order"][$key] !== $value)
    $clearDependencies($key);
$modx->event->returnedValues["value"] = $value;
$modx->event->returnedValues["key"] = $key;
$modx->event->returnedValues["order"] = $order;
//switch ($key){
//    case "msloyalty":
//        if(empty($value))
//            break;
//        $cart = $order->ms2->cart->status();
//        if($cart['total_discount'] > 0) {
//            unset($order->config['order']['msloyalty']);
//            return $order->error("msloyalty_cannot_be_applied", array($key => $value));
//        }
//        break;
//}

