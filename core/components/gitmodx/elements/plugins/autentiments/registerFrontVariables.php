<?php
/** @var gitModx $modx */
// регистрируем переменные для js
$modx->regClientStartupHTMLBlock('<script type="text/javascript">
    var ms2_frontend_currency = "'.$modx->getPlaceholder('msmc.symbol_right').'",
        stik_order_delivery_not_calculated = "'.$modx->lexicon('stik_order_delivery_not_calculated').'",
        stik_order_delivery_free = "'.$modx->lexicon('stik_order_delivery_free').'",
        stik_order_delivery_impossible_calculate = "'.$modx->lexicon('stik_order_delivery_Impossible_calculate').'",
        stik_order_need_to_accept_terms = "'.$modx->lexicon('stik_order_need_to_accept_terms').'",
        stik_order_fill_required_fields = "'.$modx->lexicon('stik_order_fill_required_fields').'",
        stik_basket_not_enough = "'.$modx->lexicon('stik_basket_not_enough').'",
        stik_declension_bonuses_js = '.$modx->lexicon('stik_declension_bonuses_js').',
        intlTelErrorMap = '.$modx->lexicon('stik_intltel_errors_js').';
</script>');
