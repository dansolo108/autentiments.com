<?php
/** @var $scriptProperties array */
/** @var $modx gitModx */
switch ($modx->event->name) {
    case 'msOnCreateOrder':
        $msOrder = $modx->getOption('msOrder', $scriptProperties, null);
        $orderProducts = $msOrder->getMany('Products');
        if(empty($orderProducts))
            break;
        foreach ($orderProducts as $orderProduct) {
            $modification_id = $orderProduct->get('modification_id');
            if($modification_id && $modification = $modx->getObject('Modification',$modification_id)){
                $remains = $modification->get('Remains');
                foreach ($remains as $remain) {
                    if($remain->get('remains') > 0 ){
                        $remain->set('remains',$remain->get('remains') - 1);
                        $remain->save();
                        break;
                    }
                }
            }
        }
        break;
}
return;
