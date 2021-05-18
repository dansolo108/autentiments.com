<?php

require_once MODX_CORE_PATH . 'model/modx/modprocessor.class.php';
require_once MODX_CORE_PATH . 'components/minishop2/processors/mgr/orders/getlist.class.php';

class mspcMsOrderGetListProcessor extends msOrderGetListProcessor
{
    /** @var msPromoCode $mspc */
    protected $mspc;

    /**
     * @return bool|null|string
     */
    public function initialize()
    {
        $tmp = parent::initialize();
        $this->mspc = $this->modx->getService('mspromocode', 'msPromoCode', MODX_CORE_PATH . 'components/mspromocode/model/mspromocode/');

        return $tmp;
    }

    public function prepareQueryBeforeCount(xPDOQuery $q)
    {
        $q = parent::prepareQueryBeforeCount($q);

        if ($promocode = $this->getProperty('promocode')) {
            $q->leftJoin('mspcOrder', 'mspcOrder', "mspcOrder.order_id = {$this->classKey}.id");
            $q->where(array(
                'mspcOrder.code' => $promocode,
            ));
        }

        return $q;
    }
}

return 'mspcMsOrderGetListProcessor';