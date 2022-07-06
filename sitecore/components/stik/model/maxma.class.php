<?php
/**
 * The base class for maxma.
 */

class maxma {

    /* @var modX $modx */
    public $modx;
    public $namespace = 'stik';
    public $config = [];
    public $userphone;
    /* @var miniShop2*/
    public $ms2;
    /* @var msPromoCode*/
    public $mspc;
    /**
     * @param modX $modx
     * @param array $config
     */
    function __construct (modX &$modx, array $config = []) {
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
        if ($user = $this->modx->getUser('web')) {
            if ($profile = $user->getOne('Profile')) {
                if ($profile->get('mobilephone') && $profile->get('join_loyalty')) {
                    $this->userphone = $this->preparePhone($profile->get('mobilephone'));
                }
            }
        }
    }

    // Создание нового клиента
    public function createNewClient(array $data) {
        if (!empty($params['phoneNumber'])) {
            $client = $this->getClientInfo($data['phoneNumber'], 'phoneNumber');
        }
        if (!$client) {
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
            $client = $response->process();
        }

        if (isset($client['errorCode'])) {
            $this->modx->log(1, 'Maxma createNewClient error: ' . print_r($client, 1));
            return false;
        } else {
            return $client;
        }
    }

    // Получает общую информацию о клиенте
    public function getClientInfo(string $input, string $type = 'phoneNumber') {
        if (!in_array($type, ['phoneNumber', 'externalId', 'card']) || !$input) return false;
        if ($type == 'phoneNumber') $input = $this->preparePhone($input);

        $params = [];
        $params[$type] = $input;

        $response = $this->modRestClient->post('get-balance', $params);
        $data = $response->process();

        if (isset($data['errorCode'])) {
            // $this->modx->log(1, 'Maxma getClientInfo error: ' . print_r($data, 1));
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

    // Преобразование телефона к единому виду
    public function preparePhone($phone) {
        $phone = preg_replace('/[^0-9+]/', '', $phone);
        return $phone;
    }

    public function setUserphone($phone) {
        $this->userphone = $this->preparePhone($phone);
    }

    // Обновляет информацию о клиенте.
    // Доступные параметры https://docs.maxma.com/api/#tag/Rabota-s-klientskoj-bazoj/paths/~1update-client/post
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
        $balance = str_replace(' ', '', $this->modx->runSnippet('msMultiCurrencyPriceFloor', ['price' => $balance, 'format' => false]));

        if ($bonuses <= $balance) return true;

        return $this->modx->lexicon('stik_loyalty_err_not_enough_bonuses');
    }
    public function calculatePurchase($promocode='',$bonuses='',$phone = ''){

        $cart = $this->ms2->cart->get();
        if(count($cart) == 0){
            return false;
        }
        if($phone){
            $this->setUserphone($phone);
        }
        $params = [
            'calculationQuery' => [
                'shop'=> [
                    "code"=>$this->config['shopCode'],
                    "name"=>$this->config['shopName'],
                ],
                "rows"=>[],
            ]
        ];
        if($this->userphone){
            $params['calculationQuery']['client']=[
                'phoneNumber' => (string)$this->userphone
            ];
        }
        if($promocode){
            $params['calculationQuery']['promocode'] = $promocode;
        }
        if($bonuses){
            $params['calculationQuery']['applyBonuses'] = $bonuses;
        }
        foreach ($cart as $key => $product) {
            $msProduct = $this->modx->getObject('msProduct',$product['id']);
            if(empty($product['price']) || empty($product['count']))
                continue;
            $params['calculationQuery']['rows'][] = [
                "id"=> (string)$key,
                "product"=>[
                    "sku"=> (string) $msProduct->get('article'),
                    "title"=> $msProduct->get('pagetitle'),
                    "blackPrice"=>$product['base_price'],
                ],
                "qty"=> (int)$product['count'],
            ];
        }
        if(count($params['calculationQuery']['rows']) == 0){
            $this->modx->log(1, 'Maxma calculatePurchase error (count or price)');
            return false;
        }
        $response = $this->modRestClient->post('v2/calculate-purchase', $params);
        $data = $response->process();

        if (isset($data['errorCode'])) {
            $this->modx->log(1, 'Maxma calculatePurchase error: ' . print_r($data, 1));
            return false;
        } else {
            return $data;
        }
    }
    public function setPromocode($promocode){

        $this->calculatePurchase($promocode);
    }
    public function canUsePromocode($promocode){
        $result = $this->calculatePurchase($promocode);
        return $result['calculationResult']['promocode']['applied'] ? true : false;
    }
    // Создание/изменение заказа
    public function setOrder(int $order_id, int $bonuses = 0,$phone = '') {
        $order = $this->modx->getObject('msOrder', $order_id);
        if (!$order) return false;
        $mspcOrder = $this->modx->getObject('mspcOrder',['order_id'=>$order_id]);
        if($mspcOrder && empty($mspcOrder->get('code'))){
            return false;
        }
        if($phone){
            $this->setUserphone($phone);
        }
        $params = [
            'orderId' => (string) $order_id,
            'calculationQuery' => [
                'client'=>[
                    'phoneNumber'=> $this->userphone,
                ],
                'shop'=> [
                    "code"=>$this->config['shopCode'],
                    "name"=>$this->config['shopName'],
                ],
                'rows'=>[],
            ],

        ];
        $products = $order->getMany('Products');
        if(count($products) == 0)
            return false;
        foreach ($products as $product){
            $msProduct = $product->getOne('Product');
            $filter = ['product_id' => $msProduct->get('id')];
            $options = $product->get('options');
            if($options['size']){
                $filter['size'] = $options['size'];
            }
            if($options['color']){
                $filter['color'] = $options['color'];
            }
            if($stikProduct =  $this->modx->getObject('stikRemains',$filter)) {
                $params['calculationQuery']['rows'][] = [
                    'id' => (string)$stikProduct->get('id'),
                    'product' => [
                        'blackPrice' => $options['base_price'] ?? $stikProduct->get('price'),
                        'sku' => (string)$msProduct->get('article'),
                    ],
                    'qty' => $product->get('count'),
                ];
            }
        }
        if($mspcOrder){
            $params['calculationQuery']['promocode'] = $mspcOrder->get('code');
        }
        if(empty($params['calculationQuery']['promocode']) && !empty($bonuses) && $bonuses > 0){
            $params['calculationQuery']['applyBonuses'] = $bonuses;
        }
        $response = $this->modRestClient->post('v2/set-order', $params);
        $data = $response->process();

        if (isset($data['errorCode'])) {
            $this->modx->log(1, 'Maxma setOrder error: ' . var_export($data, 1));
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

    // Отмена заказа и возврат зарезервированных бонусов
    public function returnOrder(string $order_id) {
        $order = $this->modx->getObject('msOrder', $order_id);
        if (!$order) return false;
        $address = $order->getOne('Address');

        $params = [];
        $params['transaction'] = [
            'phoneNumber' => $address->get('phone'),
            'id' => $order_id,
            'executedAt' => date("c"),
            'purchaseId' => $order_id,
            'shopCode' => $this->config['shopCode'],
            'shopName' => $this->config['shopName'],
            'refundAmount' => $order->get('cost'),
        ];

        $response = $this->modRestClient->post('apply-return', $params);
        $data = $response->process();

        if (isset($data['errorCode'])) {
            $this->modx->log(1, 'Maxma returnOrder error: ' . print_r($data, 1));
            return false;
        } else {
            return $data;
        }
    }
    public function setCertificate($name){
        if(!key_exists($name,json_decode($this->modx->getOption('maxma_certificates',null,[]),true))){
            return $this->modx->lexicon('stik_loyalty_err_certificate_not_found');
        }
        $params = [
            'code'=>$name,
        ];
        $response = $this->modRestClient->post('generate-gift-card', $params);
        $data = $response->process();
        if (isset($data['errorCode'])) {
            $this->modx->log(1, 'Maxma setCertificate error: ' . print_r($data, 1));
            return false;
        } else {
            return $data;
        }
    }
}
