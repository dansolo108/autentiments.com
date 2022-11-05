<?php
if(!class_exists('msDeliveryInterface')) {
    require_once MODX_CORE_PATH. 'components/minishop2/model/minishop2/msdeliveryhandler.class.php';
}

require_once MODX_CORE_PATH . 'components/stik_cdek/vendor/autoload.php';

use CdekSDK\Requests;
use CdekSDK\CdekClient;
use CdekSDK\Common\AdditionalService;

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
        $receiverCountry = $orderData['country'];
        $receiverCity = $orderData['city'];
        $receiverIndex = $orderData['index'];
        $receiverCityId = $orderData['cdek_id'];
        
        if (empty($receiverCity) || empty($receiverIndex)) return $cost;
        $client = new CdekClient($this->config['authLogin'], $this->config['authPassword']);
        
        $request = new Requests\CalculationAuthorizedRequest();
        $request->setSenderCityPostCode($this->config['fromIndex'])
            ->setReceiverCityPostCode($receiverIndex)
            ->addAdditionalService(AdditionalService::SERVICE_INSURANCE, $total['total_cost'])
            ->setTariffId($tariff_code)
            ->addPackage([
                'weight' => 2,
                'length' => 48,
                'width'  => 36,
                'height' => 12,
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
        $min = $response->getDeliveryPeriodMin();
        $max = $response->getDeliveryPeriodMax();
        $delivery_cost = 0;
        // бесплатная доставка по РФ в зависимости от настройки
        if (!($cost > 20000 && in_array(mb_strtolower($receiverCountry), ['россия','russian federation']))) {
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
        return [$cost, $min, $max];
    }
}
?>