<?php
/** @var modX $modx */
/** @var modUSer $user */
/** @var amoCRM $amo */
/** @var msOrder $order */
/** @var integer $status */

switch ($modx->event->name) {
    case "msOnCreateOrder":
        if (!($amo = $modx->getService('amocrm', 'amoCRM', MODX_CORE_PATH . 'components/amocrm/model/amocrm/'))) {
            return;
        }

        if (!$modx->getOption('amocrm_update_order_on_change_status')) {
            /** @var msOrder $msOrder */
            $order_id = $amo->addOrder($msOrder);
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
