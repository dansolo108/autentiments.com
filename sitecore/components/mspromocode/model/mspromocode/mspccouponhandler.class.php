<?php

interface mspcCouponInterface
{
    public function initialize($ctx = 'web');

    public function getCurrentCoupon();

    public function setCurrentCoupon($code = '');

    public function removeCurrentCoupon();

    public function getCouponByID($id, $cache = true);

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

        // Если код купона передан - это применение
        if (!empty($code)) {
            if (empty($coupon)) {
                $this->mspc->setError($this->modx->lexicon('mspromocode_err_code_invalid'), true);
            } elseif (!empty($_SESSION['mspc']['coupon'])) {
                $this->mspc->setError($this->modx->lexicon('mspromocode_err_coupon_applied_before', array('coupon' => $_SESSION['mspc']['coupon'])),
                    true);

                return false;
            }

            if (!empty($coupon)) {
                $response = $this->mspc->invokeEvent('mspcOnBeforeSetCoupon', array(
                    'mspc' => $this->mspc,
                    'coupon' => $coupon,
                ));
                if (!$response['success']) {
                    $success = false;
                    $this->mspc->setError($response['message'], true);
                }
            }
        }

        if ($success) {
            $this->current = $coupon;
            $_SESSION['mspc']['coupon'] = !empty($this->current) ? $this->current['code'] : '';

            if (!empty($coupon) && !empty($code)) {
                $this->mspc->invokeEvent('mspcOnSetCoupon', array(
                    'mspc' => $this->mspc,
                    'coupon' => $coupon,
                ));
            }
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
     * Получает данные купона по id.
     *
     * @param      $id
     * @param bool $cache
     *
     * @return array
     */
    public function getCouponByID($id, $cache = true)
    {
        if ($cache && isset($this->mspc->cache['coupons']['id'][$id]['get'])) {
            return $this->mspc->cache['coupons']['id'][$id]['get'];
        }

        return $this->getCoupon(array('id' => $id), $cache);
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
        $id = !empty($coupon['id']) ? $coupon['id'] : false;
        $code = !empty($coupon['code']) ? $coupon['code'] : false;
        if (empty($id) && empty($code)) {
            return $row;
        }

        if ($cache && isset($this->mspc->cache['coupons'][($id ? 'id' : 'code')][($id ?: $code)]['get'])) {
            return $this->mspc->cache['coupons'][($id ? 'id' : 'code')][($id ?: $code)]['get'];
        }

        $where_id = !empty($id) ? array('id' => $id) : array();

        $where_code = !empty($code) ? array('code' => $code) : array();

        $q = $this->modx->newQuery('mspcCoupon', array_merge($where_id, $where_code, array('active' => 1)));
        $q->orCondition(array(
            // 'begins:IN' => array('0000-00-00 00:00:00', '', null),
            '`mspcCoupon`.`begins` IS NULL',
            '`mspcCoupon`.`begins` IN ("0000-00-00 00:00:00", "")',
            '`mspcCoupon`.`begins` <= NOW()',
        ), '', 1);
        $q->orCondition(array(
            // 'ends:IN' => array('0000-00-00 00:00:00', '', null),
            '`mspcCoupon`.`ends` IS NULL',
            '`mspcCoupon`.`ends` IN ("0000-00-00 00:00:00", "")',
            '`mspcCoupon`.`ends` >= NOW()',
        ), '', 2);
        $q->orCondition(array(
            'count:=' => '',
            'count:>' => 0,
        ), '', 3);

        $q->select($this->modx->getSelectColumns('mspcCoupon', 'mspcCoupon'));
        $q->prepare();
        // $this->modx->log(1, print_r($q->toSQL(), 1));

        if ($cache) {
            $this->mspc->cache['coupons']
            [($id ? 'id' : 'code')]
            [($id ?: $code)]
            ['get'] = array();
        }

        if ($c = $this->modx->getObject('mspcCoupon', $q)) {
            $row = $c->toArray();
            $id = $row['id'];
            $code = $row['code'];

            // если купон привязан к акции
            if ($row['action_id']) {
                if (!$action = $this->mspc->action->getAction(array('id' => $row['action_id']))) {
                    return array();
                }

                // кешируем акцию
                if ($cache && !empty($action)) {
                    $this->mspc->cache['actions']['id'][$action['id']]['get'] = $this->mspc->cache['actions']['coupon'][$id]['get'] = $action;
                }
            }

            // кешируем купон
            if ($cache) {
                $this->mspc->cache['coupons']['id'][$id]['get'] = $this->mspc->cache['coupons']['code'][$code]['get'] = $row;
            }
        }
        unset($q, $c);

        return $row;
    }
}
