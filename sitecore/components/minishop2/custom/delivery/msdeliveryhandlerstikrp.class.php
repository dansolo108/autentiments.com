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
        $orderData = [
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

        $response = $this->modRestClient->post('tariff', $orderData);
        $data =  $response->process();

        if (isset($data['total-rate'])) {
            $delivery_cost = round(number_format($data['total-rate'] / 100, 0, '.', '')); // переводим копейки в рубли и округляем
            $this->modx->log(1,var_export($order->get(),1));
            if($cost < 20000 || !in_array(mb_strtolower($orderData['country']), ['россия','russian federation']))
                $cost = $cost + $delivery_cost * 2;
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