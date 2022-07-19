<?php
define('MODX_API_MODE', true);
require_once dirname(__DIR__, 3) . '/index.php';
/** @var $modx gitModx */
$modx->setLogTarget('HTML');
$modx->setLogLevel(MODX_LOG_LEVEL_INFO);
$input = json_decode(file_get_contents('php://input'),1);
$myCurl = curl_init();
curl_setopt_array($myCurl, array(
    CURLOPT_URL => $input['auditContext']['meta']['href'].'/events',
    CURLOPT_RETURNTRANSFER => true,
    CURLOPT_CUSTOMREQUEST => 'GET',
    CURLOPT_HTTPHEADER => array("Authorization: Bearer 36f61839f3f41d0c14fc10f493a44139529be7ea",
        "Content-Type: application/json"),
));
$response = json_decode(curl_exec($myCurl),1);
curl_close($myCurl);

if($response['rows'][0]['diff']['state']['newValue']['name'] == 'На выдаче'){
    $myCurl = curl_init();
    curl_setopt_array($myCurl, array(
        CURLOPT_URL => $response['rows'][0]['entity']['meta']['href'],
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_CUSTOMREQUEST => 'GET',
        CURLOPT_HTTPHEADER => array("Authorization: Bearer 36f61839f3f41d0c14fc10f493a44139529be7ea",
            "Content-Type: application/json"),
    ));
    $response = json_decode(curl_exec($myCurl),1);
    curl_close($myCurl);
    if($response['attributes'][3]['name'] == "Адрес доставки"){
        $adress = $response['attributes'][3]['value'];
    }
    else{
        foreach($response['attributes'] as $attr){
            if($attr['name'] == "Адрес доставки"){
                $adress = $attr['value'];
                break;
            }
        }
    }
    if($response['attributes'][2]['name'] == "Телефон клиента"){
        $phone = $response['attributes'][2]['value'];
    }
    else{
        foreach($response['attributes'] as $attr){
            if($attr['name'] == "Телефон клиента"){
                $phone = $attr['value'];
                break;
            }
        }
    }
    $name = $response['name'];
    $ms2 = $modx->getService('minishop2');
    $sum = floatval($response['sum']) / 100;
    $sum = $ms2->formatPrice($sum);
    $text = "Ваш заказ №{$name} на сумму {$sum} \nготов к выдаче по адресу \n{$adress} шоу рум AUTENTIMENTS. \nЧасы работы ежедневно с 11.00 до 22.00. \nСрок хранения 5 дней.";
    if(empty($phone)){
        $myCurl = curl_init();
        curl_setopt_array($myCurl, array(
            CURLOPT_URL => $response['agent']['meta']['href'],
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_CUSTOMREQUEST => 'GET',
            CURLOPT_HTTPHEADER => array("Authorization: Bearer 36f61839f3f41d0c14fc10f493a44139529be7ea",
                "Content-Type: application/json"),
        ));
        $response = json_decode(curl_exec($myCurl),1);
        curl_close($myCurl);
        $phone = $response['phone'];
    }
    $stikSms = $modx->getService('stik', 'stikSms', $modx->getOption('core_path').'components/stik/model/', []);
    $phone = $stikSms->preparePhone($phone);
    if($phone){
        $modx->getService("sms", "sms", MODX_CORE_PATH . "components/sms/model/sms/");
        $modx->sms->initialize();
        $modx->sms->mode = "user";
        $modx->sms->sendSms(urlencode($text),$phone);
    }
}