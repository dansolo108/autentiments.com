<?php
/** @var gitModx $modx */
/** @var array $scriptProperties */
/** @var msCartHandlerCustom $cart */
$cart = $scriptProperties['cart']->get();
foreach($cart as $key => &$cartModification){
    $filter = ['id' => $cartModification['id']];
    /** @var Modification $modificationObj */
    if($modificationObj = $modx->getObject('Modification',$filter)){
        $cartModification['price'] = $modificationObj->get('price');
        $cartModification['old_price'] = $modificationObj->get('old_price');
        $remains = $modificationObj->getRemains();
        $cartModification['options']['max_count'] = $remains;
        if($cartModification['count'] > $remains)
            $cartModification['count'] = $remains;
        $cartModification['discount_price'] = $cartModification['old_price'] > 0 ? $cartModification['old_price'] - $cartModification['price'] : 0;
        $cartModification['discount_cost'] = $cartModification['discount_price'] * $cartModification['count'];
    }
}
$scriptProperties['cart']->set($cart);
$mspc = $modx->getService('mspromocode');
if($mspc->coupon->current['code'])
    $mspc->discount->refreshDiscountForCart($mspc->coupon->current['code']);
