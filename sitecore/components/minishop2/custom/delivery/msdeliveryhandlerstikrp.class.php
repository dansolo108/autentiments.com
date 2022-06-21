<?php
if(!class_exists('msDeliveryInterface')) {
    require_once MODX_CORE_PATH. 'components/minishop2/model/minishop2/msdeliveryhandler.class.php';
}

class msDeliveryHandlerStikRp extends msDeliveryHandler implements msDeliveryInterface {
    /** @var modX $modx */
    public $modx;
    /** @var miniShop2 $ms2 */
    public $ms2;


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
    }
    
    public function getCost(msOrderInterface $order, msDelivery $delivery, $cost = 0) {
        if (!$this->config['authToken'] || !$this->config['authKey'] || !$this->config['fromIndex']) {
            return [$cost, 0, 0];
        }
        $modx = $this->modx;
        $orderData = $order->get('order');
        // $tariff_code = $delivery->get('tariff') ?: 1;
        // $total = $this->ms2->cart->status();
        // $cart = $this->ms2->cart->get();
        // $receiverCity = $orderData['city'];
        $receiverIndex = $orderData['index'];
        
        if (empty($receiverIndex)) return [$cost, 0, 0];
        
        $orderData = [
            "index-from" => (string)$this->config['fromIndex'],
            "index-to" => (string)$receiverIndex,
            "mail-category" => "ORDINARY",
            "mail-type" => (string)$delivery->get('tariff'),
            "mass" => 1000,
            "dimension" => [
                "height" => 32,
                "length" => 21,
                "width" => 21
            ],
            "fragile" => false,
        ];
        $this->modx->log(1,print_r($orderData,1));
        $url = 'https://otpravka-api.pochta.ru/1.0/tariff';   
        $method = 'POST';
        $headers = [
            'Content-Type: application/json;charset=UTF-8',
            'Authorization: AccessToken ' . $this->config['authToken'],
            'X-User-Authorization: Basic ' . $this->config['authKey'],
        ];
        $data = json_encode($orderData);
        
        $curl = curl_init();
        curl_setopt($curl, CURLOPT_URL, $url);
        curl_setopt($curl, CURLOPT_POST, 1);
        curl_setopt($curl, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($curl, CURLOPT_POSTFIELDS, $data);
        //verify https
        curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, false);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
        
        $response =  curl_exec($curl);
        $response = json_decode($response, 1);
        $httpCode = curl_getinfo($curl, CURLINFO_HTTP_CODE);
        curl_close($curl);
        $success = in_array($httpCode, [200, 201, 204]) ? true : false;
        // $this->modx->log(1, print_r($response,1));
        if ($success && isset($response['total-rate'])) {
            $delivery_cost = round(number_format($response['total-rate'] / 100, 0, '.', '')); // переводим копейки в рубли и округляем
            
//            // бесплатная доставка по РФ в зависимости от настройки
//            if ($delivery->get('free_delivery_rf') == 1 && in_array(mb_strtolower($receiverCountry), ['россия','russian federation'])) {
//                //
//            } else {
//                $cost = $cost + $delivery_cost;
//                // увеличиваем стоимость доставки на 150р.
//                $cost += 150;
//            }
            $cost = $cost + $delivery_cost;
            $min = $max = 0;
            if (isset($response['delivery-time'])) {
                $min = $response['delivery-time']['min-days'];
                $max = $response['delivery-time']['max-days'];
            }
            return [$cost, $min, $max];
        } else {
            $modx->log(1, 'EMS error. CURLINFO_HTTP_CODE: ' . $httpCode . ' response: ' . print_r($response, 1));
        }
        return [$cost, 0, 0];
    }
}
?>