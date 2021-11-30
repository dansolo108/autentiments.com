id: 25
name: stikLoyalty
category: stik
properties: 'a:0:{}'

-----

// Плагин работает только для авторизованных пользователей
if (!$modx->user->hasSessionContext('web')) return;

$maxma = $modx->getService('maxma', 'maxma', $modx->getOption('core_path').'components/stik/model/', []);

if (!($maxma instanceof maxma)) return '';

switch($modx->event->name) { 
    case 'msOnBeforeAddToOrder':
        /** @var string $key */
        if ($key == 'msloyalty') {
            if (!empty($value)) {
                $check = $maxma->checkBonuses($value);
                if ($check !== true) {
                    $modx->event->output($check);
                    $response = array(
                        'success' => false
                        ,'message' => $check
                        ,'data' => array('msloyalty' => 0)
                    );
                    exit(json_encode($response));
                }
                
                $data = $order->get();
                $percent = $modx->getOption('stik_maxma_cart_percent');
                $ms2 = $modx->getService('miniShop2');
                $ms2->initialize($modx->context->key);
                $cart = $ms2->cart->status();
                $msloyalty_allowable_amount = floor($cart['total_cost'] * $percent / 100);
    
                $pdoTools = $modx->getService('pdoTools');
                if ($declension = $pdoTools->getFenom()->getModifier('declension')) {
                    $allowable_amount_text = $declension($msloyalty_allowable_amount, $modx->lexicon('stik_declension_bonuses'), true);
                }
                
                if ($msloyalty_allowable_amount < $value) {
                    $response = array(
                        'success' => false
                        ,'message' => $modx->lexicon('stik_order_loyalty_amount_error', ['percent' => $percent, 'allowable_amount_text' => $allowable_amount_text])
                        ,'data' => array('msloyalty' => $data['msloyalty'])
                    );
                    exit(json_encode($response));
                }
            } else {
                $order->remove('msloyalty');
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
        
        $maxmaClient = $maxma->getClientInfo($data['phone'], 'phoneNumber');
        
        if (isset($_POST['join_loyalty']) && $_POST['join_loyalty'] == 1) {
            $properties['join_loyalty'] = 1;
            
            $profile->set('join_loyalty', 1);
            $profile->set('mobilephone', $data['phone']);
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
        if ($maxma->userphone || $maxmaClient) {
            if ($data && !empty($data['msloyalty']) && $maxma->userphone) {
                // $currency = (float)$modx->getPlaceholder('msmc.val');
                // $data['msloyalty'] = ceil($data['msloyalty'] * $currency);
                if ($maxma->checkBonuses($data['msloyalty']) === true) {
                    $properties['msloyalty'] = $data['msloyalty'];
                    $maxma->setOrder($msOrder->get('id'), $data['msloyalty'], 'apply'); // создаем заказ и резервируем бонусы
                }
            } else {
                if (!empty($maxmaClient['client']['phoneNumber'])) {
                    $maxma->setUserphone($maxmaClient['client']['phoneNumber']);
                }
                $stikLoyalty = $modx->getService('stik_loyalty', 'stikLoyalty', $modx->getOption('core_path').'components/stik/model/', []);
                if (!($stikLoyalty instanceof stikLoyalty)) return '';
                
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
        
        if (empty($properties['msloyalty']) && empty($properties['msloyalty_accrue']) && empty($properties['join_loyalty'])) return '';
        
        // Оплачен
        if ($status == 2) {
            $maxma->confirmOrder($order->get('id')); // подтверждаем заказ и списываем бонусы
        }
        // Отменен
        if ($status == 4) {
            $payed = $modx->getObject('msOrderLog', [
                'order_id' => $order->get('id'),
                'action' => 'status',
                'entry' => 2,
            ]);
            // если у заказа в истории был статус "Оплачен"
            if ($payed) {
                $maxma->returnOrder($order->get('id')); // делаем возврат и пересчет бонусов
            } else {
                $maxma->cancelOrder($order->get('id')); // отменяем заказ и возвращаем бонусы
            }
        }
        break;
        
    case 'msmcOnToggleCurrency':
        $ms2 = $modx->getService('miniShop2');
        $ms2->initialize($modx->context->key);
        $ms2->order->remove('msloyalty');
        break;
}