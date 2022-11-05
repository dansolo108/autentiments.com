<?php
if(!class_exists('msDeliveryInterface')) {
    require_once MODX_CORE_PATH. 'components/minishop2/model/minishop2/msdeliveryhandler.class.php';
}

class msDeliveryHandlerStikRp extends msDeliveryHandler implements msDeliveryInterface {
    /** @var modX $modx */
    public $modx;
    /** @var miniShop2 $ms2 */
    public $ms2;
    /** @var modRest */
    public $modRestClient;

    /**
     * @param xPDOObject $object
     * @param array $config
     */
    function __construct(xPDOObject $object, $config = [])
    {
        $this->modx = $object->xpdo;
        $this->ms2 = $object->xpdo->getService('miniShop2');
        
        $this->config = array_merge([
            'authToken' => $this->modx->getOption('stik_ems_token'),
            'authKey' => $this->modx->getOption('stik_ems_key'),
            'fromIndex' => $this->modx->getOption('stik_ems_from_index'),
        ], $config);
        $this->modRestClient = $this->modx->getService('rest', 'rest.modRest');
        $this->modRestClient->setOption('baseUrl', 'https://otpravka-api.pochta.ru/1.0');
        $this->modRestClient->setOption('format', 'json');
        $this->modRestClient->setOption('suppressSuffix', true);
        $this->modRestClient->setOption('headers', [
            'Content-Type'=> 'application/json;charset=UTF-8',
            'Authorization'=> 'AccessToken ' . $this->config['authToken'],
            'X-User-Authorization'=> 'Basic ' . $this->config['authKey'],
        ]);
    }
    
    public function getCost(msOrderInterface $order, msDelivery $delivery, $cost = 0) {
        if (!$this->config['authToken'] || !$this->config['authKey'] || !$this->config['fromIndex']) {
            return [$cost, 0, 0];
        }
        $orderData = $order->get();
        if (empty($orderData['index'])) return [$cost, 0, 0];
        $postData = [
            "index-from" => (string)$this->config['fromIndex'],
            "index-to" => (string)$orderData['index'],
            "mail-category" => "ORDINARY",
            "mail-type" => (string)$delivery->get('tariff'),
            "sms-notice-recipient"=>1,
            "mass" => 1000,
            "dimension" => [
                "height" => 32,
                "length" => 21,
                "width" => 21
            ],
            "fragile" => false,
        ];

        $response = $this->modRestClient->post('tariff', $postData);
        $data =  $response->process();

        if (isset($data['total-rate'])) {
            $delivery_cost = 0;
            // бесплатная доставка по РФ в зависимости от цены
            if (!($cost > 20000 && in_array(mb_strtolower($orderData['country']), ['россия','russian federation']))) {
                /* @var modRest $modRestClient */
                $modRestClient = $this->modx->getService('rest', 'rest.modRest');
                $modRestClient->setOption('baseUrl', 'api.delivery.yandex.ru');
                $modRestClient->setOption('format', 'json');
                $modRestClient->setOption('suppressSuffix', true);
                $modRestClient->setOption('headers', [
                    'Accept' => 'application/json',
                    'Content-type' => 'application/json',
                    'Authorization' => 'OAuth y0_AgAAAAAw6kw-AAiOCQAAAADTGrE5FwNJXaCJTbuweF6C-qQreOjVzUg'
                ]);
                $response = $modRestClient->get('location', ['term' => $orderData['city']]);
                $response = $response->process();
                $addresses = [];
                //normalize addresses
                foreach($response[0]['addressComponents'] as $item){
                    $addresses[$item['kind']][] = $item['name'];
                }
                $prices = [
                    'LOCALITY'=>[
                        'Москва' => 500,
                        'Санкт-Петербург' => 500,
                    ],
                    'PROVINCE'=>[
                        'Московская область'=>690,
                        'Ленинградская область'=>690,
                        'Санкт-Петербург'=>690,
                    ],
                ];
                foreach ($prices as $keyKind => $itemKind){
                    if($addresses[$keyKind]){
                        foreach($addresses[$keyKind] as $name){
                            if($itemKind[$name]){
                                $delivery_cost = $itemKind[$name];
                                break(2);
                            }
                        }
                    }
                }
                if($delivery_cost == 0){
                    $delivery_cost = 790;
                }
            }
            $cost += $delivery_cost;
            // увеличиваем стоимость доставки вдвое.
            $min = $max = 0;
            if (isset($data['delivery-time'])) {
                $min = $data['delivery-time']['min-days'];
                $max = $data['delivery-time']['max-days'];
            }
            return [$cost, $min, $max];
        }
        $this->modx->log(MODX_LOG_LEVEL_ERROR,'Почта россии ошибка:'.var_export($data,1));
        return [$cost, 0, 0];
    }
}
?>