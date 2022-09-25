<?php
/** @var $modx gitModx */
switch($modx->event->name){
    case "SMSAfterCodeCheck":
        $val = &$modx->event->returnedValues;
        if($response['success']){
            $val['response']['message'] = $modx->lexicon('stik_profile_sms_approved');
        }
        break;

    case "SMSCodeActivate":
        /** @var stikSms $stikSms */
        $stikSms = $modx->getService('stik', 'stikSms', $modx->getOption('core_path').'components/stik/model/', []);
        $phone = $stikSms->preparePhone($values['phone']);
        // проверяем существование пользователя
        $user = $stikSms->findUser($phone);
        if (!$user) {
            $user = $stikSms->register($phone);
        }
        // авторизуем
        $stikSms->authenticate($user, 'web');
        break;
    case 'msOnCreateOrder':
        // Сохраняем в профайл пользователя поля из заказа
        /** @var stikSms $stikSms */
        $stikSms = $modx->getService('stikSms', 'stikSms', $modx->getOption('core_path').'components/stik/model/', []);
        /**@var msOrderCustom $order */
        /** @var msOrder $msOrder */
        $msAddress = $msOrder->getOne('Address');
        $user = $modx->getObject('modUser', $msOrder->get('user_id'));
        $profile = $user->getOne('Profile');
        $fullname = $msAddress->get('name') . ' ' . $msAddress->get('surname');
        if (empty($profile->get('name'))) {
            $profile->set('name', $msAddress->get('name'));
        }
        if (empty($profile->get('surname'))) {
            $profile->set('surname', $msAddress->get('surname'));
        }
        if (empty($profile->get('fullname'))) {
            $profile->set('fullname', $fullname);
        }
        if (empty($profile->get('mobilephone'))) {
            $profile->set('mobilephone', $stikSms->preparePhone($msAddress->get('phone')));
        }
        $profile->save();
        // Авторизуем пользователя
        $user->addSessionContext('web');
        $data = $order->get();
        $properties = $msOrder->get('properties');
        if (!is_array($properties)) {
            $properties = array();
        }
        $user = $modx->getObject('modUser', $msOrder->get('user_id'));
        $profile = $user->getOne('Profile');

        if(empty($data['phone'])){
            $data['phone'] = $profile->get('mobilephone');
        }
        if(empty($data['phone'])){
            return false;
        }
        /** @var maxma $maxma */
        $maxma = $modx->getService('maxma', 'maxma', $modx->getOption('core_path').'components/stik/model/', []);
        $maxma->setUserphone($data['phone']);
        if (isset($_POST['join_loyalty']) && $_POST['join_loyalty'] == 1) {
            $properties['join_loyalty'] = 1;
            $profile->set('join_loyalty', 1);
            $profile->save();
            // создаем клиента в Maxma
            if ($data) {
                $maxma->createNewClient([
                    'phoneNumber' => $maxma->userphone,
                    'email' => $data['email'],
                    'surname' => $data['surname'],
                    'name' => $data['name'],
                    'externalId' => 'modx'.$msOrder->get('user_id'),
                ]);
            }
        }
        if(empty($data['msloyalty'])){
            $data['msloyalty'] = 0;
        }
        $properties['msloyalty'] = $data['msloyalty'];
        $maxma->setOrder($msOrder->get('id'), $data['msloyalty']);
        $msOrder->set('properties', $properties);
        $msOrder->save();
        break;
    case "msOnBeforeGetOrderCustomer":
        /** @var msOrderCustom $order */
        $data = $order->get();
        /** @var stikSms $stikSms */
        $stikSms = $modx->getService('stikSms', 'stikSms', $modx->getOption('core_path').'components/stik/model/', []);
        $data['phone'] = $stikSms->preparePhone($data['phone']);
        if (!empty($data['phone'])) {
            $c = $modx->newQuery('modUser');
            $c->leftJoin('modUserProfile', 'Profile');
            $filter['Profile.mobilephone'] = $data['phone'];
            $c->where($filter);
            if ($user = $modx->getObject('modUser', $c)) {
                $scriptProperties['customer'] = $user;
            }
        }
        break;
}