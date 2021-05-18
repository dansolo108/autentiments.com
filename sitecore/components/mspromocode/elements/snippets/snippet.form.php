<?php
/** @var msPromoCode $mspc */
/** @var pdoFetch $pdoFetch */
/** @var array $sp */
$sp = &$scriptProperties;
if (!$modx->loadClass('pdofetch', MODX_CORE_PATH . 'components/pdotools/model/pdotools/', false, true)) {
    return false;
}
$pdoFetch = new pdoFetch($modx, $sp);

$path = MODX_CORE_PATH . 'components/mspromocode/model/mspromocode/';
$mspc = $modx->getService('mspromocode', 'msPromoCode', $path, $sp);
if (!is_object($mspc) || empty($mspc->active)) {
    return;
}
$mspc->initialize($modx->context->key, $sp);
$mspc->ms2->initialize($modx->context->key);
if (!$mspc->ms2->cart->get()) {
    return;
}

$tpl = $modx->getOption('tpl', $sp, 'tpl.mspcForm');

$coupon = $mspc->coupon->getCurrentCoupon();
$code = !empty($coupon) ? $coupon['code'] : '';
$description = !empty($coupon) ? $coupon['description'] : '';

return $pdoFetch->getChunk($tpl, array(
    'coupon' => $code,
    'coupon_description' => $description,
    'discount_amount' => $mspc->discount->getDiscountAmount(),
    'disfield' => $mspc->config['params']['disfield'],
    'btn' => !empty($coupon) ? $modx->lexicon('mspromocode_btn_remove') : $modx->lexicon('mspromocode_btn_apply'),
));