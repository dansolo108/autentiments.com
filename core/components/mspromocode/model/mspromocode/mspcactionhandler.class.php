<?php

interface mspcActionInterface
{
    public function initialize($ctx = 'web');

    public function getActionByCouponID($id, $cache = true);

    public function getAction($action, $cache = true);
}

class mspcActionHandler implements mspcActionInterface
{
    /* @var modX $modx */
    public $modx;
    /* @var msPromoCode $mspc */
    public $mspc;
    /* @var array $current */
    // public $current = array();

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
        // $this->mspc->setError('mspcActionHandler', true); // отладка подключения

        return true;
    }

    /**
     * Метод.
     * Получает данные акции по id купона.
     *
     * @param      $id
     * @param bool $cache
     *
     * @return array|mixed
     */
    public function getActionByCouponID($id, $cache = true)
    {
        if ($cache && isset($this->mspc->cache['actions']['coupon'][$id]['get'])) {
            return $this->mspc->cache['actions']['coupon'][$id]['get'];
        }

        return $this->getAction(array('coupon_id' => $id), $cache);
    }

    /**
     * Метод.
     * Получает данные акции.
     *
     * @param      $action
     * @param bool $cache
     *
     * @return array|mixed
     */
    public function getAction($action, $cache = true)
    {
        $row = array();
        $id = !empty($action['id']) ? $action['id'] : false;
        $coupon_id = !empty($action['coupon_id']) ? $action['coupon_id'] : false;
        if (empty($id) && empty($coupon_id)) {
            return $row;
        }

        if ($coupon_id) {
            $coupon = $this->mspc->coupon->getCouponByID($coupon_id, $cache);
            if ($id = $coupon['action_id'] ?: 0) {
                return $row;
            }
        }

        if ($cache && isset($this->mspc->cache['actions']['id'][$id]['get'])) {
            return $this->mspc->cache['actions']['id'][$id]['get'];
        }

        $q = $this->modx->newQuery('mspcAction', array(
            'id' => $id,
            'active' => 1,
        ));
        $q->orCondition(array(
            // 'begins:IN' => array('0000-00-00 00:00:00', '', null),
            '`mspcAction`.`begins` IS NULL',
            '`mspcAction`.`begins` IN ("0000-00-00 00:00:00", "")',
            '`mspcAction`.`begins` <= NOW()',
        ), '', 1);
        $q->orCondition(array(
            // 'ends:IN' => array('0000-00-00 00:00:00', '', null),
            '`mspcAction`.`ends` IS NULL',
            '`mspcAction`.`ends` IN ("0000-00-00 00:00:00", "")',
            '`mspcAction`.`ends` >= NOW()',
        ), '', 2);

        $q->select($this->modx->getSelectColumns('mspcAction', 'mspcAction'));
        // $q->prepare(); $this->modx->log(1, print_r($q->toSQL(),1));

        if ($cache) {
            $this->mspc->cache['actions']['id'][$id]['get'] = array();
        }

        if ($q->prepare() && $q->stmt->execute() && $row = $q->stmt->fetch(PDO::FETCH_ASSOC)) {
            // кешируем
            if ($cache) {
                $this->mspc->cache['actions']['id'][$id]['get'] = $row;
            }
        }
        unset($q);

        return $row;
    }
}
