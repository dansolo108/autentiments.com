<span class="au-card__price">{'!msMultiCurrencyPrice' | snippet : ['price' => $price]} {$_modx->getPlaceholder('msmc.symbol_right')}</span>
{if $old_price?}
    <span class="au-card__price  au-card__price_old">{'!msMultiCurrencyPrice' | snippet : ['price' => $old_price]} {$_modx->getPlaceholder('msmc.symbol_right')}</span>
{/if}