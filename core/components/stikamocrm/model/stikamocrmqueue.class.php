<?php
class stikAmoCRMQueue
{
    /** @var modX $modx */
    public $modx;
    public $amo;
    protected $qm;
    // protected $config;


    /**
     * @param modMindbox $modmindbox
     */
    public function __construct(stikAmoCRM $amo)
    {
        $this->amo = $amo;
        $this->modx = $amo->modx;
        
        $registry = $this->modx->getService('registry', 'registry.modRegistry');
        $this->qm = $registry->getRegister('stikamocrm', 'registry.modDbRegister');
        $this->qm->subscribe("/order/");
    }
    
    public function push(int $order, $action)
    {
        if ($order > 0 && $action) {
            $this->qm->send("/order/", [['order' => $order, 'action' => $action]]);
        }
    }
    
    public function execute()
    {
        $this->qm->subscriptions = ["/order/"];
        
        $messages = $this->qm->read([
            'poll_limit' => 1,
            'msg_limit' => 10,
            'remove_read' => true,
        ]);
        if (empty($messages)) {
            return;
        }
        
        foreach($messages as $key => $message) {
            if (!$message['order']) {
                $this->modx->log(MODX_LOG_LEVEL_ERROR, 'Не передан id заказа! ' . print_r($message, 1));
                continue;
            }
            $msOrder = $this->modx->getObject('msOrder', $message['order']);
            if (!$msOrder) {
                $this->modx->log(MODX_LOG_LEVEL_ERROR, 'Не найден заказ с таким id! ' . print_r($message, 1));
                continue;
            }
            switch ($message['action']) {
                case 'order_create':
                    $lead_id = $this->amo->createOrder($msOrder);
                    if ($lead_id) {
                        $this->amo->addProductsToLead($lead_id, $message['order']);
                        unset($messages[$key]);
                    }
                    break;
                case 'order_change_status':
                    if ($this->amo->changeOrderStatus($msOrder)) {
                        unset($messages[$key]);
                    }
                    break;
            }
        }
        if (!empty($messages)) {
            $this->modx->log(MODX_LOG_LEVEL_ERROR, 'Не все запросы были отправлены! ' . print_r($messages, 1));
        }
    }
}