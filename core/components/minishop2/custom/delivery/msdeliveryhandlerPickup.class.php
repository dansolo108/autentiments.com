<?php
if(!class_exists('msDeliveryInterface')) {
    require_once MODX_CORE_PATH. 'components/minishop2/handlers/msdeliveryhandler.class.php';
}

class msDeliveryHandlerPickup extends msDeliveryHandler {
    /**
     * @param xPDOObject $object
     * @param array $config
     */
    public function getCost(msOrderInterface $order, msDelivery $delivery, $cost = 0) {

        $orderData = $order->get();
        if($orderData["city"] != trim(str_replace("Самовывоз ","", $delivery->get("name")))){
            return null;
        }
        return $cost;
    }
}
?>