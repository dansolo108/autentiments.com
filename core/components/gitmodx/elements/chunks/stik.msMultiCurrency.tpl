{if !empty($currencies)}
    <div class="au-header__currency msmc">
        {foreach $currencies as $currency}
            <a class="au-header__currency-link msmc-dropdown-item {if $currency['id'] == $userCurrencyId}active{/if}" data-id="{$currency['id']}" href="#">
                {$currency['symbol_right']} ({$currency['code']})
            </a>
        {/foreach}
    </div>
{/if}