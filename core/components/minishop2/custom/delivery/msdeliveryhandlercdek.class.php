<?php
if(!class_exists('msDeliveryInterface')) {
    require_once MODX_CORE_PATH. 'components/minishop2/handlers/msdeliveryhandler.class.php';
}

require_once MODX_CORE_PATH . 'components/stik_cdek/vendor/autoload.php';

use CdekSDK\Requests;
use CdekSDK\CdekClient;
use CdekSDK\Common\AdditionalService;

class msDeliveryHandlerCdek extends msDeliveryHandler implements msDeliveryInterface {
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
        $orderData = $order->get();
        $status = $this->ms2->cart->status();
        if (empty($orderData["index"])) return null;
        $client = new CdekClient($this->config['authLogin'], $this->config['authPassword']);
        $request = new Requests\CalculationAuthorizedRequest();
        $request->setSenderCityPostCode($this->config['fromIndex'])
            ->setReceiverCityPostCode($orderData["index"])
            ->addAdditionalService(AdditionalService::SERVICE_INSURANCE, $status['total_cost'])
            ->setTariffId($delivery->get("tariff"))
            ->addPackage([
                'weight' => 2,
                'length' => 48,
                'width'  => 36,
                'height' => 12,
            ]);
        $response = $client->sendCalculationRequest($request);
        if ($response->hasErrors()) {
            foreach ($response->getMessages() as $message) {
                if ($message->getErrorCode() !== '') {
                    $modx->log(modX::LOG_LEVEL_ERROR, 'CDEK ErrorCode: ' . $message->getErrorCode() . '<br> CDEK ErrorMessage: ' . $message->getMessage());
                }
            }

            return null;
        }
        $min = $response->getDeliveryPeriodMin();
        $max = $response->getDeliveryPeriodMax();
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

        return [$cost + $response->getPrice(), $min, $max];
    }
}
?>