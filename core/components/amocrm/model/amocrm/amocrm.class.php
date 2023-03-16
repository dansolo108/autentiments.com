<?php

require_once 'amocrmtools.class.php';
require_once dirname(__FILE__) . '/controllers/contacts.php';
require_once dirname(__FILE__) . '/controllers/leads.php';

class amoCRM
{
    const AMO_ELEMENT_TYPE_CONTACT = 1;
    const AMO_ELEMENT_TYPE_ORDER = 2;
    const AMO_ELEMENT_TYPE_COMPANY = 3;

    const AMO_FIELD_TYPE_TEXT = 1;
    const AMO_FIELD_TYPE_NUMERIC = 2;
    const AMO_FIELD_TYPE_CHECKBOX = 3;
    const AMO_FIELD_TYPE_SELECT = 4;
    const AMO_FIELD_TYPE_MULTISELECT = 5;
    const AMO_FIELD_TYPE_DATE = 6;
    const AMO_FIELD_TYPE_URL = 7;
    const AMO_FIELD_TYPE_MULTITEXT = 8;
    const AMO_FIELD_TYPE_TEXTAREA = 9;
    const AMO_FIELD_TYPE_RADIOBUTTON = 10;
    const AMO_FIELD_TYPE_STREETADDRESS = 11;
    const AMO_FIELD_TYPE_SMART_ADDRESS = 13;
    const AMO_FIELD_TYPE_BIRTHDAY = 14;

    /** @var modX $modx */
    public $modx;
    /** @var amoCRMTools $tools */
    public $tools;
    /** @var array $config */
    public $config;
    /** @var boolean $debug */
    public $debug = false;
    public $account_config = [];
    public $customFields = [];
    private $webhookMode = false;
    private $customFieldsSymbolTypes = [
        self::AMO_ELEMENT_TYPE_CONTACT => 'contacts',
        self::AMO_ELEMENT_TYPE_ORDER => 'leads',
        self::AMO_ELEMENT_TYPE_COMPANY => 'companies',
    ];

    public $contactsController;
    /**
     * @var Leads $leadsController
     */
    public $leadsController;

    /**
     * @param modX $modx
     * @param array $config
     */
    function __construct($modx, $config = [])
    {
        $this->modx = $modx;

        $corePath = $this->modx->getOption(
            'amocrm_core_path',
            $config,
            $this->modx->getOption('core_path') . 'components/amocrm/'
        );
        $assetsUrl = $this->modx->getOption(
            'amocrm_assets_url',
            $config,
            $this->modx->getOption('assets_url') . 'components/amocrm/'
        );
        $connectorUrl = $assetsUrl . 'connector.php';

        $this->config = array_merge([
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
            'protocol' => $this->getSetting('amocrm_protocol', 'https'),
            /** @TODO Сделать проверку указания полного домена с/без протокола. Лишнее вырезать. */
            'account' => $this->getSetting('amocrm_account'),
            'pipeline' => (int)$this->getSetting('amocrm_pipeline_id'),
            'categories_pipelines' => $this->getSetting('amocrm_categories_pipelines'),
            'responsible_id_priority_category' => $this->getSetting('amocrm_responsible_id_priority_category'),
            'form_pipeline' => (int)$this->getSetting('amocrm_form_pipeline_id'),
            'form_as_lead' => $this->getSetting('amocrm_form_as_lead'),
            'form_filled_fields' => $this->getSetting('amocrm_form_filled_fields'),
            'form_status_new' => (int)$this->getSetting('amocrm_form_status_new'),
            'statusNewOrder' => (int)$this->getSetting('amocrm_new_order_status_id'),
            'msStatusNewOrder' => 1,
            'spam_to_modx_log' => $this->getSetting('amocrm_spam_modx_log', false),
            'defaultOrderFields' => [
                'order_id',
                'name',
                'status_id',
                'price',
                'pipeline_id',
                'date_create',
                'responsible_user_id',
                'tags',
                'company_id',
                'visitor_uid',
            ],
            'orderFields' => $this->getSetting('amocrm_order_fields', ''),
            'orderPropertiesElement' => $this->getSetting('amocrm_order_properties_element', 'amoCRMFields'),
            'orderAddressFields' => $this->getSetting('amocrm_order_address_fields', ''),
            'orderAddressFieldsPrefix' => $this->getSetting('amocrm_order_address_fields_prefix', 'address.'),
            'defaultUserFields' => [
                'name',
                'created_at',
                'updated_at',
                'responsible_user_id',
                'tags',
                'company_id',
                'company_name'
            ],
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
            'updateOrderOnChangeStatus' => $this->getSetting('amocrm_update_order_on_change_status', false),
        ], $config);

        $this->modx->addPackage('amocrm', $this->config['modelPath']);
        $this->modx->lexicon->load('amocrm:default');
        $this->loadTools();
    }

    private function loadTools()
    {
        $this->tools = new amoCRMTools($this->modx, $this);
        $this->tools->amoCRM = $this;
        $this->account_config = &$this->tools->account_config;
        $this->contactsController = new Contacts($this->modx, $this);
        $this->leadsController = new Leads($this->modx, $this);
    }

    /**
     * Проверка актуальности токена
     * @return bool
     */
    public function auth()
    {
        $authorized = $this->tools->authController->checkAuth();
        return $authorized;
    }

    /**
     * Получение списка дополнительных полей.
     * По умолчанию в CRM в кастомных полях сделок есть только источники и UTM
     *
     * @return mixed
     */
    public function getCustomFieldsList()
    {
        if (!is_array($this->customFields)) {
            $this->tools->updateAccountConfig();
        }
        $this->customFields = $this->account_config['custom_fields'];
        return $this->customFields;
    }

    /**
     * Метод проверяет наличие дополнительного поля в указанной сущности type по ключу key
     * @param $type
     * @param $key
     * @return mixed|null
     */
    public function getCustomFieldId($type, $key)
    {
        //Получаем список доп полей сущности.
        //  Для lead по умолчанию это UTM, источник и метки аналитики
        // Для contacts по умолчанию это Должность, Телефон, Email
        $fields = $this->getCustomFieldsList();
        if (!is_array($fields[$type])) {
            return null;
        }
        $keyLower = mb_strtolower($key);

        foreach ($fields[$type] as $field) {
            if ($keyLower == mb_strtolower($field['code']) ||
                $keyLower == mb_strtolower($field['name']) ||
                $key == $field['id']
            ) {
                return $field['id'];
            }
        }

        return null;
    }

    /**
     * Получаем статус в amoCRM для указанного статуса miniShop2
     *
     * @param integer $ms2status
     * @param integer $pipeline_id
     *
     * @return mixed|null
     * @internal param $order_status
     */
    public function getLeadStatusId($ms2status, $pipeline_id)
    {
        if (empty($ms2status)) {
            $ms2status = $this->config['statusNewOrder'];
        }
        if (empty($pipeline_id)) {
            $pipeline_id = $this->config['pipeline'];
        }

        $status = $this->modx->getObject('amoCRMOrderStatus', [
            'status' => $ms2status,
            'pipeline_id' => $pipeline_id,
        ]);
        if ($status) {
            return $status->get('status_id');
        } else {
            return $this->config['statusNewOrder'];
        }
    }

    /**
     * Добавление информации о заказе.
     *
     * @param array $data
     * @param bool $canHold
     *
     * @return bool|int
     * @internal param msOrder $order
     */
    public function addForm($data = [], $canHold = false)
    {
        foreach (explode(',', $this->config['form_filled_fields']) as $reqField) {
            if (isset($data[$reqField]) and empty($data[$reqField])) {
                return false;
            }
        }

        if (!$this->config['form_pipeline']) {
            if (!$this->addFormPipeline()) {
                $this->log('Error adding form pipeline to AmoCRM');
                return false;
            }
        }

        if ($this->tools->checkSQ($canHold)) {
            if ($this->tools->addSQTask(amoCRMTools::SQ_ACTION_ADD_FORM, '', $data)) {
                return true;
            }
        }

        $amoLead = ['id' => 0];

        $this->contactsController->addContact($data);

        if ($this->config['form_as_lead']) {
            $fields = [
                'name' => isset($data['name']) ? $data['name'] : $this->modx->lexicon(
                    'amocrm_form_name_new',
                    ['date' => date('Y-m-d H:i:s')]
                ),
                'price' => isset($data['price']) ? $data['price'] : ((isset($data['name']) and is_numeric(
                        $data['name']
                    )) ? $data['name'] : 0),
                'pipeline_id' => $this->config['form_pipeline'],
                'status_id' => $this->config['form_status_new'],
                'date_create' => time(),
            ];
            $leadData = $this->leadsController->prepareOrder(array_replace($data, $fields));

            $response = $this->tools->invokeEvent('amocrmOnBeforeOrderSend', [
                'lead' => $leadData,
                'msOrder' => null,
                'msOrderId' => 0,
                'amoCRM' => $this,
            ]);
            if (isset($leadData['custom_fields']) && isset($response['data']['lead']['custom_fields'])) {
                foreach ($response['data']['lead']['custom_fields'] as $custom_field) {
                    $leadData['custom_fields'][] = $custom_field;
                }
                unset($response['data']['lead']['custom_fields']);
            }

            //Ищу и добавляю контакт к сделке
            $contact = $this->contactsController->searchContact($data['phone']);

            if (!empty($contact)) {
                $leadData['_embedded']['contacts'][0]['id'] = $contact['id'];
            }

            $leadData = array_merge($leadData, $response['data']['lead']);

            $amoLead = $this->leadsController->addLead($leadData);
            if ($lead = $this->modx->newObject('amoCRMLead')) {
                $lead->set('order', 0);
                $lead->set('order_id', $amoLead['id']);
                $lead->set('pipeline_id', $leadData['pipeline_id']);
                $lead->save();
            }

            $this->tools->invokeEvent('amocrmOnOrderSend', [
                'lead' => $leadData,
                'amoCRMLead' => $lead,
                'amoCRM' => $this,
                'amoCRMResponse' => $amoLead,
            ]);
        }

        return $this->config['form_as_lead'] ? $amoLead['id'] : true;
    }

    /**
     * Получение списка воронок
     *
     * @param int $id
     *
     * @return array
     */
    public function getPipelines($id = 0)
    {
        $request = $this->tools->sendRequest('/api/v4/leads/pipelines', [], 'GET');
        if ($request) {
            $pipelines = $request['_embedded']['pipelines'];
            $result = !empty($id) ? [$id => $pipelines[$id]] : $pipelines;
            return $result;
        } else {
            return null;
        }
    }

    /**
     * Получение статусов воронки продаж.
     *
     * @param null $pipeline_id
     *
     * @return mixed
     */
    public function getStatuses($pipeline_id = null)
    {
        if (!$pipeline_id) {
            $pipeline_id = $this->config['pipeline'];
        }
        if ($pipelines = $this->getPipelines($pipeline_id)) {
            return !empty($pipelines[$pipeline_id]['statuses']) ? $pipelines[$pipeline_id]['statuses'] : [];
        } else {
            return null;
        }
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
        if ($this->tools) {
            return $this->tools->getSetting($key, $default, $parse);
        }
        return $this->modx->getOption($key, null, $default);
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
        $this->tools->log($message, $level, $def, $file, $line, $target);
    }

    /**
     * Получение списка статусов miniShop2
     *
     * @return array
     */
    public function getMiniShopStatuses()
    {
        $result = [];
        /** @var xPDOObject $statuses */
        $statuses = $this->modx->getIterator('msOrderStatus', ['active' => 1]);
        $statuses->rewind();
        if ($statuses->valid()) {
            foreach ($statuses as $status) {
                $result[] = $status->toArray();
            }
        }

        return $result;
    }

    /**
     * @param array $data
     *
     * @return array
     */
    public function addUpdatePipeline(array $data = [])
    {
        if (empty($data['id'])) {
            $method = 'POST';
            $link = '/api/v4/leads/pipelines';
            $request = $this->tools->sendRequest($link, $data, $method);
        } else {
            $method = 'PATCH';
            $link = '/api/v4/leads/pipelines/' . $data['id'];
            $request = $this->tools->sendRequest($link, $data, $method);
        }

        return $request;
    }

    public function eqAmoMiniShopStatuses($pipeline_id, $ms2Statuses = [])
    {
        $i = 0;
        $result = [];
        if (empty($ms2Statuses)) {
            $ms2Statuses = $this->prepareMiniShopStatuses([], true);
        }
        foreach ($ms2Statuses as $status) {
            $ms2StatusId = $status['id'];
            $amoStatusId = 0;
            if ($obj = $this->modx->getObject(
                'amoCRMOrderStatus',
                ['pipeline_id' => $pipeline_id, 'status' => $status['id']]
            )) {
                $amoStatusId = $obj->get('status_id');
                $status['id'] = $amoStatusId;
            } else {
                unset($status['id']);
                $amoStatuses = &$this->account_config['pipelines'][$pipeline_id]['statuses'];
                foreach ($amoStatuses as $amoStatus) {
                    if ($status['name'] == $amoStatus['name']) {
                        $amoStatusId = $amoStatus['id'];
                        $status['id'] = $amoStatusId;
                        if ($obj = $this->modx->newObject('amoCRMOrderStatus')) {
                            $obj->set('pipeline_id', $pipeline_id);
                            $obj->set('status', $ms2StatusId);
                            $obj->set('status_id', $amoStatusId);
                            $obj->save();
                        };
                        break;
                    }
                }
            }
            $status['pipeline_id'] = $pipeline_id ?: $this->config['pipeline'];
            if (empty($status['pipeline_id'])) {
                unset($status['pipeline_id']);
            }
            if ($amoStatusId) {
                $result[$amoStatusId] = $status;
            } else {
                $result[] = $status;
            }
            $i++;
        }
        return $result;
    }

    /**
     * Готовлю короткий справочник статусов miniShop2
     * @param array $data
     * @param bool $idsOriginal
     * @return array
     */
    public function prepareMiniShopStatuses(array $data = [], $idsOriginal = false)
    {
        $statuses = [];
        if (empty($data)) {
            $data = $this->getMiniShopStatuses();
        }
        foreach ($data as $status) {
            $s = [
                'name' => $status['name'],
                'color' => '#' . $status['color'],
                'sort' => $status['rank'],
            ];
            if ($idsOriginal) {
                $s['id'] = $status['id'];
            }
            $statuses[] = $s;
        }

        return $statuses;
    }

    /**
     * Добавление воронки для форм заявок
     *
     * @return bool|array
     */
    public function addFormPipeline()
    {
        if (empty($this->config['form_pipeline'])) {
            return true;
        }
        $data = [
            'name' => $this->getSetting('site_name') . ' Forms',
            'statuses' => [['name' => $this->modx->lexicon('amocrm_form_status_new')]],
        ];

        $result = $this->addUpdatePipeline($data);
        $pipelines = $result['out']['pipelines']['add']['pipelines'];
        foreach ($pipelines as $pipeline_id => $pipeline) {
            if ($pidSetting = $this->modx->getObject('modSystemSetting', 'amocrm_form_pipeline_id')) {
                $pidSetting->set('value', $pipeline_id);
                $pidSetting->save();
                $this->modx->cacheManager->refresh([
                    'system_settings' => ['key' => 'amocrm_form_pipeline_id']
                ]);
                $this->config['form_pipeline'] = $pipeline_id;
            }

            foreach ($pipeline['statuses'] as $status_id => $status) {
                if ($status['name'] == $this->modx->lexicon('amocrm_form_status_new')) {
                    if ($pidSetting = $this->modx->getObject('modSystemSetting', 'amocrm_form_status_new')) {
                        $pidSetting->set('value', $status_id);
                        $pidSetting->save();
                        $this->modx->cacheManager->refresh([
                            'system_settings' => ['key' => 'amocrm_form_status_new']
                        ]);
                        $this->config['form_status_new'] = $status_id;
                    }
                }
            }
        }

        return $result;
    }

    /**
     * Метод при первом создании заказа (или при принудительном флаге) добавляет статусы заказа из указанной воронки в miniShop2     *
     *
     * @param int $pipelineId
     *
     * @return bool
     */
    public function addMiniShopPipeline($pipelineId = 0)
    {
        //Автоматически обновлять воронки и статусы. По умолчанию отключено.
        $autoUpdate = $this->config['autoUpdatePipelines'];

        if (empty($pipelineId) and $this->config['pipeline'] and !$autoUpdate) {
            return true;
        }

        //Если не указан $pipelineId беру из системной настройки
        $pipelineId = $pipelineId ?: $this->config['pipeline'];

        if ($autoUpdate or empty($this->account_config['pipelines'][$pipelineId])) {
            $data = [
//                'id' => $pipelineId,
                'name' => $this->getSetting('site_name'),
                'statuses' => $this->eqAmoMiniShopStatuses($pipelineId),
            ];
            if ($pipelineId) {
                $data['id'] = $pipelineId;
            }
            if (!empty($pipelineId)) {
                $data['name'] = $this->account_config['pipelines'][$pipelineId]['name'];
            }

            $result = $this->addUpdatePipeline($data);

            if (empty($pipelineId)) {
                $pipelines = $result['_embedded']['pipelines'];
                $pipelinesIds = array_keys($pipelines);
                $pipelineId = array_shift($pipelinesIds);
            }

            if (empty($this->config['pipeline'])) {
                $this->tools->setSetting('amocrm_pipeline_id', $pipelineId);
                $this->config['pipeline'] = $pipelineId;
            }

            $statuses = $this->getStatuses($pipelineId);
            foreach ($statuses as $status_id => $status) {
                if (!$msStatus = $this->modx->getObject('msOrderStatus', ['name' => $status['name']])) {
                    $msStatus = $this->modx->newObject('msOrderStatus', ['name' => $status['name']]);
                    $msStatus->save();
                }
                if (!$this->modx->getCount(
                        'amoCRMOrderStatus',
                        ['pipeline_id' => $pipelineId, 'status_id' => $status_id]
                    )
                    and $obj = $this->modx->newObject('amoCRMOrderStatus')) {
                    $obj->set('pipeline_id', $pipelineId);
                    $obj->set('status_id', $status_id);
                    $obj->set('status', $msStatus->get('id'));
                    $obj->save();
                }
            }
        }

        return true;
    }

    /**
     * Назначение для отдельных категорий товара собственных настроек (Воронка, Статус, ответственный).
     * Настраивается системной настройкой amocrm_categories_pipelines
     * По умолчанию не заполняется и для простой отправки заказов по единым данным не нужно
     * @param msOrder $order
     * @return array|mixed
     */
    public function findCategoryPipeline(msOrder $order)
    {
        $result = [];
        if (!empty($this->config['categories_pipelines'])) {
            /** @var msOrderProduct $product */
            foreach ($order->getMany('Products') as $product) {
                $options = [];
                if ($resource = $product->getOne('Product')) {
                    $options['context'] = $resource->get('context_key');
                }
                $parents = $this->modx->getParentIds($product->get('product_id'), 10, $options);
                foreach ($this->modx->fromJSON($this->config['categories_pipelines']) as $ctgr => $ppln) {
                    if (in_array($ctgr, $parents)) {
                        $result = $ppln;
                        break;
                    }
                }
            }
        }
        return $result;
    }

    /**
     * Ярлык для метода parseFieldsSet контроллера tools
     * @param array $fieldsSet
     * @return array|string
     */
    public function parseFieldsSet($fieldsSet = [])
    {
        $result = $this->tools->parseFieldsSet($fieldsSet);
        return $result;
    }

    /**
     * Метод получает на входе список отправляемых полей
     * Проверяет и если нужно создает дополнительные поля заказа в AMO
     * @param array $fields
     * @param bool $needCreate
     * @return bool
     */
    public function checkOrdersCustomFields($fields = [], $needCreate = false)
    {
        $orderFields = $this->tools->parseFieldsSet($this->config['orderFields']);
        $addressFields = $this->tools->parseFieldsSet($this->config['orderAddressFields']);
        foreach ($addressFields as $addressField) {
            $orderFields[] = 'address.' . $addressField;
        }
        $newFields = [];
        $result = true;
        //Проверяю список передающих полей.
        //Если поле отсутствует в списке доп полей и при этом есть в списке требуемых для передачи создаю массив для создания новых полей в AMO
        //Если указан флаг запрещающий создание новый полей - прерываю процесс.  По умолчанию разрешено
        foreach ($fields as $field) {
            if (!$this->getOrdersCustomFieldId($field)) {
                if (in_array($field, $orderFields)) {
                    $newFields[] = $field;
                }
                if (!$needCreate) {
                    $result = false;
                }
            }
        }

        //Запускаю процесс создания дополнительных полей
        if ($needCreate) {
            $this->addLeadsCustomFields($newFields);
            $result = true;
        }
        return $result;
    }

    /**
     * Метод запрашивает наличие дополнительного поля в AMO CRM по ключу
     * @param $key
     * @return mixed|null
     */
    public function getOrdersCustomFieldId($key)
    {
        return $this->getCustomFieldId('leads', $key);
    }

    public function prepareOrderCustomFieldValue($value)
    {
        $result = [];
        if ($value == '') {
            $value = ' ';
        }
        if (!is_array($value)) {
            $result = [['value' => (string)$value]];
        }

        return $result;
    }

    public function addLeadsCustomFields($names)
    {
        $this->addCustomFields($names, 'leads');
    }

    public function addCustomFields($names, $type)
    {
        if (!is_array($names)) {
            $names = [$names];
        }
        foreach ($names as $name) {
            $this->addCustomField($name, $type);
        }
        $this->tools->updateAccountConfig();
        return;
    }

    //TODO Исправить логику выборки типа сущности
    public function addCustomField($name, $type)
    {
        $result = [];

        foreach ($this->account_config['custom_fields'][$type] as $custom_field) {
            if ($name == $custom_field['name'] or $name == $custom_field['id']) {
                return $custom_field;
            }
        }

        switch ($name) {
            case 'goods':
                $data = [
                    'name' => $name,
                    'type' => 'textarea',
                ];
                break;
            default:
                $data = [
                    'name' => $name,
                    'type' => 'text',
                ];
        }

        $result = $this->tools->sendRequest('/api/v4/' . $type . '/custom_fields', $data);
        $result = $result['_embedded']['custom_fields'][0];

        return $result;
    }

    public function isWebhookMode()
    {
        return $this->webhookMode or (defined('AMOCRM_WEBHOOK_MODE') and AMOCRM_WEBHOOK_MODE);
    }

    public function setWebhookMode($webhookMode)
    {
        $this->webhookMode = $webhookMode;
    }

    /**
     * Ярлык для метода addOrder в контроллере Leads
     * @param msOrder $data
     *
     * @return string|null
     */
    public function addOrder($data, $canHold = false)
    {
        $result = $this->leadsController->addOrder($data, $canHold);
        return $result;
    }

    public function changeOrderStatusInAmo($ms2OrderId, $ms2StatusId, $canHold = false)
    {
        $result = $this->leadsController->changeOrderStatusInAmo($ms2OrderId, $ms2StatusId, $canHold);
        return $result;
    }

    public function addContact($userData = [], $modUserId = 0, $leads = [], $canHold = false)
    {
        $result = $this->contactsController->addContact($userData, $modUserId, $leads, $canHold);
        return $result;
    }
}
