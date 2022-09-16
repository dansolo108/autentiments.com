<?php
// Подписка на размер
if (isset($_GET['ss_hash']) && $_GET['ss_hash']) {
    $object = $modx->getObject('stikSizesubscriber', [
        'hash' => $_GET['ss_hash'],
    ]);
    
    if ($object) {
        $object->set('active', 1);
        $object->save();
    } else {
        return $modx->lexicon('stik_activate_fail');
    }
    return $modx->lexicon('stik_activate_success');
}

// if (isset($_GET['ds_hash']) && $_GET['ds_hash']) {
//     $object = $modx->getObject('discountSubscriber', [
//         'hash' => $_GET['ds_hash'],
//     ]);
    
//     if ($object) {
//         $object->set('active', 1);
//         $object->save();
//     } else {
//         return $modx->lexicon('lcl_activate_subscribe_fail');
//     }
//     return $modx->lexicon('lcl_activate_subscribe_success');
// }

// Подписка на рассылку
if (isset($_GET['ns_hash']) && $_GET['ns_hash']) {
    $object = $modx->getObject('stikSubscriber', [
        'hash' => $_GET['ns_hash'],
    ]);
    
    if ($object) {
        $object->set('active', 1);
        $object->save();
    } else {
        return $modx->lexicon('stik_activate_fail');
    }
    return $modx->lexicon('stik_activate_success');
}

return false;