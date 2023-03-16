<?php
if(!class_exists('msDeliveryInterface')) {
    require_once MODX_CORE_PATH. 'components/minishop2/handlers/msdeliveryhandler.class.php';
}

class msDeliveryHandlerPochtaRus extends msDeliveryHandler implements msDeliveryInterface {
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
            'Authorization'=> "AccessToken {$this->config['authToken']}",
            'X-User-Authorization'=> "Basic {$this->config['authKey']}",
        ]);
    }
    
    public function getCost(msOrderInterface $order, msDelivery $delivery, $cost = 0) {
        if (!$this->config['authToken'] || !$this->config['authKey'] || !$this->config['fromIndex']) {
            return $cost;
        }
        $orderData = $order->get();
        $status = $this->ms2->cart->status();
        if (empty($orderData['index'])) return null;
        $response = $this->modRestClient->post('tariff', [
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
        ]);
        $data =  $response->process();

        if (empty($data['total-rate'])) {
            $this->modx->log(MODX_LOG_LEVEL_ERROR,'Почта россии ошибка:'.var_export(["response"=>$data,"postData"=>$postData,"modRest"=>$this->modRestClient->config],1));
            return null;
        }
        $min = 0;
        $max = 0;
        if (isset($data['delivery-time'])) {
            $min = $data['delivery-time']['min-days'];
            $max = $data['delivery-time']['max-days'];
        }
        // бесплатная доставка по РФ в зависимости от настройки
        if(in_array(mb_strtolower($orderData['country']), ['россия','russian federation'])){
            if ($status["total_cost"] > 20000)
                return [$cost,$min,$max];


            if($orderData['city'] === "Москва" || $orderData['city'] === "Санкт-Петербург")
                return [$cost + 500,$min,$max];

            if($orderData['region'] === 'Московская' || $orderData['region'] === 'Ленинградская')
                return [$cost + 690,$min,$max];

            return [$cost + 790, $min, $max];
        }
        $cost += round(number_format($data['total-rate'] / 100, 0, '.', '')) * 2;
        return [$cost, $min, $max];
    }
}
?>