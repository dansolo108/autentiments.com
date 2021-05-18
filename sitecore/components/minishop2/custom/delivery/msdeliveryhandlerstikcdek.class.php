<?php
if(!class_exists('msDeliveryInterface')) {
    require_once MODX_CORE_PATH. 'components/minishop2/model/minishop2/msdeliveryhandler.class.php';
}

require_once MODX_CORE_PATH . 'components/stik_cdek/vendor/autoload.php';

use CdekSDK\Requests;
use CdekSDK\CdekClient;

class msDeliveryHandlerStikCdek extends msDeliveryHandler implements msDeliveryInterface {
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
            'authLogin' => $this->modx->getOption('stik_cdek_auth_login'),
            'authPassword' => $this->modx->getOption('stik_cdek_auth_password'),
            'fromIndex' => $this->modx->getOption('stik_cdek_from_index'),
        ], $config);
    }
    
    public function getCost(msOrderInterface $order, msDelivery $delivery, $cost = 0) {
        if (!$this->config['authLogin'] || !$this->config['authPassword'] || !$this->config['fromIndex']) {
            return [$cost, 0, 0];
        }
        $modx = $this->modx;
        $orderData = $order->get('order');
        $tariff_code = $delivery->get('tariff') ?: 1;
        $total = $this->ms2->cart->status();
        $cart = $this->ms2->cart->get();
        $receiverCity = $orderData['city'];
        $receiverIndex = $orderData['index'];
        $receiverCityId = $orderData['cdek_id'];
        
        if (empty($receiverCity) || empty($receiverIndex)) return $cost;
        
        $client = new CdekClient($this->config['authLogin'], $this->config['authPassword']);
        
        $request = new Requests\CalculationAuthorizedRequest();
        $request->setSenderCityPostCode($this->config['fromIndex'])
            ->setReceiverCityPostCode($receiverIndex)
            ->setTariffId($tariff_code)
            ->addPackage([
                'weight' => 1,
                'length' => 32,
                'width'  => 21,
                'height' => 21,
            ]);
            
        if ($receiverCityId) {
        	$request->setReceiverCityId($receiverCityId);
        }
        
        $response = $client->sendCalculationRequest($request);
        
        if ($response->hasErrors()) {
            foreach ($response->getMessages() as $message) {
                if ($message->getErrorCode() !== '') {
                    $modx->log(modX::LOG_LEVEL_ERROR, 'CDEK ErrorCode: ' . $message->getErrorCode() . '<br> CDEK ErrorMessage: ' . $message->getMessage());
                }
            }
            return [$cost, 0, 0];
        }
        
        /** @var \CdekSDK\Responses\CalculationResponse $response */
        $delivery_cost = $response->getPrice();
        $cost = $cost + $delivery_cost;
        $min = $response->getDeliveryPeriodMin();
        $max = $response->getDeliveryPeriodMax();
        
        return [$cost, $min, $max];
    }
}
?>