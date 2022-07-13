<?php
$product_id = (int)$_POST['product_id'];
$email = htmlspecialchars(strip_tags($_POST['email']));
$size = htmlspecialchars(strip_tags($_POST['size']));
$color = htmlspecialchars(strip_tags($_POST['color']));
$language = htmlspecialchars(strip_tags($_POST['language']));

$errors = array();

if (empty($size)) {
    $errors['size'] = $modx->lexicon('stik_size_subscribe_err_size');
}

if (empty($color)) {
    $errors['color'] = $modx->lexicon('stik_size_subscribe_err_color');
}

if (empty($email) || !preg_match('/.+@.+\..+/i', $email)) {
    $errors['email'] = $modx->lexicon('stik_size_subscribe_err_email');
}

if (!empty($errors)) {
    return $AjaxForm->error($modx->lexicon('stik_size_subscribe_err_form'), $errors);
} else {
    $stik = $modx->getService('stik', 'stik', MODX_CORE_PATH . 'components/stik/model/', $scriptProperties);
    if (!$stik) {
        return 'Could not load stik class!';
    }
    
    $exist = $modx->getObject('stikSizesubscriber', [
        'email' => $email,
        'product_id' => $product_id,
        'size' => $size,
        'color' => $color,
        'language' => $language,
        'status' => 0
    ]);
    
    if (!$exist) {
        // Создаем запись
        $object = $modx->newObject('stikSizesubscriber');
        
        $hash = $email.$product_id.$size.$color.$language;
        
        $active = 0; // подписка не активна по умолчанию
        $profile = $modx->user->getOne('Profile');
        $profile_email =  $profile ? $profile->get('email') : '';
        if (!empty($email) && (trim($email) == trim($profile_email))) $active = 1; // активируем, если пользователь авторизован и email совпадает
        
        $array = [
            'email' => $email,
            'product_id' => $product_id,
            'user_id' => $modx->getLoginUserID($modx->context->key) ?: 0,
            'size' => $size,
            'color' => $color,
            'language' => $language,
            'createdon' => time(),
            'hash' => crypt($hash, md5($hash)),
            'active' => $active,
        ];
            
        $object->fromArray($array);
        $object->save();
        
        // Отправляем пользователю письмо с подтверждением 
        if ($email) {
            $params = [
                'activated' => $active,
            ];
            $subject = $scriptProperties['subject'];
            $chunk = $scriptProperties['tpl'];
            $stik->sendEmail($email, $subject, $chunk, array_merge($array, $params));
        }
    } else {
        return $AjaxForm->error($modx->lexicon('stik_size_subscribe_err_subscribed'));
    }
}
return $AjaxForm->success($modx->lexicon('stik_size_subscribe_success_message'));