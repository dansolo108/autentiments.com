id: 27
source: 1
name: amoCRMCustom
category: amocrm
properties: 'a:0:{}'
disabled: 1

-----

/** @var modX $modx */
/** @var modUSer $user */
/** @var amoCRM $amo */

switch ($modx->event->name) {
    case "msOnCreateOrder":
        // Проверяем периходил ли пользователь по специальной ссылке
        // $amoUserid = $_SESSION['amo_userid'];
        // if (!$amoUserid) {
        //     if ($user = $modx->getUser()) {
        //         if ($profile = $user->getOne('Profile')) {
        //             $amoUserid = $profile->get('amo_userid');
        //         }
        //     }
        // }
        // если да, то выполняем стандартный скрипт
        // if ($amoUserid) {
            if (!($amo = $modx->getService('amocrm', 'amoCRM', MODX_CORE_PATH . 'components/amocrm/model/amocrm/'))) {
                return;
            }
            if (!$modx->getOption('amocrm_update_order_on_change_status')) {
                $order_id = $amo->addOrder($msOrder);
            }
        // }
        break;

    case 'amocrmOnBeforeOrderSend':
        // передаем сохраненный в сессии или профиле id пользователя amoCRM
        $amoUserid = $_SESSION['amo_userid'] ?: '';

        if (is_object($msOrder) && !$amoUserid) {
            if ($profile = $msOrder->getOne('UserProfile')) {
                $amoUserid = $profile->get('amo_userid');
            }
        }

        $values = & $scriptProperties['__returnedValues'];

        if (!is_array($values)) {
            $values = array();
        }
        if (!isset($values['lead']) or !is_array($values['lead'])) {
            $values['lead'] = [];
        }
        if ($amoUserid) {
            $values['lead']['_embedded']['contacts'][0]['id'] = $amoUserid;
        }
        
        $modx->event->returnedValues = $values;
        break;

    case 'amocrmOnOrderSend':
        // Линкуем товары к лиду
        $lead_id = $amoCRMLead->get('order_id'); // id созданного лида
        $catalog_id = 1193; // id каталога стоварами в AmoCRM
        $msOrder = $modx->getObject('msOrder', $amoCRMLead->get('order'));
        $msOrderProducts = $msOrder->getMany('Products');
        
        foreach ($msOrderProducts as $orderProduct) {
            $options = $orderProduct->get('options');
            if (isset($options['color']) && isset($options['size'])) {
                $msProductData = $modx->getObject('msProductData', $orderProduct->get('product_id'));
                $article = $msProductData->get('article');
                if ($article) {
                    $result = $amoCRM->tools->sendRequest('/api/v2/catalog_elements?catalog_id='.$catalog_id.'&term=' . $article, [], 'GET');
                    $products = $result['_embedded']['items'];
                    $links = [];
                    foreach ($products as $product) {
                        if (mb_stripos($product['name'], $options['size']) !== false && mb_stripos($product['name'], $options['color']) !== false) {
                            $links[] = [
                                'to_entity_id' => $product['id'],
                                'to_entity_type' => 'catalog_elements',
                                'metadata' => [
                                    'quantity' => $orderProduct->get('count'),
                                    'catalog_id' => $catalog_id,
                                ]
                            ];
                            // $modx->log(1, $product['id']);
                        }
                    }
                }
            }
        }
        
        if (count($links)) {
            $result = $amoCRM->tools->sendRequest('/api/v4/leads/'.$lead_id.'/link', $links, 'POST');
        }
        break;
        
    case 'msOnChangeOrderStatus':
        if (!($amo = $modx->getService('amocrm', 'amoCRM', MODX_CORE_PATH . 'components/amocrm/model/amocrm/'))) {
            return;
        }

        if ($amo->isWebhookMode()) {
            return true;
        }
        if ($modx->getOption('amocrm_update_order_on_change_status')) {
            $order_id = $amo->addOrder($order);
        } else {
            $order_id = $order->get('id');
            $amo->changeOrderStatusInAmo($order_id, $status, true);
        }
        return true;
        break;

    case "OnUserFormSave":
    case "OnUserProfileSave":
        if (!($amo = $modx->getService('amocrm', 'amoCRM', MODX_CORE_PATH . 'components/amocrm/model/amocrm/'))) {
            return;
        }

        if (
            ($modx->context->key == 'mgr' and !$amo->config['userSaveInMgr'])
            or ($modx->event->name == 'OnUserProfileSave' and !$amo->config['userSaveByProfile'])
            or $amo->isWebhookMode()
        ) {
            return;
        }
        $authorized = $amo->auth();
        if ($authorized) {
            if (!isset($user) and isset($userprofile)) {
                $user = $userprofile->getOne('User');
            }

            $user_id = $user->get('id');
            $profile = $user->getOne('Profile');
            $amo->addContact($profile->toArray(), $user_id, array(), true);
        }

        break;
}