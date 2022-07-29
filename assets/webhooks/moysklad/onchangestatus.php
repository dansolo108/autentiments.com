<?php
define('MODX_API_MODE', true);
require_once dirname(__DIR__, 3) . '/index.php';
/** @var $modx gitModx */
$moySklad = $modx->getService('moysklad', 'moysklad', $modx->getOption('core_path').'components/stik/model/', []);

$input = json_decode(file_get_contents('php://input'),1);

$response = $moySklad->get($input['auditContext']['meta']['href'].'/events');
if($response['rows'][0]['diff']['state']['newValue']['name'] == 'На выдаче'){

    $response = $moySklad->get($response['rows'][0]['entity']['meta']['href']);
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
    $text = "Ваш заказ №{$name} на сумму {$sum} \nготов к выдаче по адресу \n{$adress} шоу рум AUTENTIMENTS. \nЧасы работы ежедневно с 11.00 до 22.00.";
    if(empty($phone)){
        $response = $moySklad->get($response['agent']['meta']['href']);
        $phone = $response['phone'];
    }
    $stikSms = $modx->getService('stik', 'stikSms', $modx->getOption('core_path').'components/stik/model/', []);
    $phone = $stikSms->preparePhone($phone);
    if($phone){

        /* @var $sms sms*/
        $sms = $modx->getService("sms", "sms", MODX_CORE_PATH . "components/sms/model/sms/");
        $sms->initialize();

        $sms->sendSms(urlencode($text),$phone);
    }
}