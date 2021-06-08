id: 27
source: 1
name: amoCRMCustom
category: amoCRM
properties: 'a:0:{}'

-----

/** @var modX $modx */
/** @var modUSer $user */
/** @var amoCRM $amo */

switch ($modx->event->name) {
    case "msOnCreateOrder":
        // Проверяем периходил ли пользователь по специальной ссылке
        $amoUserid = $_SESSION['amo_userid'];
        if (!$amoUserid) {
            if ($user = $modx->getUser()) {
                if ($profile = $user->getOne('Profile')) {
                    $amoUserid = $profile->get('amo_userid');
                }
            }
        }
        // если нет, то выполняем стандартный скрипт
        if (!$amoUserid) {
            if (!($amo = $modx->getService('amocrm', 'amoCRM', MODX_CORE_PATH . 'components/amocrm/model/amocrm/'))) {
                return;
            }
            if (!$modx->getOption('amocrm_update_order_on_change_status')) {
                $order_id = $amo->addOrder($msOrder, true);
            }
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
            $order_id = $amo->addOrder($order, true);
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