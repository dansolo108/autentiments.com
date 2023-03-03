<?php

require_once dirname(__FILE__) . '/controllers/contacts.php';

class amoCRMWebhook
{

    const EVENT_RESPONSIBLE_LEAD = 'responsible_lead'; //	У сделки сменился ответственный
    const EVENT_RESPONSIBLE_CONTACT = 'responsible_contact'; //	У контакта сменился ответственный
    const EVENT_RESPONSIBLE_COMPANY = 'responsible_company'; //	У компании сменился ответственный
    const EVENT_RESPONSIBLE_CUSTOMER = 'responsible_customer'; //	У покупателя сменился ответственный
    const EVENT_RESPONSIBLE_TASK = 'responsible_task'; //	У задачи сменился ответственный
    const EVENT_RESTORE_LEAD = 'restore_lead'; //	Сделка восстановлена из корзины
    const EVENT_RESTORE_CONTACT = 'restore_contact'; //	Контакт восстановлен из корзины
    const EVENT_RESTORE_COMPANY = 'restore_company'; //	Компания восстановлена из корзины
    const EVENT_ADD_LEAD = 'add_lead'; //	Добавлена сделка
    const EVENT_ADD_CONTACT = 'add_contact'; //	Добавлен контакт
    const EVENT_ADD_COMPANY = 'add_company'; //	Добавлена компания
    const EVENT_ADD_CUSTOMER = 'add_customer'; //	Добавлен покупатель
    const EVENT_ADD_TASK = 'add_task'; //	Добавлена задача
    const EVENT_UPDATE_LEAD = 'update_lead'; //	Сделка изменена
    const EVENT_UPDATE_CONTACT = 'update_contact'; //	Контакт изменён
    const EVENT_UPDATE_COMPANY = 'update_company'; //	Компания изменена
    const EVENT_UPDATE_CUSTOMER = 'update_customer'; //	Покупатель изменен
    const EVENT_UPDATE_TASK = 'update_task'; //	Задача изменена
    const EVENT_DELETE_LEAD = 'delete_lead'; //	Удалена сделка
    const EVENT_DELETE_CONTACT = 'delete_contact'; //	Удалён контакт
    const EVENT_DELETE_COMPANY = 'delete_company'; //	Удалена компания
    const EVENT_DELETE_CUSTOMER = 'delete_customer'; //	Удален покупатель
    const EVENT_DELETE_TASK = 'delete_task'; //	Удалена задача
    const EVENT_STATUS_LEAD = 'status_lead'; //	У сделки сменился статус
    const EVENT_RESPONSIBLE = 'responsible'; //	lead	У сделки сменился ответсвенный
    const EVENT_NOTE_LEAD = 'note_lead'; //	Примечание добавлено в сделку
    const EVENT_NOTE_CONTACT = 'note_contact'; //	Примечание добавлено в контакт
    const EVENT_NOTE_COMPANY = 'note_company'; //	Примечание добавлено в компанию
    const EVENT_NOTE_CUSTOMER = 'note_customer'; //	Примечание добавлено в покупателя

    /** @var modX $modx */
    public $modx;
    /** @var  amoCRM $amo */
    public $amo;
    /** @var array $config */
    public $config = array();

    public $contactsController;


    /**
     * @param amoCRM $amoCRM
     */
    public function __construct(amoCRM $amoCRM)
    {
        $this->amo = $amoCRM;
        $this->modx = $this->amo->modx;
        $this->contactsController = new Contacts($this->modx, $this);

        $this->config['processorsPath'] = $this->amo->config['processorsPath'] . 'webhook/';
    }

    public function process($request = array())
    {
        $result = true;
        $this->amo->setWebhookMode(true);
        if (!$this->checkDomain(@$request['account']['subdomain'])) {
            $this->endFail();
            return false;
        }

        $response = $this->amo->tools->invokeEvent('amocrmOnBeforeWebhookProcess', array(
            'webhookData' => $request,
            'amoCRMWebhook' => $this,
        ));

        $request = array_merge($request, $response['data']['webhookData']);

        if (isset($request['leads'])) {
            if (isset($request['leads']['status'])) {
                $result = $this->changeLeadsStatuses($request['leads']['status'], true);
            }
            if (isset($request['leads']['update'])) {
                $result = $this->changeLeadsStatuses($request['leads']['update'], true);
            }
        }
        if (isset($request['contacts'])) {
            if (isset($request['contacts']['add'])) {
                $result = $this->updateUser($request['contacts']['add'], true);
            }
            if (isset($request['contacts']['update'])) {
                $result = $this->updateUser($request['contacts']['update'], true);
            }
        }

        $this->amo->tools->invokeEvent('amocrmOnWebhookProcess', array(
            'webhookData' => $request,
            'amoCRMWebhook' => $this,
        ));

        if (empty($result)) {
            /** @TODO Реализовать логирование ошибочных запросов от amoCRM в отдельное хранилище
             *  Пример:
             * $modx->log(ERROR, print_r($_POST, 1);
             * $modx->log(ERROR, print_r($result, 1);
             */
        }
        $this->endSuccess();
        return true;
    }

    public function checkDomain($domain)
    {
        return $domain == $this->amo->config['account'];
    }

    /**
     * Обновление статусов заказов MS2 (сделок Amo)
     *
     * @param array $leads
     * @param bool $canHold
     *
     * @return bool
     */
    public function changeLeadsStatuses($leads, $canHold = false)
    {
        $result = true;
        if (is_array($leads)) {
            foreach ($leads as $lead) {
                if ($this->amo->tools->checkSQ($canHold)) {
                    if ($this->amo->tools->addSQTask(amoCRMTools::SQ_ACTION_WEBHOOK_LEADS_STATUSES, $lead['id'], $lead)) {
                        return true;
                    }
                } else {
                    $result = (boolean)$this->amo->leadsController->changeOrderStatusInMS2($lead['id'], $lead['status_id']);
                }
            }
        } else {
            $result = false;
        }
        return $result;
    }

    public function updateUser($contacts, $canHold = false)
    {
        $result = true;
        if (is_array($contacts)) {
            foreach ($contacts as $contact) {
//                $this->log(print_r($contact, 1), 1, '', __FILE__);
                if ($this->amo->tools->checkSQ($canHold)) {
                    if ($this->amo->tools->addSQTask(amoCRMTools::SQ_ACTION_WEBHOOK_UPDATE_USER, $contact['id'], $contact)) {
                        return true;
                    }
                } else {
                    $result = (boolean)$this->contactsController->updateUserInMODX($contact);
                }
            }
        } else {
            $result = false;
        }
        return $result;
    }

    /**
     * Подписка вебхука на события в amoCRM
     *
     * @param string $url
     * @param array $events
     *
     * @return array
     */
    public function subscribeWebhookInAmo($url, $events)
    {
        $data = array(
            'subscribe' => array(
                array(
                    'url' => $url,
                    'events' => $events,
                    'disabled' => false,
                ),
            )
        );
        if ($result = $this->amo->tools->sendRequest('/api/v2/webhooks/subscribe', $data)) {
            return $result['_embedded']['items'][0];
        } else {
            return array();
        }
    }

    /**
     * Отписка вебхука от событий в amoCRM
     *
     * @param string $url
     * @param array $events
     *
     * @return array
     */
    public function unsubscribeWebhookInAmo($url, $events)
    {
        $data = array(
            'unsubscribe' => array(
                array(
                    'url' => $url,
                    'events' => $events
                ),
            )
        );
        if ($result = $this->amo->tools->sendRequest('/api/v2/webhooks/unsubscribe', $data)) {
            return $result['_embedded']['items'][0];
        } else {
            return array();
        }
    }

    /**
     * Получение вебхуков из amoCRM по списку URL
     *
     * @param array $urls
     * @return array
     */
    public function getWebhookFromAmo($urls = array())
    {

        if ($result = $this->amo->tools->sendRequest('/api/v2/webhooks', array(), 'GET')) {
            $result = $result['_embedded']['items'];
            if (!empty($urls)) {
                foreach ($result as $k => $webhook) {
                    if (!in_array($webhook['url'], $urls)) {
                        unset($result[$k]);
                    }
                }
            }
            return $result;
        } else {
            return array();
        }
    }

    /**
     * @param string $action
     * @param array $data
     * @return boolean
     */
    public function runProcessor($action, $data)
    {
        $response = $this->modx->runProcessor(
            $action,
            $data,
            array('processors_path' => $this->config['processorsPath'])
        );
        if (!$response->response['success']) {
            $this->log("processor $action failed with data " . print_r($data, 1));
        }

        return $response->response['success'];

    }

    /**
     * Successful break executing
     */
    public function endSuccess()
    {
        @session_write_close();
        exit(true);
    }

    /**
     * Break executing by 500 error
     */
    public function endFail()
    {
        @session_write_close();
        header($_SERVER['SERVER_PROTOCOL'] . ' 500 Internal Server Error', true, 500);
    }

    public function log($msg = '', $level = xPDO::LOG_LEVEL_WARN, $def = '', $file = '', $line = '', $target = '')
    {
        $this->amo->log($level, $msg, $target, $def, $file, $line);
    }

}
