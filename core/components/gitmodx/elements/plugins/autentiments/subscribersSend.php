<?php
/** @var $modx gitModx */
switch ($modx->event->name) {
    case "OnModificationRemainsUpdate":
        /** @var ModificationRemain $object */
        if($object->get('remains') == 0)
            break;
        $sms = $modx->getService("sms", "sms", MODX_CORE_PATH . "components/sms/model/sms/");
        $sms->initialize();
        $sms->mode = "user";
        $subscribers = $modx->getCollection('ModificationSubscriber',['modification_id'=>$object->get('modification_id')]);
        /** @var Modification $modification */
        $modification = $object->getOne('Modification');
        /** @var msProduct $product */
        $product = $modification->getOne('Product');
        $size = $modification->getDetail('size');
        $color = $modification->getDetail('color');
        /** @var ModificationSubscriber $subscriber */
        foreach ($subscribers as $subscriber){
            $phone = $sms->clearPhone($subscriber->get('phone'));

            $text = $modx->lexicon('size_in_stock_phone',[
                'product'=>$product->get('pagetitle'),
                'size'=> $size->get('value'),
                'color'=> $color->get('value'),
                'url'=> $product->getPreviewUrl()
            ]);
            if($subscriber->remove()){

                $sms->sendSms(urlencode($text), $phone);
            }
        }

        break;
}
