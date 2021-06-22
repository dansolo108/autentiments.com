id: 25
name: stikLoyalty
category: stik
properties: 'a:0:{}'

-----

// Плагин работает только для авторизованных пользователей
if (!$modx->user->hasSessionContext('web')) return;

$stikLoyalty = $modx->getService('stik_loyalty', 'stikLoyalty', $modx->getOption('core_path').'components/stik/model/', []);
$maxma = $modx->getService('maxma', 'maxma', $modx->getOption('core_path').'components/stik/model/', []);

if (!($stikLoyalty instanceof stikLoyalty) || !($maxma instanceof maxma)) return '';

switch($modx->event->name) { 
    case 'msOnBeforeAddToOrder':
        /** @var string $key */
        if ($key == 'msloyalty') {
            if (!empty($value)) {
                $check = $maxma->checkBonuses($value);
                if ($check !== true) {
                    $modx->event->output($check);
                }
                
                $data = $order->get();
                $percent = 99;
    
                $pdoTools = $modx->getService('pdoTools');
                if ($declension = $pdoTools->getFenom()->getModifier('declension')) {
                    $allowable_amount_text = $declension($data['msloyalty_allowable_amount'], $modx->lexicon('stik_declension_bonuses'), true);
                }
                
                if ($data['msloyalty_allowable_amount'] < $value) {
                    $response = array(
                        'success' => false
                        ,'message' => $modx->lexicon('stik_order_loyalty_amount_error', ['percent' => $percent, 'allowable_amount_text' => $allowable_amount_text])
                        ,'data' => array('msloyalty' => $data['msloyalty'])
                    );
                    exit(json_encode($response));
                }
            } else {
                $order->remove('total_cost_loyalty');
            }
        }
        break;

    case 'msOnBeforeRemoveFromOrder':
        /** @var string $key */
        if ($key == 'msloyalty') {
            $order->remove('total_cost_loyalty');
        }
        break;

    case 'msOnBeforeGetOrderCost':
        if (!empty($with_cart) && !empty($cart)) {
            $data = $order->get();
            if ($data && $data['msloyalty']) {
                $currency = (float)$modx->getPlaceholder('msmc.val');
                $status = $order->ms2->cart->status();
                $cost = $status['total_cost'] - ($data['msloyalty'] * $currency);
                $order->add('total_cost_loyalty', $cost ? $cost : 0);
            }
        }
        break;

    case 'msOnGetOrderCost':
        
        if ($user = $modx->getUser()) {
            if ($profile = $user->getOne('Profile')) {
                if ($profile->get('mobilephone') && $profile->get('join_loyalty')) {
                    
                    $status = $order->ms2->cart->status();
                    $data = $order->get();
            
                    $cost = $status['total_cost'];
            
                    $allowable_amount = floor($cost * 99 / 100);
                    
                    $bonus = $modx->runSnippet('msMultiCurrencyPriceFloor', ['price' => $maxma->getClientBalanceByPhone($profile->get('mobilephone'))]) /*number_format(($maxma->getClientBalanceByPhone($profile->get('mobilephone'))), 0, '.', '')*/;
                    if ($bonus < $allowable_amount) {
                        $allowable_amount = $bonus;
                    }
                    
                    $pdoTools = $modx->getService('pdoTools');
                    if ($declension = $pdoTools->getFenom()->getModifier('declension')) {
                        $allowable_amount_text = $declension($allowable_amount, $modx->lexicon('stik_declension_bonuses'), true);
                    }
                    
                    $order->add('msloyalty_text', $modx->lexicon('stik_order_loyalty_text_max') . ' ' . ($allowable_amount_text ? $allowable_amount_text : $allowable_amount));
                    $order->add('msloyalty_allowable_amount', $allowable_amount);
                }
            }
        }

        break;

    case 'msOnCreateOrder':
        /**@var msOrderInterface $order */
        $data = $order->get();
        
        $properties = $msOrder->get('properties');
        if (!is_array($properties)) {
            $properties = array();
        }
        $user = $modx->getObject('modUser', $msOrder->get('user_id'));
        $profile = $user->getOne('Profile');
        
        if ($_POST['join_loyalty'] == 1) {
            $properties['join_loyalty'] = 1;
            
            $profile->set('join_loyalty', 1);
            $profile->save();
            // создаем клиента в Maxma
            if ($data) {
                $maxma->createNewClient([
                    'phoneNumber' => $data['phone'],
                    'email' => $data['email'],
                    'surname' => $data['surname'],
                    'name' => $data['name'],
                    'externalId' => 'modx'.$msOrder->get('user_id'),
                ]);
            }
        }
        if ($data && !empty($data['msloyalty'])) {
            $currency = (float)$modx->getPlaceholder('msmc.val');
            $data['msloyalty'] = ceil($data['msloyalty'] * $currency);
            if ($maxma->checkBonuses($data['msloyalty']) === true) {
                $properties['msloyalty'] = $data['msloyalty'];
                $maxma->setOrder($msOrder->get('id'), $data['msloyalty'], 'apply'); // создаем заказ и резервируем бонусы
            } else {
                $bonuses_ammount = $stikLoyalty->getLoyaltyBonusAccrual($msOrder->get('cart_cost'), $msOrder->get('user_id'));
                $properties['msloyalty_accrue'] = $bonuses_ammount;
                $maxma->setOrder($msOrder->get('id'), $bonuses_ammount, 'collect'); // создаем заказ и начисляем бонусы
            }
        }
        $msOrder->set('properties', $properties);
        $msOrder->save();
        break;
        
    case 'msOnChangeOrderStatus':
        $properties = $order->get('properties');
        // Оплачен
        if ($status == 2) {
            if (isset($properties['msloyalty']) && $properties['msloyalty']) {
                $maxma->confirmOrder($order->get('id')); // подтверждаем заказ и списываем бонусы
            }
        }
        // Отменен
        if ($status == 4) {
            if (isset($properties['msloyalty']) && $properties['msloyalty'] || isset($properties['join_loyalty']) && $properties['join_loyalty']) {
                $maxma->cancelOrder($order->get('id')); // отменяем заказ и возвращаем бонусы
            }
        }
}