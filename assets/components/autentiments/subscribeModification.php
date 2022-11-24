<?php
define('MODX_API_MODE', true);
require_once dirname(__FILE__, 4) . '/index.php';
/** @var gitModx $modx */
$modification_id = $_POST['modification_id'];
if(empty($modification_id)){
    exit(json_encode([
        'success'=> false,
        'message'=> 'Неизвестная ошибка',
    ]));
}
$phone = $_POST['phone'];
if(empty($phone)){
    $user = $modx->getAuthenticatedUser('web');
    if(empty($user)){
        exit(json_encode([
            'success'=> false,
            'message'=> 'Неизвестная ошибка',
        ]));
    }
    $profile = $user->getOne('Profile');
    $phone = $profile->get('mobilephone');
}
$phone = preg_replace("/[^,.0-9]/", '', $phone);
$modifSub = $modx->getObject('ModificationSubscriber',[
    'phone'=>$phone,
    'modification_id'=>$modification_id,
]);
if($modifSub){
   exit(json_encode([
       'success'=> false,
       'message'=> 'Вы уже подписаны',
   ]));
}
$modifSub = $modx->newObject('ModificationSubscriber',[
    'phone'=>$phone,
    'modification_id'=>$modification_id,
]);
if($modifSub->save()){
    exit(json_encode([
        'success'=> true,
        'message'=> 'Мы уведомим вас, о новых поступлениях.',
    ]));
}
exit(json_encode([
    'success'=> false,
    'message'=> 'Неизвестная ошибка',
]));
