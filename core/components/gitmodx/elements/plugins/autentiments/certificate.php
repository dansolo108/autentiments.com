<?php
/** @var $modx gitModx */
switch ($modx->event->name) {
    case "msOnChangeOrderStatus":
        /** @var $status int */
        if ($status !== 2) {
            return;
        }
        /** @var $order msOrder */
        $products = $order->getMany('Products');
        foreach ($products as $product) {
            $modification = $modx->getObject('Modification', $product->get('modification_id'));
            if ($modification && $modification->get('code') && $modification->get('product_id') == 709) {
                /** @var maxma $maxma */
                $maxma = $modx->getService('maxma', 'maxma', $modx->getOption('core_path') . 'components/stik/model/', []);
                $response = $maxma->createGiftCard($modification->get('code'));
                if ($response && $giftCard = $response['giftCard']) {
                    /** @var sms $sms */
                    $sms = $modx->getService("sms", "sms", MODX_CORE_PATH . "components/sms/model/sms/");
                    $sms->initialize();
                    $sms->mode = "user";
                    $address = $order->getOne("Address");
                    if (empty($address) || empty($phone = $address->get('phone')))
                        return;
                    $phone = $sms->clearPhone($phone);
                    $text = "Поздравляем с приобретением  подарочного сертификата на сумму {$giftCard['initAmount']} RUB. \nКод сертификата: {$giftCard['code']}";
                    $sms->sendSms(urlencode($text), $phone);
                    $options = $product->get('options');
                    if(empty($options)){
                        $options = [];
                    }
                    $options['code'] = $giftCard['code'];
                    $product->set('options',$options);
                }
            }
        }
        break;
}
