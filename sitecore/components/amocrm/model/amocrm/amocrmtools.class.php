<?php

require_once dirname(__FILE__) . '/controllers/auth.php';

class amoCRMTools
{
    const SQ_SERVICE = 'amoCRM';
    const SQ_ACTION_ADD_FORM = 1;
    const SQ_ACTION_ADD_ORDER = 2;
    const SQ_ACTION_ADD_CONTACT = 3;
    const SQ_ACTION_UPDATE_CONTACT = 4;
    const SQ_ACTION_CHANGE_ORDER_STATUS = 5;
    const SQ_ACTION_WEBHOOK_LEADS_STATUSES = 6;
    const SQ_ACTION_WEBHOOK_UPDATE_USER = 7;

    const WEBHOOK_DELAY_FOR_REPEAT = 2;

    /** @var modX $modx */
    public $modx;
    /** @var amoCRM $amoCRM */
    public $amoCRM;
    /** @var simpleQueue $sq */
    public $sq = null;
    /** @var amoCRMWebhook $webhook */
    public $webhook = null;
    /** @var minishop2 $ms2 */
    public $ms2 = null;
    /** @var array $config */
    public $config;
    /** @var int $ountRequests */
    public $countRequests = 0;
    public $addedSQTasks = array();
    public $eventResponses = array();
    public $account_config = array();

    public $authController;

    function __construct($modx, $amo)
    {
        $this->modx = $modx;
        $this->amoCRM = $amo;

        $config = $this->amoCRM->config;

        $corePath = $this->modx->getOption('amocrm_core_path', $config,
            $this->modx->getOption('core_path') . 'components/amocrm/'
        );
        $assetsUrl = $this->modx->getOption('amocrm_assets_url', $config,
            $this->modx->getOption('assets_url') . 'components/amocrm/'
        );
        $connectorUrl = $assetsUrl . 'connector.php';

        $this->config = array_merge(array(
            'assetsUrl' => $assetsUrl,
            'cssUrl' => $assetsUrl . 'css/',
            'jsUrl' => $assetsUrl . 'js/',
            'imagesUrl' => $assetsUrl . 'images/',
            'connectorUrl' => $connectorUrl,
            'corePath' => $corePath,
            'modelPath' => $corePath . 'model/',
            'chunksPath' => $corePath . 'elements/chunks/',
            'templatesPath' => $corePath . 'elements/templates/',
            'chunkSuffix' => '.chunk.tpl',
            'snippetsPath' => $corePath . 'elements/snippets/',
            'processorsPath' => $corePath . 'processors/',
            'domain' => $this->getSetting('amocrm_domain', 'amocrm.ru'),
            'protocol' => 'https',
            'account' => $this->getSetting('amocrm_account'),
            'client_id' => $this->getSetting('amocrm_client_id'),
            'client_secret' => $this->getSetting('amocrm_client_secret'),
            'client_code' => $this->getSetting('amocrm_client_code'),
            'pipeline' => $this->getSetting('amocrm_pipeline_id'),
            'categories_pipelines' => $this->getSetting('amocrm_categories_pipelines'),
            'responsible_id_priority_category' => $this->getSetting('amocrm_responsible_id_priority_category'),
            'form_pipeline' => $this->getSetting('amocrm_form_pipeline_id'),
            'form_as_lead' => $this->getSetting('amocrm_form_as_lead'),
            'form_filled_fields' => $this->getSetting('amocrm_form_filled_fields'),
            'form_status_new' => $this->getSetting('amocrm_form_status_new'),
            'statusNewOrder' => $this->getSetting('amocrm_new_order_status_id'),
            'spam_to_modx_log' => $this->getSetting('amocrm_spam_modx_log', false),
            'defaultOrderFields' => array(
                'order_id',
                'name',
                'status_id',
                'price',
                'pipeline_id',
                'date_create',
                'responsible_user_id',
                'tags',
                'company_id'
            ),
            'orderFields' => $this->getSetting('amocrm_order_fields', ''),
            'orderPropertiesElement' => $this->getSetting('amocrm_order_properties_element', 'amoCRMFields'),
            'orderAddressFields' => $this->getSetting('amocrm_order_address_fields', ''),
            'orderAddressFieldsPrefix' => $this->getSetting('amocrm_order_address_fields_prefix', 'address.'),
            'defaultUserFields' => array(
                'name',
                'created_at',
                'updated_at',
                'responsible_user_id',
                'tags',
                'company_id',
                'company_name'
            ),
            'userFields' => $this->getSetting('amocrm_user_fields', ''),
            'userEnumFields' => $this->getSetting('amocrm_user_enum_fields', ''),
            'userReadonlyFields' => $this->getSetting('amocrm_user_readonly_fields', 'name'),
            'userFieldsGlueAmoValues' => $this->getSetting('amocrm_user_fields_glue_amo_values', ''),
            'userSaveInMgr' => $this->getSetting('amocrm_save_user_in_mgr', false),
            'userSaveByProfile' => $this->getSetting('amocrm_save_user_by_profile', false),
            'defaultResponsibleUserId' => $this->getSetting('amocrm_default_responsible_user_id', 0),
            'useSimpleQueue' => $this->getSetting('amocrm_use_simple_queue', false),
            'autoCreateUsersCustomFields' => $this->getSetting('amocrm_auto_create_users_fields', false),
            'autoCreateOrdersCustomFields' => $this->getSetting('amocrm_auto_create_orders_fields', false),
            'skipEmptyFields' => $this->getSetting('amocrm_skip_empty_fields', false),
            'autoUpdatePipelines' => $this->getSetting('amocrm_auto_update_pipelines', false),
            'site_url' => $this->modx->getOption('site_url')
        ), $config);

        $this->modx->addPackage('amocrm', $this->config['modelPath']);
        $this->modx->lexicon->load('amocrm:default');

        $this->authController = new Auth($this->modx, $this);
        $this->updateAccountConfig();
    }


    /**
     * Получение настройки (Сокращененный вариант)
     *
     * @param $key
     * @param null $default
     * @param bool $parse
     *
     * @return mixed
     */
    public function getSetting($key, $default = null, $parse = false)
    {
        $setting = $this->modx->getOption($key, null, $default);
        return $parse ? $this->parseFieldsSet($setting) : $setting;
    }


    /**
     * Сохранение системной настройки
     *
     * @param $key
     * @param $value
     *
     * @return mixed
     */
    public function setSetting($key, $value)
    {
        $result = false;
        if ($setting = $this->modx->getObject('modSystemSetting', $key)
        ) {
            $setting->set('value', $value);
            $result = $setting->save();
            $this->modx->cacheManager->refresh(array(
                'system_settings' => array('key' => $key)
            ));
        }
        return $result;
    }


    /**
     * Parse fields string
     *
     * @param string|array $fieldsSet
     *
     * @return array
     */

    public function parseFieldsSet($fieldsSet)
    {
        $formFields = array();
        if (is_array($fieldsSet)) {
            $formFields = $fieldsSet;
        } elseif (is_string($fieldsSet)) {
            if ($fieldsSet[0] == '{') {
                $formFields = $this->modx->fromJSON($fieldsSet);
            } elseif (strpos($fieldsSet, '==') > 0 or strpos($fieldsSet, '||') > 0) {
                $fieldsSet = explode('||', $fieldsSet);
                foreach ($fieldsSet as & $field) {
                    $field = explode('==', $field);
                    if (empty($field[1])) {
                        $field[1] = $field[0];
                    }
                    $formFields[$field[0]] = $field[1];
                }
            } else {
                $formFields = explode(',', $fieldsSet);
            }
        }
        return $formFields;
    }


    public function normalizeRusPhone($basePhone)
    {
        $phone = preg_replace("#[^\d]#", "", $basePhone);
        /** Добавление кода страны 7 для номеров без кода  */
        if (9000000000 <= $phone and $phone <= 9999999999) {
            $phone += 70000000000;
        }
        /** Замена 8 на 7 в начале номера */
        if (80000000000 <= $phone and $phone <= 89999999999) {
            $phone -= 10000000000;
        }

        if (70000000000 <= $phone and $phone <= 79999999999) {
            return '+' . (string)$phone;
        }
        return $basePhone;
    }


    public function mergeOrderOptions($orderProps, $newProps, $contactFields = array())
    {
        $propsElem = $this->config['orderPropertiesElement'];
        $amoProperties = &$orderProps[$propsElem];

        if (!is_array($amoProperties)) {
            $amoProperties = array();
        }
        if (!is_array($amoProperties['contactFields'])) {
            $amoProperties['contactFields'] = array();
        }

        $amoProperties = array_replace($amoProperties, $newProps);
        $amoProperties['contactFields'] = array_replace($amoProperties['contactFields'], $contactFields);

        return $orderProps;

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


    public function deleteFromArray($needle, $array, $all = true)
    {
        if (!$all) {
            if (false !== $key = array_search($needle, $array)) {
                unset($array[$key]);
            }
            return $array;
        }
        foreach (array_keys($array, $needle) as $key) {
            unset($array[$key]);
        }

        return $array;
    }


    /**
     * Обновление свойств аккаунта
     */
    public function updateAccountConfig()
    {
        $account = $this->sendRequest('/api/v4/account', array(), 'GET');
        $this->account_config = $account;
        $leads_custom_fields = $this->getCustomFields($type = 'leads');
        if ($leads_custom_fields) {
            $this->account_config['custom_fields']['leads'] = $leads_custom_fields;
        }
        $contacts_custom_fields = $this->getCustomFields($type = 'contacts');
        if ($contacts_custom_fields) {
            $this->account_config['custom_fields']['contacts'] = $contacts_custom_fields;
        }

        $pipelines = $this->getPipelinesForAccount();
        if ($pipelines) {
            $this->account_config['pipelines'] = $pipelines;
        }

        return $this->account_config;
    }

    public function getPipelinesForAccount()
    {
        $result = $this->sendRequest('/api/v4/leads/pipelines', array(), 'GET');
        if (!empty($result) && $result['_total_items'] > 0) {
            $output = [];
            foreach ($result['_embedded']['pipelines'] as $item) {
                $output[$item['id']] = [
                    'id' => $item['id'],
                    'name' => $item['name'],
                    'statuses' => $item['_embedded']['statuses']
                ];
            }

            return $output;
        }
        return false;
    }

    /**
     * Запрос кастомных полей в AMOCRM
     *
     * @param string $type
     *
     * @return array|false
     * @internal param string $type
     */
    public function getCustomFields($type = 'leads')
    {
        $result = $this->sendRequest('/api/v4/' . $type . '/custom_fields', array(), 'GET');
        if (!empty($result) && $result['_total_items'] > 0) {

            $output = [];
            foreach ($result['_embedded']['custom_fields'] as $field) {
                if (!empty($field['code'])) {
                    $output[mb_strtolower($field['code'])] = $field;
                } elseif (!empty($field['name'])) {
                    $output[mb_strtolower($field['name'])] = $field;
                }
            }

            if($result['_page_count'] > 1) {
                for ($i = $result['_page'] + 1; $i <= $result['_page_count']; $i++) {
                    $result = $this->sendRequest('/api/v4/' . $type . '/custom_fields', array('page' => $i), 'GET');
                    foreach ($result['_embedded']['custom_fields'] as $field) {
                        if (!empty($field['code'])) {
                            $output[mb_strtolower($field['code'])] = $field;
                        } elseif (!empty($field['name'])) {
                            $output[mb_strtolower($field['name'])] = $field;
                        }
                    }
                }
            }


            return $output;
        }
        return false;
    }

    /**
     * Подготовка ссылки для отправки и/или получения данных
     *
     * @param string $url
     *
     * @return string
     * @internal param string $type
     */
    public function prepareLink($url = '')
    {
        $link = '';
        if ($this->config['protocol'] && $this->config['account']) {
            $link = $this->config['protocol'] . '://' . $this->config['account'] . '.' . $this->config['domain'] . $url;
        }
        return $link;
    }


    /**
     * Отправка запроса
     *
     * @param $link
     * @param $data
     * @param string $method
     *
     * @return array
     */
    public function sendRequest($link, $data, $method = 'POST')
    {
        $authorized = $this->authController->checkAuth();
        if ($authorized) {
            $link = $this->prepareLink($link);
            return $this->sendCURL($link, $data, $method);
        }
    }


    /**
     * Отправка запроса
     *
     * @param string $link
     * @param array $data
     * @param string $method
     *
     * @return array
     * @internal param $post
     */
        public function sendCURL($link, $data, $method = 'POST')
    {
        $headers = [];
        $headers[] = 'Content-Type:application/json';

        $token = $this->authController->token;
        $authorized = $this->authController->authorized;

        if ($authorized && !empty($token)) {
            $headers[] = 'Authorization: Bearer ' . $token;
        }

        $userAgent = 'amoCRM-API-client/1.0';

        if (!empty($data['grant_type'])) {
            switch ($data['grant_type']) {
                case 'authorization_code':
                case 'refresh_token':
                    $userAgent = 'amoCRM-oAuth-client/1.0';
                    break;
            }
        }

        if ($method === 'GET') {
            if (!empty($data)) {
                $link = $link . '?' . http_build_query($data);
            }
        }

        $curl = curl_init();
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);

        curl_setopt($curl, CURLOPT_USERAGENT, $userAgent);

        curl_setopt($curl, CURLOPT_URL, $link);
        if (!empty($headers)) {
            curl_setopt($curl, CURLOPT_HTTPHEADER, $headers);
        }

        if ($method === 'GET') {
            curl_setopt($curl, CURLOPT_CUSTOMREQUEST, 'GET');
        }

        if ($method === 'POST') {
            curl_setopt($curl, CURLOPT_CUSTOMREQUEST, 'POST');
            curl_setopt($curl, CURLOPT_POSTFIELDS, json_encode($data));
        }

        if ($method === 'PATCH') {
            curl_setopt($curl, CURLOPT_CUSTOMREQUEST, 'PATCH');
            curl_setopt($curl, CURLOPT_POSTFIELDS, json_encode($data));
        }

        if ($method === 'DELETE') {
            curl_setopt($curl, CURLOPT_CUSTOMREQUEST, 'DELETE');
            curl_setopt($curl, CURLOPT_POSTFIELDS, json_encode($data));
        }

        curl_setopt($curl,CURLOPT_HEADER, false);

        curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, 1);
        curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, 2);
        $out = curl_exec($curl);
        $code = curl_getinfo($curl, CURLINFO_HTTP_CODE);
        curl_close($curl);

        $code = (int)$code;
        $errors = [
            400 => 'Bad request',
            401 => 'Unauthorized',
            403 => 'Forbidden',
            404 => 'Not found',
            500 => 'Internal server error',
            502 => 'Bad gateway',
            503 => 'Service unavailable',
        ];

        $response = json_decode($out, true);

        try {
            if ($code < 200 || $code > 204) {
                throw new Exception(isset($errors[$code]) ? $errors[$code] : 'Undefined error', $code);
            }
        } catch (\Exception $e) {
            $this->modx->log(1, print_r(array(
                '[AmoCrm] Ошибка запроса',
                $e->getMessage() . ' Код ошибки: ' . $e->getCode(),
                $link,
                $data,
                $response
            ), 1));

            return false;
        }
        return $response;
    }


    private function loadWebhook()
    {
        if (!$this->webhook and $this->modx->loadClass(
                'amocrm.amoCRMWebhook',
                $this->config['modelPath'],
                false,
                true)) {
            $this->webhook = new amoCRMWebhook($this->amoCRM);
        }
        return $this->webhook;
    }


    public function getWebhook()
    {
        if (!$this->webhook) {
            $this->loadWebhook();
        }
        return $this->webhook;
    }


    /**
     * @return bool|minishop2
     */
    public function getMS2()
    {
        if (!$this->ms2) {
            if (!($this->ms2 = $this->modx->getService('minishop2'))) {
                return false;
            }
        }
        return $this->ms2;
    }


    /**
     * @return bool|simpleQueue
     */
    public function getSQ()
    {
        if (!$this->sq) {
            if (!$this->sq = $this->modx->getService('simplequeue', 'simpleQueue',
                $this->modx->getOption('simplequeue_core_path', null,
                    $this->modx->getOption('core_path') . 'components/simplequeue/') . 'model/simplequeue/', array())
            ) {
                return false;
            }
        }
        return $this->sq;
    }


    public function checkSQ($canHold = false)
    {
        return $canHold and $this->config['useSimpleQueue'] and $this->getSQ();
    }


    public function addSQTask($action, $subject = '', $properties = '', $options = array())
    {

        if (is_array($subject)) {
            $subject = $this->modx->toJSON($subject);
        }
        if (is_array($properties)) {
            $properties = $this->modx->toJSON($properties);
        }

        $data = array_merge(
            array(
                'service' => self::SQ_SERVICE,
                'action' => $action,
                'subject' => $subject,
                'properties' => $properties
            ),
            $options
        );

        $key = md5($this->modx->toJSON($data));
        if (!empty($this->addedSQTasks[$key])) {
            return true;
        }

        $response = $this->modx->runProcessor(
            'message/create',
            $data,
            array('processors_path' => $this->sq->config['processorsPath'])
        );

        $this->addedSQTasks[$key] = true;

        return $response->response['success'];
    }


    /**
     * Shorthand for original modX::invokeEvent() method with some useful additions.
     * Original in miniShop2
     * https://github.com/Ibochkarev/miniShop2/blob/master/core/components/minishop2/model/minishop2/minishop2.class.php
     *
     * Regards to @bezumkin
     *
     * @param $eventName
     * @param array $params
     * @param $glue
     *
     * @return array
     */
    public function invokeEvent($eventName, array $params = array(), $glue = '<br/>')
    {
        if (isset($this->modx->event->returnedValues)) {
            $this->modx->event->returnedValues = null;
        }

        $response = $this->modx->invokeEvent($eventName, $params);
        if (is_array($response) && count($response) > 1) {
            foreach ($response as $k => $v) {
                if (empty($v)) {
                    unset($response[$k]);
                }
            }
        }

        $message = is_array($response) ? implode($glue, $response) : trim((string)$response);
        if (isset($this->modx->event->returnedValues) && is_array($this->modx->event->returnedValues)) {
            $params = array_merge($params, $this->modx->event->returnedValues);
        }

        return array(
            'success' => empty($message),
            'message' => $message,
            'data' => $params,
        );
    }


    /**
     * Запуск процессоров, краткая запись
     *
     * @param string $action
     * @param array $data
     *
     * @return mixed
     */
    public function runProcessor($action = '', $data = array())
    {

        $response = $this->modx->runProcessor($action, $data);
        if ($response->isError()) {
            $error = $response->getMessage();
            if (empty($error)) {
                $error = print_r($response->response['errors'], 1);
            }
            $this->modx->log(modX::LOG_LEVEL_ERROR, 'amoCRM runProcessor. Action: ' . $action . ', Error: ' . $error);
        }

        return $response->response;
    }


    /**
     * Краткая запись для логов
     *
     * @param $message
     * @param int $level
     * @param string $def
     * @param string $file
     * @param string $line
     * @param string $target
     */
    public function log($message, $level = modX::LOG_LEVEL_ERROR, $def = '', $file = '', $line = '', $target = '')
    {
        if ($this->config['spam_to_modx_log']) {
            $this->modx->log($level, $message, $target, $def, $file, $line);
        }
    }

}
