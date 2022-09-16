<span class="au-card__price js_card_price">
    <span>{'!msMultiCurrencyPrice' | snippet : ['price' => $price]}</span>
    {$_modx->getPlaceholder('msmc.symbol_right')}
</span>
<span class="au-card__price  au-card__price_old js_card_old_price" {if !$old_price}style="display:none;"{/if}>
    <span>{'!msMultiCurrencyPrice' | snippet : ['price' => $old_price]}</span>
    {$_modx->getPlaceholder('msmc.symbol_right')}
</span>