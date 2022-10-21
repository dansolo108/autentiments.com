<?php
/** @var AjaxForm $AjaxForm */
$require = ['name'=>'вы не заполнили имя','email'=>'вы не заполнили почту','phone'=>'вы не заполнили телефон','surname'=>'вы не заполнили фамилию','certificate'=>'вы не выбрали сертификат'];
foreach ($require as $key => $message) {
    if (empty($_POST[$key])) {
        return $AjaxForm->error($message, array(
            $key=>$message
        ));
    }
}

/** @var gitModx $modx */
/** @var miniShop2 $ms2 */
$ms2 = $modx->getService('minishop2');
$ms2->initialize('web',array('json_response' => true,'return_response'=>true));
$oldOrder = $_SESSION['minishop2']['order'];
$_SESSION['minishop2']['order'] = [];

$oldCart = $_SESSION['minishop2']['cart'];
$_SESSION['minishop2']['cart'] = [];

$ms2->cart->add($_POST['certificate']);
$ms2->order->set($_POST);
$ms2->order->add('delivery',8);
$ms2->order->add('payment',5);
$response = $ms2->handleRequest('order/submit');
$_SESSION['minishop2']['order'] = $oldOrder;
$_SESSION['minishop2']['cart'] = $oldCart;

return $response;
