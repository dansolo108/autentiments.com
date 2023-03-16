{if !empty($currencies)}
    <table class="table">
        <tbody>
        {foreach $currencies as $currency}
            {if $currency.id != $userCurrencyId}
                <tr>
                    <td>
                        <span><span>{$currency.price}</span> {$currency[$symbol]}</span>
                        <span {$currency.old_price ? '' : 'style="display:none"'}><span>{$currency.old_price}</span> {$currency[$symbol]}</span>
                    </td>
                </tr>
            {/if}
        {/foreach}
        </tbody>
    </table>
{/if}