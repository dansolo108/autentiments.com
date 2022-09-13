<?php
$email = htmlspecialchars(strip_tags($_POST['email_sbs']));
$language = htmlspecialchars(strip_tags($_POST['language']));
$antispam = htmlspecialchars(strip_tags($_POST['link']));

$errors = array();

if (!empty($antispam)) {
    return $AjaxForm->error('Скрытое поле link должно быть пустым', ['link' => 'spam']);
}

if (empty($email)) {
    $errors['email_sbs'] = $modx->lexicon('stik_newsletter_err_noemail');
}

if (!empty($email) && !preg_match('/.+@.+\..+/i', $email)) {
    $errors['email_sbs'] = $modx->lexicon('stik_newsletter_err_email');
}

if (!empty($errors)) {
    return $AjaxForm->error($modx->lexicon('stik_newsletter_err_form'), $errors);
} else {
    $stik = $modx->getService('stik', 'stik', MODX_CORE_PATH . 'components/stik/model/', $scriptProperties);
    if (!$stik) {
        return 'Could not load stik class!';
    }
    
    $exist = $modx->getObject('stikSubscriber', [
        'email' => $email,
    ]);
    
    if (!$exist) {
        // Создаем запись
        $object = $modx->newObject('stikSubscriber');
        
        $hash = $email.time();
        
        $active = 0; // подписка не активна по умолчанию
        $profile = $modx->user->getOne('Profile');
        $profile_email =  $profile ? $profile->get('email') : '';
        if (!empty($email) && (trim($email) == trim($profile_email))) $active = 1; // активируем, если пользователь авторизован и email совпадает
        
        $array = [
            'email' => $email,
            'user_id' => $modx->getLoginUserID($modx->context->key) ?: 0,
            'language' => $language,
            'createdon' => time(),
            'hash' => crypt($hash, md5($hash)),
            'active' => $active,
        ];
            
        $object->fromArray($array);
        $object->save();
        
        // Отправляем менеджерам письмо с подтверждением 
        $emailManager = $scriptProperties['emailTo'] ?: $modx->getOption('ms2_email_manager');
        $subjectManager = $scriptProperties['subject'];
        $chunkManager = $scriptProperties['tpl'];
        $stik->sendEmail($emailManager, $subjectManager, $chunkManager, $array);
        // Отправляем пользователю письмо с подтверждением 
        $params = [
            'hash' => $array['hash'],
            'activated' => $active,
        ];
        $subject = $scriptProperties['subjectConfirm'];
        $tplConfirm = $scriptProperties['tplConfirm'];
        $stik->sendEmail($email, $subject, $tplConfirm, array_merge($array, $params));
    } else {
        return $AjaxForm->error($modx->lexicon('stik_newsletter_err_subscribed'), ['email_sbs' => $modx->lexicon('stik_newsletter_err_subscribed_details')]);
    }
}
return $AjaxForm->success($modx->lexicon('stik_newsletter_success_message'));