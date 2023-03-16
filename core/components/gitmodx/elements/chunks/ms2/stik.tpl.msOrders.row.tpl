{set $msmcCost = '!msMultiCurrencyPrice' | snippet : [
    'price' => $cost,
    'cid' => $properties.msmc.id ?: 1,
    'cource' => $properties.msmc.val ?: 0,
]}

<li class="au-profile__order">
    <a class="au-profile__order-link" href="{29 | url : [] : ['msorder' => $id]}">
        <span class="au-profile__order-td  au-profile__order-number">№ {$num}</span>
        <span class="au-profile__order-td  au-profile__order-date">{$createdon | date : 'd.m.Y'}</span>
        <span class="au-profile__order-td  au-profile__order-price">{$msmcCost | priceFormat} {$properties.msmc.symbol_right ?: 'ms2_frontend_currency' | lexicon}</span>
        <div class="au-profile__order-td  au-profile__order-details">
            <span class="au-desktop_xl">{'stik_order_list_details' | lexicon}</span>
        </div>
    </a>
</li>