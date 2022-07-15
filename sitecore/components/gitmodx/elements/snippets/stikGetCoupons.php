<?php
if(!$order || !$product){
    return;
}
$collection = $modx->getCollection('stikCoupon',['order_id'=>$order,'product_id'=>$product]);
$output = [];
foreach($collection as $coupon){
    $output[] = [
            'code'=>$coupon->get('code'),
            'amount'=>$coupon->get('amount'),
        ];
}
return $output;