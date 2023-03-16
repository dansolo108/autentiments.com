<?php

interface mspcCouponInterface
{
    public function initialize($ctx = 'web');

    public function getCurrentCoupon();

    public function setCurrentCoupon($code = '');

    public function removeCurrentCoupon();

    public function getCouponByCode($code, $cache = true);

    public function getCoupon(array $coupon = array(), $cache = true);
}

class mspcCouponHandler implements mspcCouponInterface
{
    /* @var modX $modx */
    public $modx;
    /* @var msPromoCode $mspc */
    public $mspc;
    /* @var array $current */
    public $current = array();

    /**
     * @param msPromoCode $mspc   [description]
     * @param array       $config [description]
     */
    public function __construct(msPromoCode &$mspc, array $config = array())
    {
        $this->mspc = &$mspc;
        $this->modx = &$mspc->modx;
    }

    /** @inheritdoc} */
    public function initialize($ctx = 'web')
    {
        if ($ctx != 'mgr') {
            // Получаем купон из сессии
            $this->getCurrentCoupon();
        }

        // $this->mspc->setError('mspcCouponHandler', true); // отладка подключения

        return true;
    }

    /**
     * Метод.
     * Получает применённый купон, если таковой есть, из сессии.
     * @return array Массив с купоном.
     */
    public function getCurrentCoupon()
    {
        $code = $_SESSION['mspc']['coupon'];
        if (!$this->current = $this->getCouponByCode($code)) {
            if (!empty($code)) {
                $this->mspc->setError($this->modx->lexicon('mspromocode_err_code_invalid'), true);
            }
        }

        return $this->current;
    }

    /**
     * Метод.
     * Применяет купон, проверяя, есть ли такой в базе и можно ли его применить.
     *
     * @param string $code Код купона
     *
     * @return bool|array Массив с купоном.
     */
    public function setCurrentCoupon($code = '')
    {
        $success = true;
        $coupon = $this->getCouponByCode($code);
        $this->current = $coupon;
        $_SESSION['mspc']['coupon'] = !empty($this->current) ? $this->current['code'] : '';

        if (!empty($coupon) && !empty($code)) {
            $this->mspc->invokeEvent('mspcOnSetCoupon', array(
                'mspc' => $this->mspc,
                'coupon' => $coupon,
            ));
        }

        return $this->current;
    }

    /**
     * Метод.
     * Удаляет применённый купон.
     */
    public function removeCurrentCoupon()
    {
        $this->setCurrentCoupon();
        $this->mspc->setSuccess($this->modx->lexicon('mspromocode_ok_code_remove'));

        return true;
    }

    /**
     * Метод.
     * Получает данные купона по коду.
     *
     * @param      $code
     * @param bool $cache
     *
     * @return array
     */
    public function getCouponByCode($code, $cache = true)
    {
        if ($cache && isset($this->mspc->cache['coupons']['code'][$code]['get'])) {
            return $this->mspc->cache['coupons']['code'][$code]['get'];
        }

        if (!empty($code)) {
            $response = $this->mspc->invokeEvent('mspcOnBeforeGetCouponByCode', array(
                'mspc' => $this->mspc,
                'code' => $code,
            ));
            if ($response['success']) {
                $code = $response['data']['code'];
            }
            // $this->modx->log(1, 'getCouponByCode mspcOnBeforeGetCouponByCode ' . print_r($response, 1));
        }

        return $this->getCoupon(array('code' => $code), $cache);
    }

    /**
     * Метод.
     * Получает данные купона.
     *
     * @param array $coupon
     * @param bool  $cache
     *
     * @return array
     */
    public function getCoupon(array $coupon = array(), $cache = true)
    {
        $row = array();
        $code = !empty($coupon['code']) ? $coupon['code'] : false;
        if (empty($code)) {
            return $row;
        }

        if ($cache && isset($this->mspc->cache['coupons']['code'][$code]['get'])) {
            return $this->mspc->cache['coupons']['code'][$code]['get'];
        }
        $result = $this->mspc->maxma->calculatePurchase($code);
        if($result['calculationResult']['promocode']['applied']){
            $row['code'] = $code;
        }
        else{
            $this->mspc->setError($result['calculationResult']['promocode']['error']['description']);
        }
        // кешируем купон
        if ($cache) {
            $this->mspc->cache['coupons']['code'][$code]['get'] = $row;
        }


        return $row;
    }
}
