<?php
/** @var $modx gitModx */
switch($modx->event->name){
    case "SMSAfterCodeCheck":
        $val = &$modx->event->returnedValues;
        if($response['success']){
            /** @var array   $values */
            /** @var stikSms $stikSms */
            $stikSms = $modx->getService('stik', 'stikSms', $modx->getOption('core_path').'components/stik/model/', []);
            $phone = $stikSms->preparePhone($phone);
            // проверяем существование пользователя
            $user = $stikSms->findUser($phone);
            $val['response']['isRegister'] = false;
            if (!$user) {
                $user = $stikSms->register($phone);
                $val['response']['isRegister'] = true;
            }
            // авторизуем
            $stikSms->authenticate($user, 'web');
            $val['response']['message'] = $modx->lexicon('stik_profile_sms_approved');
        }
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
        break;
    case "msOnBeforeGetOrderCustomer":
        /** @var msOrderCustom $order */
        $data = $order->get();
        /** @var stikSms $stikSms */
        $stikSms = $modx->getService('stikSms', 'stikSms', $modx->getOption('core_path').'components/stik/model/', []);
        $data['phone'] = $stikSms->preparePhone($data['phone']);
        if(strlen($data['phone']) !== 11){
            $modx->event->output("В номере должно быть 11 цифр.");
            return false;
        }
        if(empty($data['receiver'])){
            $order->add('receiver', $data['name']." ".$data['surname']);
        }
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
    case "OnUserSave":
        /** @var $mode 'new' || 'upd' */
        if($mode === "upd"){
            /** @var modUserProfile $profile */
            /** @var $user modUser */
            $profile = $user->getOne('Profile');
            ['name'=>$name, 'surname'=>$surname] = $profile->toArray();
            if($name && $surname){
                $profile->set('fullname',"{$name} {$surname}");
                $profile->save();
            }
        }
        break;
}