<?php
require_once "PickupPointsInterface.php";
require_once "msdeliveryhandlercdek.class.php";
use CdekSDK\Requests;
use CdekSDK\CdekClient;
class msDeliveryHandlerCdekPickupPoint extends msDeliveryHandlerCdek implements PickupPointsInterface {
    public ?modRest $modRest;
    public function __construct(xPDOObject $object, $config = [])
    {
        parent::__construct($object, $config);
        $this->modRest = new modRest($this->modx);
        $this->modRest->setOption('baseUrl', 'https://api.cdek.ru/v2');
        $this->modRest->setOption('suppressSuffix', true);
        $this->modRest->setOption('format', 'x-www-form-urlencoded');
        $response = $this->modRest->post("/oauth/token?parameters",[
            "grant_type"=>"client_credentials",
            "client_secret"=>$this->modx->getOption("cdek_client_secret"),
            "client_id"=>$this->modx->getOption("cdek_client_id"),
        ])->process();
        if(empty($response["access_token"]))
            $this->modRest = null;
        else{
            $this->modRest->setOption('headers', [
                'Content-Type'=> 'application/json;charset=UTF-8',
                "Authorization"=>"Bearer {$response["access_token"]}"
            ]);
            $this->modRest->setOption('format', 'json');
        }
    }

    public function getPickupPoints(msOrderInterface $order) : array{
        $orderData = $order->get();
        if(empty($orderData["index"]) || empty($this->modRest))
            return [];
        $response = $this->modRest->get("deliverypoints",[
            "type"=>"PVZ",
            "postal_code"=>$orderData["index"],
        ])->process();
        if(empty($response) || !empty($response["errors"])|| !empty($response["error"]))
            return [];
        return $response;
    }
}
?>