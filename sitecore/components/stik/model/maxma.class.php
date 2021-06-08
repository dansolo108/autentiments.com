<?php
/**
 * The base class for maxma.
 */

class maxma {

    /* @var modX $modx */
    public $modx;
    public $namespace = 'stik';
    public $config = array();

    /**
     * @param modX $modx
     * @param array $config
     */
    function __construct (modX &$modx, array $config = array()) {
        $this->modx =& $modx;
        $this->namespace = $this->modx->getOption('namespace', $config, 'stik');
        $corePath = $this->modx->getOption('core_path') . 'components/stik/';
        $assetsPath = $this->modx->getOption('assets_path') . 'components/stik/';
        $assetsUrl = $this->modx->getOption('assets_url') . 'components/stik/';
        $this->config = array_merge(array(
            'corePath' => $corePath,
            'assetsPath' => $assetsPath,
            'modelPath' => $corePath . 'model/',
            'processorsPath' => $corePath . 'processors/',
            
            'assetsUrl' => $assetsUrl,
            'actionUrl' => $assetsUrl . 'action.php',
            'connectorUrl' => $assetsUrl . 'connector.php',
            'apiKey' => $this->modx->getOption('stik_maxma_api_key'),
            'serverAddress' => $this->modx->getOption('stik_maxma_url'), // api-test.cloudloyalty.ru
            'shopCode' => $this->modx->getOption('stik_maxma_shop_code'),
            'shopName' => $this->modx->getOption('stik_maxma_shop_name'),
        ), $config);

        $this->modx->addPackage('stik', $this->config['modelPath']);
        
        /* @var modRest $this->modRestClient */
        $this->modRestClient = $this->modx->getService('rest', 'rest.modRest');
        $this->modRestClient->setOption('baseUrl', rtrim($this->config['serverAddress'], '/'));
        $this->modRestClient->setOption('format', 'json');
        $this->modRestClient->setOption('suppressSuffix', true);
        $this->modRestClient->setOption('headers', [
            'Accept' => 'application/json',
            'Content-type' => 'application/json', // Сообщаем сервису что хотим получить ответ в json формате
            'X-Processing-Key' => $this->config['apiKey']
        ]);
        
        $this->userphone = '';
        if ($user = $this->modx->getUser()) {
            if ($profile = $user->getOne('Profile')) {
                if ($profile->get('mobilephone') && $profile->get('join_loyalty')) {
                    $this->userphone = preg_replace('/[^0-9+]/', '', $profile->get('mobilephone'));
                }
            }
        }
    }

    // Создание нового клиента
    public function createNewClient(array $data) {
        $params = [
            'client' => $data
            // [
            //     'phoneNumber' => '+79514877500',
            //     'email' => 'ig0r74@yandex.com',
            //     'surname' => 'Терентьев',
            //     'name' => 'Игорь',
            //     'externalId' => '5',
            // ]
        ];
        
        $response = $this->modRestClient->post('new-client', $params);
        $data = $response->process();
        
        if (isset($data['errorCode'])) {
            $this->modx->log(1, 'Maxma createNewClient error: ' . print_r($data, 1));
            return false;
        } else {
            return $data;
        }
    }

    // Получает общую информацию о клиенте
    public function getClientInfo(string $input, string $type = 'phoneNumber') {
        if (!in_array($type, ['phoneNumber', 'externalId', 'card']) || !$input) return false;
        if ($type == 'phoneNumber') $input = preg_replace('/[^0-9+]/', '', $input);
        
        $params = [];
        $params[$type] = $input;
        
        $response = $this->modRestClient->post('get-balance', $params);
        $data = $response->process();
        
        if (isset($data['errorCode'])) {
            $this->modx->log(1, 'Maxma getClientInfo error: ' . print_r($data, 1));
            return false;
        } else {
            return $data;
        }
    }
    
    // Получает баланс бонусов по номеру телефона
    public function getClientBalanceByPhone($phoneNumber) {
        $data = $this->getClientInfo($phoneNumber, 'phoneNumber');
        return $data['client']['bonuses'] ?: 0;
    }
    
    // Получает баланс бонусов по id пользователя в MODX
    public function getClientBalanceByExternalId($externalId) {
        $data = $this->getClientInfo($externalId, 'externalId');
        return $data['client']['bonuses'] ?: 0;
    }
    
    // Обновляет информацию о клиенте. Доступные параметры https://docs.maxma.com/api/#tag/Rabota-s-klientskoj-bazoj/paths/~1update-client/post
    public function updateClient(array $data) {
        $params['client'] = $data;
        $params['phoneNumber'] = $this->userphone;
        
        $response = $this->modRestClient->post('update-client', $params);
        $data = $response->process();
        
        if (isset($data['errorCode'])) {
            $this->modx->log(1, 'Maxma updateClient error: ' . print_r($data, 1));
            return false;
        } else {
            return $data;
        }
    }
    
    // Обновляет баланс клиента
    public function adjustClientBalance(int $amount, $phone = '') {
        if (!$phone) $phone = $this->userphone;
        if (!$phone) return false;
        
        $params = [];
        $params['client']['phoneNumber'] = $phone;
        $params['balanceAdjustment']['amountDelta'] = $amount;
        
        $response = $this->modRestClient->post('adjust-balance', $params);
        $data = $response->process();
        
        if (isset($data['errorCode'])) {
            $this->modx->log(1, 'Maxma adjustClientBalance error: ' . print_r($data, 1));
            return false;
        } else {
            return $data;
        }
    }

    // Проверка хватает-ли баланса
    public function checkBonuses($bonuses) {
        if (!$this->userphone) return $this->modx->lexicon('stik_loyalty_err_not_confirmed');
        
        $balance = $this->getClientBalanceByPhone($this->userphone);
        
        if ($bonuses <= $balance) return true;
        
        return $this->modx->lexicon('stik_loyalty_err_not_enough_bonuses');
    }
    
    // Создание/изменение заказа
    public function setOrder(string $order_id, int $bonuses, string $action) {
        if (!in_array($action, ['collect', 'apply']) || !$this->userphone) return false;
        $order = $this->modx->getObject('msOrder', $order_id);
        if (!$order) return false;
        
        $params = [];
        $params['client']['phoneNumber'] = $this->userphone;
        $params['order'] = [
            'id' => $order_id,
            'shopCode' => $this->config['shopCode'],
            'shopName' => $this->config['shopName'],
            'totalAmount' => $order->get('cost'),
            'loyalty' => [
                'action' => $action,
                'collectBonuses' => 0,
                'applyBonuses' => 0,
            ],
        ];
        
        $params['order']['loyalty'][$action.'Bonuses'] = $bonuses;
        
        $response = $this->modRestClient->post('set-order', $params);
        $data = $response->process();
        
        if (isset($data['errorCode'])) {
            $this->modx->log(1, 'Maxma setOrder error: ' . print_r($data, 1));
            return false;
        } else {
            return $data;
        }
    }
    
    // Подтверждение оплаты заказа
    public function confirmOrder(string $order_id) {
        $params = [];
        $params['orderId'] = $order_id;
        
        $response = $this->modRestClient->post('confirm-order', $params);
        $data = $response->process();
        
        if (isset($data['errorCode'])) {
            $this->modx->log(1, 'Maxma confirmOrder error: ' . print_r($data, 1));
            return false;
        } else {
            return $data;
        }
    }
    
    // Отмена заказа и возврат зарезервированных бонусов
    public function cancelOrder(string $order_id) {
        $params = [];
        $params['orderId'] = $order_id;
        
        $response = $this->modRestClient->post('cancel-order', $params);
        $data = $response->process();
        
        if (isset($data['errorCode'])) {
            $this->modx->log(1, 'Maxma cancelOrder error: ' . print_r($data, 1));
            return false;
        } else {
            return $data;
        }
    }
}
