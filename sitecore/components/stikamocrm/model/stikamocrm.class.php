<?php
require_once __DIR__ . '/modrestcustom.class.php';
require_once __DIR__ . '/stikamocrmqueue.class.php';

class stikAmoCRM
{
    /** @var modX $modx */
    public $modx;
    /** @var pdoFetch $pdo */
    public $pdo;
    /** @var rest.modRest $modRest */
    public $modRest;
    /** @var stikAmoCRMQueue $queue */
    public $queue;
    private $header_token;

    /**
     * @param modX $modx
     * @param array $config
     */
    function __construct(modX &$modx, array $config = [])
    {
        $this->modx =& $modx;
        $this->pdo = $this->modx->getService('pdoFetch');
        $corePath = MODX_CORE_PATH . 'components/stikamocrm/';
        $assetsUrl = MODX_ASSETS_URL . 'components/stikamocrm/';

        $this->config = array_merge([
            'corePath' => $corePath,
            'modelPath' => $corePath . 'model/',
            'processorsPath' => $corePath . 'processors/',

            'connectorUrl' => $assetsUrl . 'connector.php',
            'assetsUrl' => $assetsUrl,
            'cssUrl' => $assetsUrl . 'css/',
            'jsUrl' => $assetsUrl . 'js/',
            'redirectUri' => $this->modx->getOption('site_url'),
            'account' => $this->modx->getOption('stikamocrm_account'),
            'authCode' => $this->modx->getOption('stikamocrm_auth_code'),
            'clientId' => $this->modx->getOption('stikamocrm_client_id'),
            'clientSecret' => $this->modx->getOption('stikamocrm_client_secret'),
            'accessToken' => $this->modx->getOption('stikamocrm_access_token'),
            'refreshToken' => $this->modx->getOption('stikamocrm_refresh_token'),
            'fieldsId' => $this->modx->getOption('stikamocrm_fields_id'),
            'statusesId' => $this->modx->getOption('stikamocrm_statuses_id'),
            'orderPipelineId' => $this->modx->getOption('stikamocrm_order_pipeline_id'),
            'catalogId' => $this->modx->getOption('stikamocrm_catalog_id'),
        ], $config);

        // $this->modx->addPackage('stikamocrm', $this->config['modelPath']);
        $this->modx->lexicon->load('stikamocrm:default');
        $this->modx->lexicon->load('minishop2:default');

        $this->loadModRest();
        $this->queue = new stikAmoCRMQueue($this);
    }

    // Обмен кода авторизации на access token и refresh token
    public function getAccessToken()
    {
        if (empty($this->config['authCode'])) return $this->modx->log(MODX_LOG_LEVEL_ERROR, 'stikAmoCRM error: Отсутствует auth_code');

        $params = [
            'client_id' => $this->config['clientId'],
            'client_secret' => $this->config['clientSecret'],
            'grant_type' => 'authorization_code',
            'code' => $this->config['authCode'],
            'redirect_uri' => $this->config['redirectUri']
        ];

        $response = $this->modRest->post('oauth2/access_token', $params);
        $result = $response->process();

        if (isset($result['errorCode'])) {
            $this->modx->log(MODX_LOG_LEVEL_ERROR, 'stikAmoCRM getAccessToken error: ' . print_r($result, 1));
        } else {
            $this->saveTokens($result);
        }
    }

    // Получение нового access token по его истечении
    public function refreshAccessToken()
    {
        if (empty($this->config['refreshToken'])) return $this->modx->log(MODX_LOG_LEVEL_ERROR, 'stikAmoCRM error: Отсутствует refresh_token');

        $params = [
            'client_id' => $this->config['clientId'],
            'client_secret' => $this->config['clientSecret'],
            'grant_type' => 'refresh_token',
            'refresh_token' => $this->config['refreshToken'],
            'redirect_uri' => $this->config['redirectUri']
        ];

        $response = $this->modRest->post('oauth2/access_token', $params);
        $result = $response->process();

        if (isset($result['errorCode'])) {
            $this->modx->log(MODX_LOG_LEVEL_ERROR, 'stikAmoCRM refreshAccessToken error: ' . print_r($result, 1));
        } else {
            $this->saveTokens($result);
        }
    }

    public function saveTokens(array $result) {
        if (!empty($result['access_token']) && !empty($result['refresh_token'])) {
            $access_token = $this->modx->getObject('modSystemSetting', 'stikamocrm_access_token');
            $access_token->set('value', $result['access_token']);
            $access_token->save();

            $refresh_token = $this->modx->getObject('modSystemSetting', 'stikamocrm_refresh_token');
            $refresh_token->set('value', $result['refresh_token']);
            $refresh_token->save();

            $this->modx->cacheManager->refresh([
                'system_settings' => [],
                'resource' => [],
            ]);
        }
    }

    public function createOrder(msOrder $msOrder) {
        $order = $this->orderCombine($msOrder);
        $fieldsId = json_decode($this->config['fieldsId'], 1);
        $statusesId = json_decode($this->config['statusesId'], 1);
        $customAddressFields = [];
        $goods = $this->getPreparedOrderProducts($order['id']);
        $email = $order['profile']['email'];
        $phone = $this->preparePhone($order['address']['phone']);
        $status = $order['status'] > 0 ? $order['status'] : 1;

        foreach ($order['address'] as $k => $v) {
            if (isset($fieldsId['address.' . $k]) && !empty($v)) {
                $customAddressFields[] = [
                    'field_id' => $fieldsId['address.' . $k],
                    'values' => [
                        [
                            'value' => (string)$v,
                        ],
                    ],
                ];
            }
        }
        
        $customFields = [
            [
                'field_id' => $fieldsId['contact_email'],
                'values' => [
                    [
                        'value' => $email,
                    ],
                ],
            ],
            [
                'field_id' => $fieldsId['contact_name'],
                'values' => [
                    [
                        'value' => $order['address']['receiver'] ?: ($order['address']['name'] . ' ' . $order['address']['surname']),
                    ],
                ],
            ],
            [
                'field_id' => $fieldsId['delivery_cost'],
                'values' => [
                    [
                        'value' => (string)$order['delivery_cost'],
                    ],
                ],
            ],
            [
                'field_id' => $fieldsId['goods'],
                'values' => [
                    [
                        'value' => implode("\n", $goods),
                    ],
                ],
            ],
            [
                'field_id' => $fieldsId['num'],
                'values' => [
                    [
                        'value' => $order['num'],
                    ],
                ],
            ],
        ];

        $params = [
            [
                'name' => $this->modx->lexicon('stikamocrm_order_name', ['num' => $order['num']]),
                'price' => $order['cart_cost'],
                'pipeline_id' => (int)$this->config['orderPipelineId'],
                'created_at' => strtotime($order['createdon']),
                'custom_fields_values' => array_merge($customAddressFields, $customFields),
            ],
        ];
        
        if (isset($statusesId[$status])) {
            $params[0]['status_id'] = $statusesId[$status];
        }

        if (!$contact = $this->searchContact($email)) {
            $contact = $this->searchContact($phone);
        }
        
        if (!$contact) {
            $params[0]['_embedded'] = [
                'contacts' => [
                    [
                        'first_name' => $order['address']['name'],
                        'last_name' => $order['address']['surname'],
                        'custom_fields_values' => [
                            [
                                'field_id' => $fieldsId['websiteId'],
                                'values' => [
                                    [
                                        'value' => (string)$order['user_id'],
                                    ],
                                ],
                            ],
                            [
                                'field_id' => $fieldsId['PHONE'],
                                'values' => [
                                    [
                                        'value' => $phone,
                                        'enum_code' => "WORK",
                                    ],
                                ],
                            ],
                            [
                                'field_id' => $fieldsId['EMAIL'],
                                'values' => [
                                    [
                                        'value' => $email,
                                        'enum_code' => "WORK",
                                    ],
                                ],
                            ],
                        ],
                    ],
                ],
            ];
        } else {
            $params[0]['_embedded'] = [
                'contacts' => [
                    [
                        'id' => $contact,
                    ],
                ],
            ];
        }

        $response = $this->modRest->post('api/v4/leads/complex', $params, $this->header_token);
        $result = $response->process();

        if (isset($result['errorCode']) || !isset($result[0]['id'])) {
            $this->modx->log(MODX_LOG_LEVEL_ERROR, 'stikAmoCRM createOrder error: ' . print_r($result, 1));
        } else {
            $orderProperties = $msOrder->get('properties');
            if (!is_array($orderProperties)) $orderProperties = [];
            $orderProperties['amo_lead_id'] = $result[0]['id'];
            $msOrder->set('properties', $orderProperties);
            $msOrder->save();
            
            return $result[0]['id'];
        }
        return false;
    }

    public function addProductsToLead(int $amoLeadId, int $orderId) {
        // Линкуем товары к лиду
        $catalog_id = $this->config['catalogId']; // id каталога с товарами в AmoCRM
        if (!$catalog_id) return;
        
        $msOrder = $this->modx->getObject('msOrder', $orderId);
        $msOrderProducts = $msOrder->getMany('Products');
        
        foreach ($msOrderProducts as $orderProduct) {
            $options = $orderProduct->get('options');
            if (isset($options['color']) && isset($options['size'])) {
                $msProductData = $this->modx->getObject('msProductData', $orderProduct->get('product_id'));
                $article = $msProductData->get('article');
                if ($article) {
                    $response = $this->modRest->get('api/v2/catalog_elements?catalog_id='.$catalog_id.'&term=' . $article, [], $this->header_token);
                    $result = $response->process();
        
                    if (isset($result['_embedded']['items'])) {
                        $products = $result['_embedded']['items'];
                        $links = [];
                        foreach ($products as $product) {
                            if (mb_stripos($product['name'], $options['size']) !== false && mb_stripos($product['name'], $options['color']) !== false) {
                                $links[] = [
                                    'to_entity_id' => (int)$product['id'],
                                    'to_entity_type' => 'catalog_elements',
                                    'metadata' => [
                                        'quantity' => (int)$orderProduct->get('count'),
                                        'catalog_id' => (int)$catalog_id,
                                    ]
                                ];
                            }
                        }
                    }
                }
            }
        }
        
        if (count($links)) {
            $response = $this->modRest->post('api/v4/leads/'.$amoLeadId.'/link', $links, $this->header_token);
            $result = $response->process();
            return $result;
        }
    }
    
    public function changeOrderStatus(msOrder $msOrder)
    {
        $statusesId = json_decode($this->config['statusesId'], 1);
        $status = $msOrder->get('status');
        if (!isset($statusesId[$status])) return false;
        
        $orderProperties = $msOrder->get('properties');
        if (!empty($orderProperties['amo_lead_id'])) {
            $params = [
                [
                    'id' => $orderProperties['amo_lead_id'],
                    'status_id' => $statusesId[$status],
                ]
            ];
            $this->modRest->patch('api/v4/leads', $params, $this->header_token);
            return true;
        }
        return false;
    }
    
    public function createFormLead(array $data, array $amoFields)
    {
        $customFields = [];
        foreach ($amoFields as $k => $v) {
            if (!isset($data[$k])) continue;
            $customFields[] = [
                'field_id' => (int)$v,
                'values' => [
                    [
                        'value' => $data[$k],
                    ],
                ],
            ];
        }
        $params = [
            [
                'name' => isset($data['form_name']) ? $data['form_name'] : $this->modx->lexicon('stikamocrm_form_name_new', ['date' => date('Y-m-d H:i:s')]),
                'price' => isset($data['price']) ? $data['price'] : 0,
                // 'status_id' => $this->config['form_status_new'],
                'pipeline_id' => (int)$this->config['orderPipelineId'],
                'created_at' => (int)strtotime($order['createdon']),
                'custom_fields_values' => $customFields,
            ]
        ];

        if (!$contact = $this->searchContact($data['email'])) {
            $contact = $this->searchContact($data['phone']);
        }

        if ($contact) {
            $params[0]['_embedded'] = [
                'contacts' => [
                    [
                        'id' => $contact,
                    ],
                ],
            ];
        }
        
        $response = $this->modRest->post('api/v4/leads', $params, $this->header_token);
        $result = $response->process();
        if (isset($result['errorCode']) || !isset($result['_embedded']['leads'][0]['id'])) {
            $this->modx->log(MODX_LOG_LEVEL_ERROR, 'stikAmoCRM createFormLead error: ' . print_r($result, 1));
        } else {
            return $result['_embedded']['leads'][0]['id'];
        }
        return false;
    }

    /**
     * @param msOrder $msOrder
     * @return mixed
     */
    public function orderCombine(msOrder $msOrder)
    {
        $order = $msOrder->toArray();

        $order['address'] = $this->pdo->getArray(
            'msOrderAddress',
            ['id' => $order['address']],
            ['sortby' => 'id']
        );

        $order['delivery'] = $this->pdo->getArray(
            'msDelivery',
            ['id' => $order['delivery']],
            ['sortby' => 'id']
        );

        $order['payment'] = $this->pdo->getArray(
            'msPayment',
            ['id' => $order['payment']],
            ['sortby' => 'id']
        );

        $order['profile'] = $this->pdo->getArray(
            'modUserProfile',
            ['internalKey' => $order['user_id']],
            ['sortby' => 'id']
        );

        $order['products'] = $this->pdo->getCollection(
            'msOrderProduct',
            ['order_id' => $order['id']],
            ['sortby' => 'id']
        );

        return $order;
    }
    
    public function getPreparedOrderProducts($orderId)
    {
        $goods = [];
        $i = 1;

        $q = $this->modx->newQuery('msOrderProduct');
        $q->leftJoin('msProductData', 'msProductData', 'msProductData.id = msOrderProduct.product_id');
        $q->where(['order_id' => $orderId]);
        $q->select(['msOrderProduct.*']);
        $q->select(['msProductData.article']);
        $q->prepare();
        $q->stmt->execute();
        $products = $q->stmt->fetchAll(PDO::FETCH_ASSOC);

        foreach ($products as $product) {
            $goods[] = $this->modx->lexicon('stikamocrm_order_product_row', [
                'idx' => $i,
                'article' => $product['article'],
                'name' => $product['name'],
                'price' => $product['price'],
                'count' => $product['count'],
                'cost' => $product['cost'],
                'options' => $this->multiImplode('; ', json_decode($product['options'], true)),
                'currency' => $this->modx->lexicon('ms2_frontend_currency'),
                'unit' => $this->modx->lexicon('ms2_frontend_count_unit'),
            ]);
            $i++;
        }
        return $goods;
    }
    
    /**
     * Получение информации о контактах
     *
     * @param string $query Строка запроса
     * @param int|array $responsible_user_id
     * @param int $limit
     *
     * @return array
     */

    public function searchContact($query = '', $responsible_user_id = [], $limit = 1)
    {
        if (is_numeric($responsible_user_id)) {
            $responsible_user_id = [$responsible_user_id];
        }
        $data = [
            'page' => 1,
            'limit' => $limit,
        ];
        if (!empty($query)) {
            $query = trim($query);
            $query = preg_replace('/[^a-zA-ZА-Яа-я0-9@.\-+]/i', '', $query);
            $data['query'] = $query;
        }
        if (!empty($responsible_user_id)) {
            $data['responsible_user_id'] = $responsible_user_id;
        }
        
        $response = $this->modRest->get('api/v4/contacts', $data, $this->header_token);
        $result = $response->process();

        if (isset($result['errorCode'])) {
            $this->modx->log(MODX_LOG_LEVEL_ERROR, 'stikAmoCRM getContacts error: ' . print_r($result, 1));
        } else {
            if (isset($result['_embedded']['contacts'][0]['id'])) {
                return $result['_embedded']['contacts'][0]['id'];
            }
        }
        return false;
    }

    // Преобразование телефона к единому виду
    public function preparePhone($phone)
    {
        $phone = preg_replace('/[^0-9+]/', '', $phone);
        return $phone;
    }

    public function multiImplode($glue, $array)
    {
        $_array = array();
        if (!empty($array) && is_array($array)) {
            foreach ($array as $val) {
                $_array[] = is_array($val) ? $this->multiImplode($glue, $val) : $val;
            }
        }
        return implode($glue, $_array);
    }

    protected function loadModRest() {
        /* @var modRest $this->modRest */
        // $this->modRest = $this->modx->getService('rest', 'rest.modRest');
        $this->modRest = new modRestCustom($this->modx);
        $this->modRest->setOption('baseUrl', 'https://' . $this->config['account'] . '.amocrm.ru');
        $this->modRest->setOption('format', 'json');
        $this->modRest->setOption('userAgent', 'amoCRM-oAuth-client/1.0');
        $this->modRest->setOption('sslVerifypeer', 1);
        $this->modRest->setOption('suppressSuffix', true);
        $this->modRest->setOption('headers', [
            'Content-type' => 'application/json',
        ]);
        
        $this->header_token = [
            'Authorization' => 'Bearer ' . $this->config['accessToken']
        ];
    }
}