<?php

if (!class_exists('msDeliveryInterface')) {
    require_once MODX_CORE_PATH . 'components/minishop2/model/minishop2/msdeliveryhandler.class.php';
}

class uaPostDeliveryHandler extends msDeliveryHandler implements msDeliveryInterface
{
    /** @var modX $modx */
    public $modx;
    /** @var miniShop2 $ms2 */
    public $ms2;
    /** @var array $order */
    public $order;
    /** @var array $delivery */
    public $delivery;
    /** @var array $cart */
    public $cart;
    /** @var array $status */
    public $status;


    /**
     * @param xPDOObject $object
     * @param array $config
     */
    function __construct(xPDOObject $object, $config = array())
    {
        parent::__construct($object, $config);
    }


    /**
     * @param msOrderInterface $order
     * @param msDelivery $delivery
     * @param int $cost
     *
     * @return float|int|mixed|null
     */
    public function getCost(msOrderInterface $order, msDelivery $delivery, $cost = 0)
    {
        $status = $this->ms2->cart->status();
        if (empty($cost)) {
            $cost = $this->modx->getOption('total_cost', $status, 0, true);
        }
        if ($this->hasMultiCurrency()) {
            /** @var MsMC $msmc */
            $msmc = $this->modx->getService('msmulticurrency', 'MsMC');
            $baseCost = $msmc->convertPriceToBaseCurrency($cost);
            $deliveryBaseCost = parent::getCost($order, $delivery, $baseCost);
            $deliveryBaseCost = $deliveryBaseCost - $baseCost;
            $deliveryCost = $msmc->getPrice($deliveryBaseCost, 0, 0, 0, false);
            $cost += $deliveryCost;
        } else {
            $cost = parent::getCost($order, $delivery, $cost);
        }
        return $cost;
    }

    /**
     * @return bool
     */
    public function hasMultiCurrency()
    {
        $cartUserCurrency = $this->modx->getOption('msmulticurrency.cart_user_currency', null, 0, true);
        return $cartUserCurrency && $this->isExistService('msmulticurrency');
    }

    /**
     * @param string $service
     * @return bool
     */
    public function isExistService($service = '')
    {
        $service = strtolower($service);
        return file_exists(MODX_CORE_PATH . 'components/' . $service . '/model/' . $service . '/');
    }
}