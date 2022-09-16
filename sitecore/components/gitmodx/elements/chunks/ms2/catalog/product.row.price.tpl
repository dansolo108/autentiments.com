<span class="au-card__price js_card_price">
    <span>{'!msMultiCurrencyPrice' | snippet : ['price' => $price]}</span>
    {$_modx->getPlaceholder('msmc.symbol_right')}
</span>
{if $old_price > $price}
    <span class="au-card__price  au-card__price_old js_card_old_price">
        <span>{'!msMultiCurrencyPrice' | snippet : ['price' => $old_price]}</span>
        {$_modx->getPlaceholder('msmc.symbol_right')}
    </span>
{/if}