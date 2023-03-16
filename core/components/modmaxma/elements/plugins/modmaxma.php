<?php
/** @var $modx modX */
/** @var modNamespace $ns */
$ns = $modx->getObject("modNamespace", "modmaxma");
/** @var modMaxma $maxma */
$maxma = $modx->getService("modmaxma", "modMaxma", $ns->getCorePath() . "/model/");
switch ($modx->event->name) {
    case "msOnGetOrderCost":
        $maxma->ms2 = $order->ms2;
        /** @var array $result */
        $result["promocode_discount"] = "";
        $result["bonuses_discount"] = "";
        $response = $maxma->calculateCurrentOrder();
        if ($response && empty($response["bonuses"]["error"]) && empty($response["promocode"]["error"])) {
            $result["promocode_discount"] = -$response["calculationResult"]["summary"]["discounts"]["promocode"] ?: "";
            $result["bonuses_discount"] = -$response["calculationResult"]["summary"]["discounts"]["bonuses"] ?: "";
            $result["cost"] -= $response["calculationResult"]["summary"]["totalDiscount"];
        }
        $modx->event->returnedValues["result"] = $result;
        break;
    //при любом изменении корзины вычисляем новое кол-во бонусов.
    case "msOnChangeInCart":
    case "msOnAddToCart":
    case "msOnRemoveFromCart":
        $maxma->ms2 = $cart->ms2;
        if($cart->ms2->order->get()["bonuses"])
            $cart->ms2->order->add("bonuses",1);
        break;
}