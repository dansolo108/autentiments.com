{set $currency = $order.properties.msmc.symbol_right ?: 'ms2_frontend_currency' | lexicon}
{set $cid = $order.properties.msmc.id ?: 1}
{set $cource = $order.properties.msmc.val ?: 0}

<div class="au-payment-end__col">
    <h1 class="au-h1  au-payment-end__title">{$_modx->resource.pagetitle}</h1>
    <p class="au-payment-end__text">{'stik_order_end_text1' | lexicon}</p>
    <p class="au-payment-end__text">{'stik_order_end_text2' | lexicon}</p><br>
    <p class="au-payment-end__text">Как только товар будет готов к выдаче в шоу-руме, вы получаете смс-сообщение на номер, указанный при оформлении заказа.</p>
</div>
<div class="au-payment-end__col  au-payment-end__col_order">
    <h2 class="au-payment-end__subtitle">{'stik_order_list_number' | lexicon} № {$order.num}</h2>
    <table class="au-cart__modal-table  au-order__table">
        <tr class="au-cart__modal-tr  au-cart__modal-tr_total  au-order__tr-status">
            <td class="au-cart__modal-td">{('stik_order_status_' ~ $order.status) | lexicon}</td>
            <td class="au-cart__modal-td">{'!msMultiCurrencyPrice' | snippet : ['price' => $total.cost, 'cid' => $cid, 'cource' => $cource]}{$currency}</td>
        </tr>
    </table>
    <div class="au-order__delivery  au-payment-end__delivery">
        <span class="au-order__delivery-title">{'stik_order_delivery' | lexicon}</span>
        <span class="au-order__delivery-title">
            {'!msMultiCurrencyPrice' | snippet : [
                'price' => $total.delivery_cost,
                'cid' => $cid,
                'cource' => $cource,
            ]}{$currency}
        </span>
        <p class="au-order__delivery-text">—{('stik_order_delivery_' ~ $delivery.id) | lexicon}{if $order.properties.order_rates}, {$order.properties.order_rates}{/if};</p>
        <p class="au-order__delivery-text">—{$address.street} {$address.building}{$address.room ? ('-'~$address.room~',') : ''} {$address.city} {$address.index}, {$address.country}.</p>
        <a class="au-payment-end__details" href="{29|url : [] : ['msorder' => $.get['msorder']]}">{'stik_order_details_link' | lexicon}</a>
    </div>
</div>
<div class="au-payment-end__link-box">
    <a class="au-btn  au-payment-end__link" href="/">{'stik_order_home_link' | lexicon}</a>
</div>