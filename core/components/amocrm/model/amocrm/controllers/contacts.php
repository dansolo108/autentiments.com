<?php

class Contacts
{
    /** @var modX $modx */
    public $modx;
    /** @var amoCRM $amoCRM */
    public $amoCRM;
    /** @var amoCRMTools $tools */
    public $tools;

    public function __construct($modx, $amo)
    {
        $this->modx = $modx;
        $this->amoCRM = $amo;
        $this->tools = $amo->tools;
    }

    /**
     * Создание контакта из хука FormId
     * @param array $data
     * @param int $leadId
     */
    public function addFromForm($data, $leadId)
    {
        //TODO Сделать постановку задачи в очередь

    }

    /**
     * Добавление контакта в amoCRM
     * Точка входа для плагина на OnUserFormSave
     * @param array $userData
     * @param integer $modUserId modUser ID
     * @param array $leads
     * @param bool $canHold
     *
     * @return int
     */
    public function addContact($userData = [], $modUserId = 0, $leads = [], $canHold = false)
    {
        //$userData = $modx->user->profile->toArray()
        //Кладу задачу в очередь если работа очередей предусмотрена.  По умолчанию нет
        if ($this->tools->checkSQ($canHold)) {
            $data = [
                'userData' => $userData,
                'modUserId' => $modUserId,
                'leads' => $leads
            ];
            $subject = !empty($modUserId) ? $modUserId : '';
            if ($this->tools->addSQTask(amoCRMTools::SQ_ACTION_ADD_CONTACT, $subject, $data)) {
                return true;
            }
        }

        $amoUserId = 0;
        $data = [];
        //Собираем данные для отправки
        $contact = $this->prepareContact($userData);
        $profile = null;

        //Если не передан id клиента - ищу его по email или телефону
        if (!$modUserId) {
            $criteria = !empty($userData['email']) ? ['email' => $userData['email']] : ['phone' => $userData['phone']];
            if ($profile = $this->modx->getObject('modUserProfile', $criteria)) {
                $modUserId = $profile->get('internalKey');
            }
        }

        $extended = [];
        if ($modUserId and ($profile or $profile = $this->modx->getObject('modUserProfile', ['internalKey' => $modUserId]))) {
            $extended = $profile->get('extended');
        }
        if (isset($userData['extended'])) {
            $extended = $userData['extended'];
        }

        if (isset($extended[$this->amoCRM->config['orderPropertiesElement']])) {
            $extAmo = &$extended[$this->amoCRM->config['orderPropertiesElement']];
            if (isset($extAmo['custom_fields']) and isset($contact['custom_fields'])) {
                $extAmo['custom_fields'] = array_merge($contact['custom_fields'], $extAmo['custom_fields']);
            }
            $contact = array_replace($contact, $extended[$this->amoCRM->config['orderPropertiesElement']]);
        }

        if ($modUserId and $amoUserId = $this->getUserId($modUserId)) {
            $contact['id'] = $amoUserId;
            $contact['last_modified'] = time();
        } else {
            $queryFieldsBase = ['email', 'phone', 'mobilephone', 'телефон'];
            $userDataLower = [];
            foreach ($userData as $key => $value) {
                $userDataLower[mb_strtolower($key)] = $value;
            }
            foreach ($queryFieldsBase as $field) {
                if (empty($userDataLower[$field])) {
                    continue;
                }
                $query = trim($userDataLower[$field]);
                $query = preg_replace('/[^a-zA-ZА-Яа-я0-9@.\-+]/i', '', $query);
                if (!empty($query)) {
                    $amoContacts = $this->getContacts([], $query);

                    if (!empty($amoContacts)) {
                        $amoContact = array_shift($amoContacts);
                        $amoUserId = $amoContact['id'];
                        break;
                    }
                }
            }
        }


        //Если контакт в базе AMO не найден добавляю новый. Иначе обновляю
        //TODO Разделить на разные методы
        if (empty($amoUserId)) {
            unset($contact['id']);
            //Добавляю новый контакт

            $response = $this->tools->invokeEvent('amocrmOnBeforeUserSend', [
                'contact' => $contact,
                'action' => 'add',
                'modUserId' => $modUserId,
                'amoUserId' => $amoUserId,
                'amoCRM' => $this,
            ]);

            $contact = array_merge($contact, $response['data']['contact']);

            $data = [$contact];

            $result = $this->tools->sendRequest('/api/v4/contacts', $data, 'POST');

            $contactData = $result['_embedded']['contacts'][0];
            if (isset($contactData['id'])) {
                $amoUserId = $contactData['id'];
                if ($modUserId) {
                    $this->setUserId($modUserId, $amoUserId);
                }
            }

            $this->tools->invokeEvent('amocrmOnUserSend', [
                'contact' => $contact,
                'action' => 'add',
                'modUserId' => $modUserId,
                'amoUserID' => $amoUserId,
                'amoCRM' => $this,
                'amoCRMResponse' => $result,
            ]);

            return $amoUserId;
        } else {
            //Обновляю найденный контакт
            $contact['id'] = $amoUserId;
            if (empty($amoContact)) {
                $amoContacts = $this->getContacts([$amoUserId]);
                $amoContact = array_shift($amoContacts);
            }
            $contact['id'] = $amoContact['id'];
            $contact = $this->guardContactROFields($contact, $amoContact);

            if (!empty($amoContact)) {
                $amoLeads = !empty($amoContact['leads']['id']) ? $amoContact['leads']['id'] : [];
            } else {
                $amoTmpLeads = $this->getContactsLeads($contact['id']);
                $amoLeads = array_shift($amoTmpLeads);
            }
            if (is_array($amoLeads)) {
                $leads = array_merge($amoLeads, $leads);
            }

            $contact['leads_id'] = $leads;
            $contact['updated_at'] = time();

            if (empty($contact['responsible_user_id'])) {
                $contact['responsible_user_id'] = $this->amoCRM->config['defaultResponsibleUserId'];
            }

            $response = $this->tools->invokeEvent('amocrmOnBeforeUserSend', [
                'contact' => $contact,
                'action' => 'update',
                'modUserId' => $modUserId,
                'amoUserId' => $amoUserId,
                'amoCRM' => $this,
            ]);

            $contact = array_merge($contact, $response['data']['contact']);
            return $contact;
        }
    }

    /**
     * Готовим данные.  Из профиля пользователя выбираем те данные, что необходимо передать в AMO согласно конфигурации
     * @param $data
     *
     * @return array
     */
    private function prepareContact($data)
    {
        $contact = [];
        //$data = $user->profile->toArray() ||
        if (!empty($data['name'])) {
            $contact['name'] = $data['name'];
            unset($data['name']);
        }

        if (empty($data['name']) && !empty($data['fullname'])) {
            $contact['name'] = $data['fullname'];
        }

        //Смотрю какие данные разрешено передавать.  По умолчанию пусто
        // email,mobilephone,fullname
        $userFields = $this->tools->parseFieldsSet($this->amoCRM->config['userFields']);
        foreach ($userFields as & $field) {
            $field = mb_strtolower($field);
        }

        //Убедимся в наличии всех необходимых полей контакта. Если нужно создам новые
        $this->checkClientsCustomFields(array_keys($data), $this->amoCRM->config['autoCreateUsersCustomFields']);
        foreach ($data as $key => $value) {
            $customField = $this->prepareCustomField($key, $value);
            if ($customField) {
                $contact['custom_fields_values'][] = $customField;
            }
        }
        return $contact;
    }

    /**
     * Получение user_id для amoCRM
     *
     * @param $id
     * @param bool $create_if_empty
     *
     * @return bool|int
     * @internal param $user_id|null
     * @internal param $user|xPDOSimpleObject
     */
    private function getUserId($id, $create_if_empty = false)
    {
        $user_id = 0;
        if ($contact = $this->modx->getObject('amoCRMUser', ['user' => $id])) {
            $user_id = $contact->get('user_id');
        } elseif ($create_if_empty === true) {
            if ($user = $this->getUser($id)) {
                $profile = $user->getOne('Profile');
                $data = [
                    'name' => $profile->get('fullname'),
                ];
                $user_id = $this->addContact($data, $id);
            }
        } else {
            return null;
        }

        return $user_id;
    }

    /**
     * Получение информации о контактах
     *
     * @param int|array $ids Массив ID или единичный ID
     * @param string $query Строка запроса
     * @param int|array $responsible_user_id
     * @param int $limit
     * @param int $offset
     *
     * @return array
     */

    public function getContacts($ids = [], $query = '', $responsible_user_id = [], $limit = 250, $offset = 0)
    {
        $contacts = [];
        if (is_numeric($ids)) {
            $ids = [$ids];
        }
        if (is_numeric($responsible_user_id)) {
            $responsible_user_id = [$responsible_user_id];
        }
        $data = [
            'page' => 1,
            'limit' => $limit,
        ];
        if (!empty($query)) {
            $data['query'] = $query;
        }
        if (!empty($responsible_user_id)) {
            $data['responsible_user_id'] = $responsible_user_id;
        }
        if ($result = $this->tools->sendRequest('/api/v4/contacts', $data, 'GET')) {
            foreach ($result['_embedded']['contacts'] as $contact) {
                $contacts[$contact['id']] = $contact;
            }
        }
        return $contacts;
    }

    /**
     * Установка в modUser request_id для amoCRM
     *
     * @param integer $user
     * @param integer $user_id
     *
     * @return mixed
     * @internal param $request_id
     */
    public function setUserId($user, $user_id)
    {
        if (!$user or !$user_id) {
            return true;
        }
        if ($contact = $this->modx->getObject('amoCRMUser', ['user' => $user, 'user_id' => $user_id])) {
            return true;
        }
        $contact = $this->modx->newObject('amoCRMUser', ['user' => $user, 'user_id' => $user_id]);
        return $contact->save();
    }

    private function guardContactROFields($contact, $amoContact)
    {
        $roFields = $this->tools->parseFieldsSet($this->amoCRM->config['userReadonlyFields']);

        foreach ($roFields as $roField) {
            if (isset($amoContact[$roField])) {
                $contact[$roField] = $amoContact[$roField];
            }
        }

        $amoROFields = [];
        if (!empty($amoContact['custom_fields'])) {
            foreach ($amoContact['custom_fields'] as $amoCF) {
                if (in_array($amoCF['id'], $roFields)) {
                    $amoROFields[$amoCF['id']] = $amoCF['values'][0]['value'];
                }
            }
        }

        if (!empty($contact['custom_fields'])) {
            foreach ($contact['custom_fields'] as & $cf) {
                if (isset($amoROFields[$cf['id']])) {
                    $cf['values'][0]['value'] = $amoROFields[$cf['id']];
                } elseif (in_array($cf['id'], $roFields)) {
                    $cf['values'][0]['value'] = '';
                }
            }
        }

        return $contact;
    }

    /**
     * Получение сделок контакта
     *
     * @param array $ids Массив ID или единичный ID
     * @param string $query
     *
     * @return array
     */
    private function getContactsLeads($ids = [], $query = '')
    {
        $contactsLeads = [];
        $contacts = $this->getContacts($ids, $query);
        foreach ($contacts as $id => $contact) {
            $contactsLeads[$id] = $contact['leads']['id'];
        }
        return $contactsLeads;
    }

    /**
     * Метод сравнивает дополнительные поля контактов АМО и сверяет с теми, что требуется передать.
     * Недостающие создает, при наличии соответствующего флага.
     * @param array $fields
     * @param bool $needCreate
     * @return bool
     */
    public function checkClientsCustomFields($fields = [], $needCreate = false)
    {
        //$fields: array_keys($profile->toArray()) ||   array_keys(formIt fields)
        //$userFields - по умолчанию пуст
        $userFields = $this->tools->parseFieldsSet($this->amoCRM->config['userFields']);
        $newFields = [];
        $result = true;

        //Перебираю ключи профиля и собираю массив полей, которые требуется иметь в дополнительных полях контакта, но которых там нет
        foreach ($fields as $field) {
            //Проверяю есть ли передаваемое поле в списке полей AMO
            if (!$this->getContactsCustomFieldId($field)) {
                //Если такого поля нет, но мы хотим его передавать - добавляю в список создаваемых полей.
                if (in_array($field, $userFields)) {
                    $newFields[] = $field;
                }
                if (!$needCreate) {
                    $result = false;
                }
            }
        }

        //Создаю дополнительные поля
        if ($needCreate) {
            $this->addContactsCustomFields($newFields);
            $result = true;
        }
        return $result;
    }

    /**
     * Метод по ключу проверяет наличие указанного дополнительного поля секции contacts
     * @param $key
     * @return mixed|null
     */
    private function getContactsCustomFieldId($key)
    {
        return $this->amoCRM->getCustomFieldId('contacts', $key);
    }

    /**
     * Метод готовит дополнительные поля, в зависимости от типа данных
     * @param int|string $key
     * @param string $value
     *
     * @return array
     */
//    private function getContactsCustomFieldValue($key, $value)
//    {
//        $result = array();
//        $keyStr = mb_strtolower($key);
//        $keyInt = -1;
//        if ($value == '') {
//            $value = ' ';
//        }
//        $userCustomFields = $this->getContactsCustomFieldsList();
//
//        //Если в списке дополнительных полей нет передаваемого поля
//        if (!isset($userCustomFields[$key]) || empty($userCustomFields[$key])) {
//            return false;
//        }
//        $customField = $userCustomFields[$key];
//
//        //Если формируемое поле имеет тип enum
//        if (!empty($customField['enums'])) {
//            //В настройках смотрю какие enum значения брать из предложенных в полях
//            $enumFields = $this->amoCRM->parseFieldsSet($this->amoCRM->config['userEnumFields']);
//            $enumType = 'WORK';
//            if (array_key_exists($keyStr, $enumFields)) {
//                $enumType = $enumFields[$keyStr];
//            }
//
//            $fieldEnum_id = 0;
//            foreach ($customField['enums'] as $fieldEnum) {
//                if ($fieldEnum['value'] == $enumType) {
//                    $fieldEnum_id = $fieldEnum['id'];
//                    break;
//                }
//            }
//
//            if ($fieldEnum_id > 0) {
//                $result = [
//                    'values' => [
//                        'value' => $value,
//                        'enum_id' => $fieldEnum_id,
//                    ]
//                ];
//            }
//            return $result;
//        }
//
//        //Для остальных строковых типов
//        if (!is_array($value)) {
//            $result = array(
//                'value' => $value,
//            );
//            if (is_numeric($key)) {
//                $keyStr = isset($userCustomFields[$key]) ? mb_strtolower($userCustomFields[$key]['name']) : '';
//                $keyInt = $key;
//            }
//            $result = array($result);
//
//            return $result;
//        }
//
//    }

    private function addContactsCustomFields($names)
    {
        $this->amoCRM->addCustomFields($names, 'contacts');
    }

    /**
     * Получение списка кастомных полей для контактов
     *
     * @return mixed
     */
    private function getContactsCustomFieldsList()
    {
        $fields = $this->amoCRM->getCustomFieldsList();
        return $fields['contacts'];
    }

    /**
     * Сокращение для получения объекта modUser по переданному user_id из amoCRM
     *
     * @param $user_id
     *
     * @return null|amoCRMUser
     */
    private function getUser($user_id)
    {
        /** @var amoCRMUser $user */
        $user = $this->modx->getObject('amoCRMUser', ['user_id' => $user_id]);

        return $user;
    }

    /**
     * Обновление или создание пользователя в MODX из контакта amoCRM
     *
     * @TODO Доработать. Протестировать
     *
     * @param array $contact
     *
     * @return bool
     */
    public function updateUserInMODX($contact)
    {
        $userData = $this->prepareUserFromContact($contact);
        if (!$this->authorizeAdmin()) {
            return false;
        }
        $this->modx->error->reset();
        /** @var modUser $user */
        if (
            $user = $this->modx->getObject('modUser', ['username' => $userData['username']])
            or (
                $amoUserLink = $this->modx->getObject('amoCRMUser', ['user_id' => $contact['id']])
                and $user = $amoUserLink->getOne('User')
            )
        ) {
//            unset($userData['username']);
            $userData = array_merge($userData, ['id' => $user->get('id'), 'username' => $user->get('username')]);
            $response = $this->tools->runProcessor(
                'security/user/update',
                $userData
            );
        } else {
            $response = $this->tools->runProcessor('security/user/create', $userData);
            if ($response->response['success']) {
                $this->setUserId($response->response['object']['id'], $contact['id']);
            }
        }

        return $response['success'];
    }

    /**
     * Преобразование полей контакта amoCRM в поля пользователя MODX
     *
     * @TODO Сделать преобразование полей
     *
     * @param array $contact
     *
     * @return array
     */

    public function prepareUserFromContact($contact)
    {
        $allowedFields = array_merge(
            $this->amoCRM->config['defaultUserFields'],
            $this->tools->parseFieldsSet($this->amoCRM->config['userFields'])
        );
        $userData = $contact;
        $userData['fullname'] = $contact['name'];
        foreach ($contact['custom_fields'] as $custom_field) {
            $fieldName = $custom_field['name'];

            if (!in_array($fieldName, $allowedFields) or isset($userData[$fieldName])) {
                continue;
            }

            $values = [];

            foreach ($custom_field['values'] as $v) {
                $values[] = $v['value'];
                if (empty($this->amoCRM->config['userFieldsGlueAmoValues'])
                    or strtolower($fieldName) == 'email'
                    or strtolower($fieldName) == 'phone'
                ) {
                    break;
                }
            }

            $userData[$fieldName] = implode($this->amoCRM->config['userFieldsGlueAmoValues'], $values);
        }
        $userData['phone'] = $userData['Телефон'];
        $userData['email'] = trim(str_replace(' ', '', $userData['Email'])) ?:
            $this->modx->sanitizeString($userData['phone']) . '@emails.' . $this->modx->getOption('http_host');
//        $userData['email'] = trim(str_replace(' ', '', $userData['email']));
        $userData['username'] = $userData['email'];
        unset(
            $userData['custom_fields'],
            $userData['name']
        );
        return $userData;
    }

    private function authorizeAdmin()
    {
        if ($member = $this->modx->getObject('modUserGroupMember', ['user_group' => 1])
            and $user = $this->modx->getObject('modUser', $member->member)) {
            $this->modx->user = $this->modx->getObjectGraph('modUser', '{"Profile":{},"UserSettings":{}}',
                ['modUser.username' => $user->get('username')]);
            $user->addSessionContext($this->modx->context->key);
            return true;
        }
        return false;
    }

    public function searchContact($phone)
    {
        $output = [];
        if (!empty($phone)) {
            $phone = preg_replace('#[^0-9]#', '', $phone);
            $link = '/api/v4/contacts';
            $data = [];
            $data['page'] = 1;
            $data['limit'] = 2;
            $data['query'] = $phone;
            $result = $this->tools->sendRequest($link, $data, 'GET');
            if (!empty($result['_embedded'])) {
                $contact = array_shift($result['_embedded']['contacts']);
                $output = $contact;
            }
        }
        return $output;
    }

    private function prepareCustomField($key, $value)
    {
        $output = [];
        if (empty($key) || empty($value)) {
            return false;
        }

        $key = mb_strtolower($key);

        //Смотрю какие контакты разрешено передавать.
        $userFields = $this->tools->parseFieldsSet($this->amoCRM->config['userFields']);

        if (!in_array($key, $userFields)) {
            return false;
        }

        if (in_array($key, $this->amoCRM->config['defaultUserFields'])) {
            $output[$key] = $value;
            return $output;
        }

        if (in_array($key, $userFields)) {
            $customField = $this->getCustomFieldByKey($key);

            if (!$customField) {
                return false;
            }

            //Если формируемое поле имеет тип enum

            if (!empty($customField['enums'])) {
                //В настройках смотрю какие enum значения брать из предложенных в полях
                $enumFields = $this->tools->parseFieldsSet($this->amoCRM->config['userEnumFields']);
                $enumType = 'WORK';
                if (array_key_exists($key, $enumFields)) {
                    $enumType = $enumFields[$key];
                }

                $fieldEnum_id = 0;

                foreach ($customField['enums'] as $fieldEnum) {
                    if ($fieldEnum['value'] == $enumType) {
                        $fieldEnum_id = $fieldEnum['id'];
                        break;
                    }
                }

                if ($fieldEnum_id > 0) {
                    $output = [
                        'field_id' => $customField['id'],
                        'values' => [
                            [
                                'value' => $value,
                                'enum_id' => $fieldEnum_id,
                            ]
                        ]
                    ];
                }
                return $output;
            }

            //Для остальных строковых типов
            if (!is_array($value)) {
                $output = [
                    'field_id' => $customField['id'],
                    'values' => [
                        [
                            'value' => $value
                        ]
                    ]
                ];

                return $output;
            }
        }

        return $output;
    }

    private function getCustomFieldByKey($key)
    {
        switch ($key) {
            case 'mobilephone':
                $key = 'phone';
                break;
        }

        $customFields = $this->amoCRM->getCustomFieldsList();
        if (!empty($customFields['contacts'])) {
            foreach ($customFields['contacts'] as $field) {
                if (
                    mb_strtolower($field['code']) == $key
                    || mb_strtolower($field['name']) == $key
                ) {
                    return $field;
                }
            }

            return false;
        }
    }

    /**
     * @param array|integer $user
     * @param array $additions
     *
     * @return array
     */
    public function prepareProfile($user, $additions = [])
    {
        /** @var modUser $modUser */
        if (is_numeric($user) and $modUser = $this->modx->getObject('modUser', $user)) {
            $user = ['id' => $modUser->get('id'), 'username' => $modUser->get('username')];
            /** @var modUserProfile $profile */
            if ($profile = $modUser->getOne('Profile')) {
                $user = array_merge($user, $profile->toArray());
            }
        }
        $user = array_replace($user, $additions);
        $user['name'] = $user['fullname'] ?: $user['username'];
//        $user['Email'] = $user['email'];
//        $user['Phone'] = $user['phone'];
        unset($user['fullname']);
        return $user;
    }
}
