<?php

interface mspcConditionInterface
{
    public function initialize($ctx = 'web');

    public function followingConditions($id);
}

class mspcConditionHandler implements mspcConditionInterface
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
        // $this->mspc->setError('mspcConditionHandler', true); // отладка подключения

        return true;
    }

    /**
     * Метод.
     * Проверяет, соблюдаются ли все условия купона/акции.
     *
     * @param mixed $id ID купона/акции
     *
     * @return bool
     */
    public function followingConditions($id)
    {
        // Узнаем, с чем работаем: coupon или action
        $type = '';
        if (is_int($id)) {
            $type = 'coupon';
        } elseif (is_array($id)) {
            if (array_key_exists('coupon', $id)) {
                $type = 'coupon';
                $id = $id['coupon'];
            } elseif (array_key_exists('action', $id)) {
                $type = 'action';
                $id = $id['action'];
            }
        }

        // Получаем значения корзины
        $total_cost = $total_count = $total_units = 0;
        foreach ($this->mspc->cart as $item) {
            $total_cost += (float)$item['price'] * (float)$item['count'];
            $total_count += (float)$item['count'];
            $total_units += 1;
        }

        switch ($type) {
            case 'coupon':
                if ($this->mspc->coupon->getCouponByID($id)) {
                    $q = $this->modx->newQuery('mspcCondition', array('coupon_id' => $id));
                    $q->select($this->modx->getSelectColumns('mspcCondition', 'mspcCondition'));

                    if ($q->prepare() && $q->stmt->execute() && $rows = $q->stmt->fetchAll(PDO::FETCH_ASSOC)) {
                        foreach ($rows as $row) {
                            if (// сумма корзины (от)
                                ($row['type'] == 'from_total_cost' && $total_cost < $row['value']) ||
                                // сумма корзины (до)
                                ($row['type'] == 'to_total_cost' && $total_cost >= $row['value']) ||
                                // кол-во в корзине (от)
                                ($row['type'] == 'from_total_count' && $total_count < $row['value']) ||
                                // кол-во в корзине (до)
                                ($row['type'] == 'to_total_count' && $total_count >= $row['value'])
                            ) {
                                $this->mspc->setWarning($this->modx->lexicon('mspromocode_err_coupon_conditions_are_not_met'));

                                return false;
                            }
                        }
                    }
                    unset($q, $rows);
                } else {
                    $this->mspc->setError($this->modx->lexicon('mspromocode_err_code_invalid'), true);

                    return false;
                }

                // $action = $this->mspc->action->getActionByCouponID($id);
                break;

            default:
                return false;
                break;
        }

        return true;
    }
}