{if !empty($currencies)}
    <div class="msmc msmc--light">
        <a href="#" class="msmc-toggle">
            <span>{$userCurrency['symbol_right']}</span>{$userCurrency['name']}
        </a>
        <div class="msmc-dropdown">
            {foreach $currencies as $currency}
                    <a href="#" class="msmc-dropdown-item {if $currency['id'] == $userCurrencyId} msmc-dropdown-item--active{/if}" data-id="{$currency['id']}">
                        <span>{$currency['symbol_right']}</span>{$currency['name']}
                    </a>
            {/foreach}
        </div>
    </div>
{/if}