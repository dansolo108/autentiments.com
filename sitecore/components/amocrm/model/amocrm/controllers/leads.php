<?php

require_once 'contacts.php';

class Leads
{
    /** @var modX $modx */
    public $modx;
    /** @var amoCRM $amoCRM */
    public $amoCRM;
    /** @var amoCRMTools $tools */
    public $tools;

    public $contactsController;

    public function __construct($modx, $amo)
    {
        $this->modx = $modx;
        $this->amoCRM = $amo;
        $this->tools = $amo->tools;
        $this->contactsController = new Contacts($this->modx, $amo);
    }

    public function getLeads($ids = array(), $query = array())
    {
        $leads = array();
        if (is_numeric($ids)) {
            $ids = array($ids);
        }
        $data = array('id' => $ids, 'query' => $query);
        if ($result = $this->tools->sendRequest('/api/v4/leads', $data, 'GET')) {
            foreach ($result['_embedded']['leads'] as $lead) {
                $leads[$lead['id']] = $lead;
            }
        }
        return $leads;
    }

    /**
     * Убедимся что все передаваемые кастомные поля в наличии в CRM
     * Отфильтруем пустые поля
     * ПОдготовим окончательный массив передаваемых полей
     *
     * @param $data
     *
     * @return array
     */
    public function prepareOrder($data)
    {
        $order = array();
        $default_fields = $this->amoCRM->config['defaultOrderFields'];
        //Убедился в наличии кастомных полей. Дополнительные данные точно сохранятся в CRM
        $this->amoCRM->checkOrdersCustomFields(array_keys($data), $this->amoCRM->config['autoCreateOrdersCustomFields']);

        foreach ($data as $key => $value) {
            if ($this->amoCRM->config['skipEmptyFields'] and empty($value)) {
                continue;
            }
            if (in_array($key, $default_fields)) {
                $order[$key] = $value;
            } else {
                if ($custom_field_id = $this->amoCRM->getOrdersCustomFieldId($key)) {
                    $custom_field = array(
                        'field_id' => $custom_field_id,
                        'values' => $this->amoCRM->prepareOrderCustomFieldValue($value),
                    );
                    $order['custom_fields_values'][] = $custom_field;
                } else {
                    $order[$key] = $value;
                }
            }
        }
        return $order;
    }

    /**
     * Добавление сделки в CRM
     * Или ее обновление.
     * @param array $data
     *
     * @return string|null
     */
    //Зачем в один метод засунули создание и обновление - загадка
    public function addLead($data = array())
    {
        if (empty($data['responsible_user_id'])) {
            $data['responsible_user_id'] = (int)$this->amoCRM->config['defaultResponsibleUserId'];
        }

        //Обновление заказа
        if (isset($data['order_id'])) {
            $data['id'] = (int)$data['order_id'];
            $data['updated_at'] = time();
            $params = [];
            $params[] = $data;
            $result = $this->tools->sendRequest('/api/v4/leads', $params, 'PATCH');
            if ($result) {
                return $result['_embedded']['leads'][0];
            }
            return null;
        }

        //Новый заказ
        $params = [];
        $params[] = $data;
        $result = $this->tools->sendRequest('/api/v4/leads', $params, 'POST');
        if ($result) {
            return $result['_embedded']['leads'][0];
        }
        return null;
    }

    /**
     * Добавление информации о заказе.
     *
     * @param msOrder $order
     * @param bool $canHold
     *
     * @return bool|int
     */
    public function addOrder(msOrder $order, $canHold = false)
    {
        //Проверка на наличие очередей.  Если разрешены очереди процесс прерывается записью в очередь
        if ($this->tools->checkSQ($canHold)) {
            if ($this->tools->addSQTask(amoCRMTools::SQ_ACTION_ADD_ORDER, $order->get('id'))) {
                return true;
            }
        }

        $goods = array();
        $i = 1;

        //Запрос товаров заказа + артикул
        $q = $this->modx->newQuery('msOrderProduct');
        $q->leftJoin('msProductData', 'msProductData', 'msProductData.id = msOrderProduct.product_id');
        $q->where(['order_id' => $order->get('id')]);
        $q->select(['msOrderProduct.*']);
        $q->select(['msProductData.article']);
        $q->prepare();
        $q->stmt->execute();
        $products = $q->stmt->fetchAll(PDO::FETCH_ASSOC);

        foreach ($products as $product) {
            //COMMENT:  Почему здесь использован лексикон?
            $goods[] = $this->modx->lexicon('amocrm_order_product_row', array(
                'idx' => $i,
                'article' => $product['article'],
                'name' => $product['name'],
                'price' => $product['price'],
                'count' => $product['count'],
                'cost' => $product['cost'],
                'options' => $this->tools->multiImplode('; ', json_decode($product['options'], true)),
                'currency' => $this->modx->lexicon('ms2_frontend_currency'),
                'unit' => $this->modx->lexicon('ms2_frontend_count_unit'),
            ));
            $i++;
        }

        $profileAdditions = array();
        //Базовые поля заказа
        $fields = array(
            'name' => $this->modx->lexicon('amocrm_order_name', array('num' => $order->get('num'))),
            'price' => $order->get('cost'),
            'pipeline_id' => $this->amoCRM->config['pipeline'],  //amocrm_pipeline_id
            'date_create' => strtotime($order->get('createdon')),
        );


        //Проверяю запись о сделке по данному заказу
        //Если такая сделка уже есть - выбираю данные для последующей работы
        $link = $this->modx->getObject('amoCRMLead', array('order' => $order->get('id')));

        if ($link) {
            $fields['order_id'] = (int)$link->get('order_id');
            $fields['pipeline_id'] = $link->get('pipeline_id');
        }

        //Проверяем свойство properties в msOrder. По умолчанию оно пустое.  В каком месте записывается и зачем пока не понятно
        //Если такое свойство заполнено данными заказа, оно перезаписывает поля заказа.
        //В новом заказе не участвует
        $propElem = $this->amoCRM->config['orderPropertiesElement'];  //Название элемента в свойствах заказа -  amoCRMFields
        $orderFieldsValues = $order->toArray();
        if (isset($orderFieldsValues['properties'][$propElem]) and is_array($orderFieldsValues['properties'][$propElem])) {
            $orderFieldsValues = array_replace($orderFieldsValues, $orderFieldsValues['properties'][$propElem]);
            if (isset($orderFieldsValues['properties'][$propElem]['contactFields']) and is_array($orderFieldsValues['properties'][$propElem]['contactFields'])) {
                $profileAdditions = $orderFieldsValues['properties'][$propElem]['contactFields'];
            }
            unset($orderFieldsValues['properties'][$propElem]);
        }

        //В массив данных по заказу добавляем разрешенные дополнительные поля из properties заказа.
        //В новом заказе не участвует
        foreach ($this->amoCRM->config['defaultOrderFields'] as $field) {
            if (isset($orderFieldsValues[$field]) and !empty($orderFieldsValues[$field])) {
                $fields[$field] = $orderFieldsValues[$field];
            }
        }


        //Проверяю индивидуальные настройки для конкретных категорий товара.  По умолчанию вернет пустой массив
        $fieldsByCategory = $this->amoCRM->findCategoryPipeline($order);
        if (!$this->amoCRM->config['responsible_id_priority_category']) {
            unset($fieldsByCategory['responsible_user_id']);
        }

        $fields = array_merge($fields, $fieldsByCategory);

        //Добавляю в miniShop2 новые статусы заказа из указанной воронки
        //Делается один раз при первом подключении
        //Логика так себе. Подобные вещи нужно делать из отдельного административного интерфейса.

        $addMiniShopPipeline_result = $this->amoCRM->addMiniShopPipeline($fields['pipeline_id']);

        if (!$addMiniShopPipeline_result) {
            $this->amoCRM->log('Error adding MS2 pipeline to AmoCRM');
            return false;
        }

        //Получаю статус для новой сделки в указанной воронке.  По умолчанию amocrm_new_order_status_id
        $amoStatus = $this->amoCRM->getLeadStatusId($order->get('status'), $fields['pipeline_id']);
        if ($amoStatus) {
            $fields['status_id'] = $amoStatus;
        }

        if ($payment = $order->getOne('Payment')) {
            $paymentName = $payment->get('name');
        } else {
            $paymentName = '';
        }

        if ($delivery = $order->getOne('Delivery')) {
            $deliveryName = $delivery->get('name');
        } else {
            $deliveryName = '';
        }

        /* @TODO Продумать механизм добавления дополнительных полей без жесткого их указания в коде */
        $customFields = array(
            'goods' => implode("\n", $goods),
            'payment' => $paymentName,
            'delivery' => $deliveryName,
        );
        $customOrderFields = array();
        foreach ($this->amoCRM->tools->parseFieldsSet($this->amoCRM->config['orderFields']) as $k) {
            $v = trim($orderFieldsValues[$k]);
            if ($v) {
                $customOrderFields[$k] = $v;
            }
        }
        if ($address = $order->getOne('Address')) {
            $prefix = $this->amoCRM->config['orderAddressFieldsPrefix'];
            foreach ($this->tools->parseFieldsSet($this->amoCRM->config['orderAddressFields']) as $k) {
                $v = trim($address->get($k));
                if ($v) {
                    $customFields[$prefix . $k] = $v;
                }
            }
        }
        $customFields = array_replace($customOrderFields, $customFields);

        $data = $this->prepareOrder(array_replace($fields, $customFields));

        //Ищу и добавляю контакт к сделке
        $contact = $this->contactsController->searchContact($address->get('phone'));
        if (!empty($contact)) {
            $data['_embedded']['contacts'][0]['id'] = $contact['id'];
        } else {
            //Если контакт не найден создаем принудительно
            $contact = $this->contactsController->addContact($order->User->Profile->toArray());
            if (!empty($contact)) {
                if (is_array($contact)) {
                    $data['_embedded']['contacts'][0]['id'] = $contact['id'];
                }

                if (is_numeric($contact)) {
                    $data['_embedded']['contacts'][0]['id'] = $contact;
                }
            }
        }

        //Запускам событие обработки данных перед отправкой заказа
        $response = $this->tools->invokeEvent('amocrmOnBeforeOrderSend', array(
            'lead' => $data,
            'msOrder' => $order,
            'msOrderId' => $order->get('id'),
            'amoCRM' => $this,
        ));

        //Поддержка старого кода
        if (isset($response['data']['lead']['custom_fields'])) {
            $custom_fields_values = $response['data']['lead']['custom_fields'];
        }

        if (isset($response['data']['lead']['custom_fields_values'])) {
            $custom_fields_values = $response['data']['lead']['custom_fields_values'];
        }


        if (isset($data['custom_fields_values']) && isset($custom_fields_values)) {
            foreach ($response['data']['lead']['custom_fields_values'] as $custom_field) {
                $data['custom_fields_values'][] = $custom_field;
            }
            unset($response['data']['lead']['custom_fields_values']);
        }

        $data = array_replace($data, $response['data']['lead']);

        //Отправляю заказ в CRM
        $leadData = $this->addLead($data);
        if (!empty($leadData['id'])
            and ($lead = $this->modx->getObject('amoCRMLead', array('order' => $order->get('id')))
                or $lead = $this->modx->newObject('amoCRMLead')
            )
        ) {
            $lead->set('order', $order->get('id'));
            $lead->set('order_id', $leadData['id']);
            $lead->set('pipeline_id', $data['pipeline_id']);
            $lead->save();
        }

        $this->tools->invokeEvent('amocrmOnOrderSend', array(
            'lead' => $leadData,
            'amoCRMLead' => $lead,
            'amoCRM' => $this,
            'amoCRMResponse' => $leadData,
        ));

        if ($address and $address->get('phone')) {
            if (empty($profileAdditions['phone']) and empty($profileAdditions['phone'])) {
                $profileAdditions['phone'] = $address->get('phone');
                $profileAdditions['телефон'] = $address->get('phone');
            }
        }

        foreach ($this->tools->parseFieldsSet($this->amoCRM->config['userFields']) as $field) {
            if (empty($profileAdditions[$field]) and !empty($data[$field])) {
                $profileAdditions[$field] = $data[$field];
            }
        }
        $contactData = $this->contactsController->prepareProfile($order->get('user_id'), $profileAdditions);
        $this->contactsController->addContact($contactData, $order->get('user_id'), array($leadData['id']));

        return $leadData['id'];
    }

    public function changeOrderStatus($data)
    {
        $link = '/api/v4/leads';
        $data = [$data];
        $result = $this->tools->sendRequest($link, $data, 'PATCH');
        return $result;
    }

    public function changeOrderStatusInAmo($ms2OrderId, $ms2StatusId, $canHold = false)
    {
        if (defined('AMOCRM_WEBHOOK_MODE')
            or ($ms2StatusId == $this->amoCRM->config['msStatusNewOrder'] and !$this->amoCRM->config['updateOrderOnChangeStatus'])
        ) {
            return array();
        }

        if ($this->tools->checkSQ($canHold)) {
            $data = array(
                'ms2OrderId' => $ms2OrderId,
                'ms2StatusId' => $ms2StatusId,
            );
            if ($this->tools->addSQTask(amoCRMTools::SQ_ACTION_CHANGE_ORDER_STATUS, $ms2OrderId, $data)) {
                return true;
            }
        }

        $result = array();
        if ($lead = $this->modx->getObject('amoCRMLead', array('order' => $ms2OrderId)) and $lead->get('order_id')) {
            $amoLeads = $this->getLeads($lead->get('order_id'));
            $amoLead = array_shift($amoLeads);
            if (!empty($amoLead['pipeline_id'])) {
                if ($lead->get('pipeline_id') != $amoLead['pipeline_id']) {
                    $lead->set('pipeline_id', $amoLead['pipeline_id']);
                    $lead->save();
                }

                $status = $this->modx->getObject(
                    'amoCRMOrderStatus',
                    array('status' => $ms2StatusId, 'pipeline_id' => $amoLead['pipeline_id'])
                );

                if ($status) {
                    $data['id'] = $lead->get('order_id');
                    $data['last_modified'] = $lead->get('updatedon');

                    if (!$data['last_modified']) {
                        $data['last_modified'] = time();
                    }

                    $data['status_id'] = $status->get('status_id');
                    $data['pipeline_id'] = $status->get('pipeline_id');

                    $result = $this->changeOrderStatus($data);
                } else {
                    $this->modx->log(xPDO::LOG_LEVEL_ERROR,
                        'Status link for MS2 status '
                        . $ms2StatusId . ' and amoCRM pipeline id ' . $amoLead['pipeline']['id']
                        . ' not found. Status in amoCRM for order ' . $ms2OrderId . ' hasn\'t changed.');
                    return array('success' => true);
                }
            } else {
                return array('success' => true);
            }
        } else {
            return array('success' => true);
        }

        return $result;
    }

    public function changeOrderStatusInMS2($order, $status)
    {
        if (!$this->tools->getMS2()) {
            return false;
        }

        $order = $this->modx->getObject('amoCRMLead', array('order_id' => $order));
        $status = $this->modx->getObject('amoCRMOrderStatus', array('status_id' => $status));

        if ($order && $status) {
            /** @var msOrder $msOrder */
            $msOrder = $order->getOne('Order');

            if ($msOrder) {
                if (!empty($msOrder->get('status'))) {
                    return $this->tools->ms2->changeOrderStatus($msOrder->get('id'), $status->get('status'));
                }
            }
            // Если соответствующий сделке заказ не найден, ничего не делаем и не возвращаем ошибку
            return true;

        } else {
            return true;
        }
    }
}
