<?php

define('MODX_API_MODE', true);
require_once __DIR__ . '/../index.php';

/*if (php_sapi_name() == 'cli') {
    $input_data = [
        'auditContext' => [
            'meta' => [
                'type' => 'audit',
                'href' => 'https://online.moysklad.ru/api/remap/1.2/audit/7394a1ae-fb56-11ec-0a80-0ec400299fb9',
            ],
            'uid' => '@autentique',
            'moment' => '2022-07-04 08:02:06',
        ],
        'events' => [
            [
                'meta' => [
                    'type' => 'customerorder',
                    'href' => 'https://online.moysklad.ru/api/remap/1.2/entity/customerorder/f0c1717c-f089-11ec-0a80-0d6c0051520a'
                ],

                'action' => 'CREATE',
                'accountId' => '2320885e-7287-11ea-0a80-03830000ff91',
            ],
            [
                'meta' => [
                    'type' => 'customerorder',
                    'href' => 'https://online.moysklad.ru/api/remap/1.2/entity/customerorder/73885ad5-fb56-11ec-0a80-0ec400299faf',
                ],
                'action' => 'CREATE',
                'accountId' => '2320885e-7287-11ea-0a80-03830000ff91',
            ],

        ],
    ];
}*/


$moyskaldUrls = array_map(function ($row) {
    return $row['meta']['href'];
}, $input_data['events']);

$modxOrdersIds = [];
foreach ($moyskaldUrls as $url) {
    $order = getMoyskaladOrder($url);
    $order_id = $order['code'];
    $params = ['order_id' => $order_id];
    $order = $modx->getObject('mspcOrder', $params);

    if (empty($order)) {
        continue;
    }
    $promocode = $order->_fields['code'];
    if (!empty($promocode)) {
        updateMoyskaldOrderPromocode($url, $promocode);
    }
}

function getMoyskaladOrder($url) {
    $curl = curl_init();
    curl_setopt_array($curl, array(
        CURLOPT_URL => $url,
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_ENCODING => '',
        CURLOPT_MAXREDIRS => 10,
        CURLOPT_TIMEOUT => 0,
        CURLOPT_FOLLOWLOCATION => true,
        CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
        CURLOPT_CUSTOMREQUEST => 'GET',
        CURLOPT_POSTFIELDS =>'',
        CURLOPT_HTTPHEADER => array(
            'Content-Type: application/json',
            'Authorization: Basic ZHZAYXV0ZW50aXF1ZTpyb2lzdGF0MjAyMg=='
        ),
    ));
    $response = curl_exec($curl);
    curl_close($curl);
    return json_decode($response, 1);
}

function updateMoyskaldOrderPromocode($url, $promocode)
{
    $curl = curl_init();

    $params = [
        "attributes" => [
            [
                "meta" => [
                    "href" => "https://online.moysklad.ru/api/remap/1.2/entity/customerorder/metadata/attributes/0be531c9-1eab-11eb-0a80-01d0000af0f4",
                    "type" => "attributemetadata",
                    "mediaType" => "application/json"
                ],
                "id" => "0be531c9-1eab-11eb-0a80-01d0000af0f4",
                "name" => "Промокод",
                "type" => "string",
                "value" => $promocode
            ]
        ]
    ];
    $json = json_encode($params);

    curl_setopt_array($curl, array(
//        CURLOPT_URL => 'https://online.moysklad.ru/api/remap/1.2/entity/customerorder/73885ad5-fb56-11ec-0a80-0ec400299faf',
        CURLOPT_URL => $url,
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_ENCODING => '',
        CURLOPT_MAXREDIRS => 10,
        CURLOPT_TIMEOUT => 0,
        CURLOPT_FOLLOWLOCATION => true,
        CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
        CURLOPT_CUSTOMREQUEST => 'PUT',
        CURLOPT_POSTFIELDS =>$json,
        CURLOPT_HTTPHEADER => array(
            'Content-Type: application/json',
            'Authorization: Basic ZHZAYXV0ZW50aXF1ZTpyb2lzdGF0MjAyMg=='
        ),
    ));

    $response = curl_exec($curl);

    curl_close($curl);
    echo $response;
}