id: 29
source: 1
name: stikSms
category: SMS
properties: 'a:0:{}'

-----

switch($modx->event->name){

    case "SMSAfterCodeCheck":
        $val = &$modx->event->returnedValues;
        if($response['success']){
            $val['response']['message'] = $modx->lexicon('stik_profile_sms_approved');
        }
        break;

    case "SMSCodeActivate":
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
}