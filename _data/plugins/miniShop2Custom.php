id: 26
source: 1
name: miniShop2Custom
category: miniShop2
properties: 'a:0:{}'
static_file: core/components/minishop2/elements/plugins/plugin.minishop2.php

-----

/** @var modX $modx */
switch ($modx->event->name) {

    case 'OnMODXInit':
        // Load extensions
        /** @var miniShop2 $miniShop2 */
        if ($miniShop2 = $modx->getService('miniShop2')) {
            $miniShop2->loadMap();
        }
        break;

    case 'OnHandleRequest':
        // Handle ajax requests
        $isAjax = !empty($_SERVER['HTTP_X_REQUESTED_WITH']) && $_SERVER['HTTP_X_REQUESTED_WITH'] == 'XMLHttpRequest';
        if (empty($_REQUEST['ms2_action']) || !$isAjax) {
            return;
        }
        /** @var miniShop2 $miniShop2 */
        if ($miniShop2 = $modx->getService('miniShop2')) {
            $response = $miniShop2->handleRequest($_REQUEST['ms2_action'], @$_POST);
            @session_write_close();
            exit($response);
        }
        break;

    case 'OnManagerPageBeforeRender':
        /** @var miniShop2 $miniShop2 */
        if ($miniShop2 = $modx->getService('miniShop2')) {
            $modx->controller->addLexiconTopic('minishop2:default');
            $modx->regClientStartupScript($miniShop2->config['jsUrl'] . 'mgr/misc/ms2.manager.js');
        }
        break;

    case 'OnLoadWebDocument':
        // Handle non-ajax requests
        if (!empty($_REQUEST['ms2_action'])) {
            if ($miniShop2 = $modx->getService('miniShop2')) {
                $miniShop2->handleRequest($_REQUEST['ms2_action'], @$_POST);
            }
        }
        // Set product fields as [[*resource]] tags
        if ($modx->resource->get('class_key') == 'msProduct') {
            if ($dataMeta = $modx->getFieldMeta('msProductData')) {
                unset($dataMeta['id']);
                $modx->resource->_fieldMeta = array_merge(
                    $modx->resource->_fieldMeta,
                    $dataMeta
                );
            }
        }
        break;

    case 'OnWebPageInit':
        // Set referrer cookie
        /** @var msCustomerProfile $profile */
        $referrerVar = $modx->getOption('ms2_referrer_code_var', null, 'msfrom', true);
        $cookieVar = $modx->getOption('ms2_referrer_cookie_var', null, 'msreferrer', true);
        $cookieTime = $modx->getOption('ms2_referrer_time', null, 86400 * 365, true);

        if (!$modx->user->isAuthenticated() && !empty($_REQUEST[$referrerVar])) {
            $code = trim($_REQUEST[$referrerVar]);
            if ($profile = $modx->getObject('msCustomerProfile', array('referrer_code' => $code))) {
                $referrer = $profile->get('id');
                setcookie($cookieVar, $referrer, time() + $cookieTime);
            }
        }
        break;

    case 'OnUserSave':
        // Save referrer id
        if ($mode == modSystemEvent::MODE_NEW) {
            /** @var modUser $user */
            $cookieVar = $modx->getOption('ms2_referrer_cookie_var', null, 'msreferrer', true);
            $cookieTime = $modx->getOption('ms2_referrer_time', null, 86400 * 365, true);
            if ($modx->context->key != 'mgr' && !empty($_COOKIE[$cookieVar])) {
                if ($profile = $modx->getObject('msCustomerProfile', array('id' => $user->get('id')))) {
                    if (!$profile->get('referrer_id') && $_COOKIE[$cookieVar] != $user->get('id')) {
                        $profile->set('referrer_id', (int)$_COOKIE[$cookieVar]);
                        $profile->save();
                    }
                }
                setcookie($cookieVar, '', time() - $cookieTime);
            }
        }
        break;

    /*case 'msOnChangeOrderStatus':
        if (empty($status) || $status != 2) {
            return;
        }

        if ($user = $order->getOne('User')) {
            $q = $modx->newQuery('msOrder', array('type' => 0));
            $q->innerJoin('modUser', 'modUser', array('modUser.id = msOrder.user_id'));
            $q->innerJoin('msOrderLog', 'msOrderLog', array(
                'msOrderLog.order_id = msOrder.id',
                'msOrderLog.action' => 'status',
                'msOrderLog.entry' => $status,
            ));
            $q->where(array('msOrder.user_id' => $user->get('id')));
            $q->groupby('msOrder.user_id');
            $q->select('SUM(msOrder.cart_cost)'); // считаем без доставки для stikLoyalty
            if ($q->prepare() && $q->stmt->execute()) {
                $spent = $q->stmt->fetchColumn();
                if ($profile = $modx->getObject('msCustomerProfile', array('id' => $user->get('id')))) {
                    $profile->set('spent', $spent);
                    $profile->save();
                }
            }
        }
        break;*/
        
        
	case 'msOnChangeOrderStatus':
		//$order - объект msOrder
		//$status - идентификатор статуса
		$modx->log(1, $status);

		if (empty($status) || ($status != 2 && $status != 4)) {
			return;
		}

        // проверяем, неменялся ли до этого статус на "Оплачен"
        $paid = $modx->getCount('msOrderLog', array(
            'order_id' => $order->get('id'),
            'action' => 'status',
            'entry' => 2,
        ));
        $modx->log(1, $paid);
        if ($paid > 1 && $status != 4) {
            return;
        }

		if ($user = $order->getOne('User')) {
			/** @var msCustomerProfile $profile */
			if ($profile = $order->getOne('CustomerProfile')) {
				$user_amount = $profile->get('spent');
				
				// вычисляем сумму выкупа за вычетом доставки
				$accrual_amount = $order->get('cost');
				if ($order->get('delivery_cost') > 0) {
				    $accrual_amount = $accrual_amount - $order->get('delivery_cost');
				}
				$accrual_amount = $accrual_amount > 0 ? $accrual_amount : 0;
				$modx->log(1, $accrual_amount);
				if ($status == 2) {
    				$profile->set('spent', $user_amount + $accrual_amount);
				} elseif ($status == 4) {
    				$profile->set('spent', $user_amount - $accrual_amount);
				}
				$profile->save();
			}
		}
        break;
}