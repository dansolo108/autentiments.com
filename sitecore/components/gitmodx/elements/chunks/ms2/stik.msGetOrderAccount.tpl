{*set $coupon = (('!pdoResources' | snippet : [
    'class' => 'mspcOrder',
    'loadModels' => 'msPromoCode',
    'innerJoin' => [
        'class' => 'mspcCoupon',
        'alias' => 'mspcCoupon',
        'on' => 'mspcCoupon.id = mspcOrder.coupon_id',
    ],
    'select' => [
        'mspcOrder' => 'code, discount_amount',
    ],
    'where' => [
        'mspcOrder.order_id' => $order['id'],
    ],
    'sortby' => '{"id":"ASC"}',
    'return' => 'json',
]) | fromJSON)}
{if $coupon?}
    {set $coupon = $coupon[0]}
{/if*}
{set $currency = $order.properties.msmc.symbol_right ?: 'ms2_frontend_currency' | lexicon}
{set $cid = $order.properties.msmc.id ?: 1}
{set $cource = $order.properties.msmc.val ?: 0}
{set $msloyalty = $order.properties.msloyalty ?: 0}
<main class="au-order">
    <div class="au-profile__nav">
        <div class="au-profile__nav-row">
            <h1 class="au-h1  au-profile__title">
                <a class="au-order__title-link" href="{11|url}#purchases">{'stik_order_list_number' | lexicon} № {$order.num}</a>
            </h1>
            <div class="au-profile__tabs">
                <a class="au-profile__tab " href="{11|url}">{'stik_order_title_data' | lexicon}</a>
                <a class="au-profile__tab" href="{11|url}#purchases" data-tab="purchases">{'stik_order_title_purchases' | lexicon}</a>
            </div>
            <a class="au-profile__output" href="{10 | url : [] : ['service' => 'logout']}">
                {'stik_profile_logout' | lexicon}
                <svg width="16" height="16" viewBox="0 0 16 16" fill="none" xmlns="http://www.w3.org/2000/svg">
                    <path fill-rule="evenodd" clip-rule="evenodd" d="M1.99609 1.99609H8.99815V2.99609H2.99609V12.9974H8.99815V13.9974H1.99609V1.99609Z" fill="#1A1714"></path>
                    <path fill-rule="evenodd" clip-rule="evenodd" d="M11.2415 4.98528L14.1096 7.99677L11.2415 11.0083L10.5174 10.3186L12.2525 8.49677H6.53027V7.49677H12.2525L10.5174 5.67493L11.2415 4.98528Z" fill="#1A1714"></path>
                </svg>  
            </a>
        </div>
    </div>
    <div class="au-order__content  page-container">
        <div class="au-order__col  au-order__col_details">
            <h2 class="au-h2  au-order__subtitle">{'stik_order_details_title' | lexicon}</h2>
            <table class="au-cart__modal-table  au-order__table">
                <tr class="au-cart__modal-tr  au-cart__modal-tr_total  au-order__tr-status">
                    <td class="au-cart__modal-td">{'stik_order_list_status' | lexicon}</td>
                    <td class="au-cart__modal-td">{('stik_order_status_' ~ $order.status) | lexicon}</td>
                </tr>
                {set $deliveryCost = '!msMultiCurrencyPrice' | snippet : [
                    'price' => $total.delivery_cost,
                    'cid' => $cid,
                    'cource' => $cource,
                ]}
                {if $order.properties.order_discount}
                    {*set $orderDiscount = '!msMultiCurrencyPrice' | snippet : [
                        'price' => $order.properties.order_discount,
                        'cid' => $cid,
                        'cource' => $cource
                    ]*}
                    {set $beforeDiscount = '!msMultiCurrencyPrice' | snippet : [
                        'price' => ($order.cart_cost + $order.properties.order_discount),
                        'cid' => $cid,
                        'cource' => $cource
                    ]}
                {else}
                    {set $beforeDiscount = '!msMultiCurrencyPrice' | snippet : [
                        'price' => $order.cart_cost,
                        'cid' => $cid,
                        'cource' => $cource
                    ]}
                {/if}
                {set $totalCost = '!msMultiCurrencyPrice' | snippet : [
                    'price' => $total.cost,
                    'cid' => $cid,
                    'cource' => $cource
                ]}
                {if $order.status != 4}
                <tr class="au-cart__modal-tr  au-cart__modal-tr_total">
                    <td class="au-cart__modal-td">{'stik_order_info_paid' | lexicon}</td>
                    <td class="au-cart__modal-td">{if $order.status == 2 || $order.status == 3}{$totalCost}{else}0{/if} {$currency}</td>
                </tr>
                {/if}
                <tr class="au-cart__modal-tr">
                    <td class="au-cart__modal-td">{'stik_order_info_cart_cost' | lexicon}</td>
                    <td class="au-cart__modal-td">{$beforeDiscount} {$currency}</td>
                </tr>
                {if $order.properties.order_discount}
                    <tr class="au-cart__modal-tr">
                        <td class="au-cart__modal-td">{'stik_order_info_discount' | lexicon}</td>
                        <td class="au-cart__modal-td">- {$order.properties.order_discount} {$currency}</td>
                    </tr>
                {/if}
                {if $total.delivery_cost}
                    <tr class="au-cart__modal-tr">
                        <td class="au-cart__modal-td">{'stik_order_info_delivery_cost' | lexicon}</td>
                        <td class="au-cart__modal-td">{$deliveryCost} {$currency}</td>
                    </tr>
                {/if}
                {if $order.properties.msloyalty?}
                    {set $msloyalty = '!msMultiCurrencyPriceFloor' | snippet : [
                        'price' => $msloyalty,
                        'cid' => $cid,
                        'cource' => $cource
                    ]}
                    <tr class="au-cart__modal-tr">
                        <td class="au-cart__modal-td">{'stik_order_info_bonuses' | lexicon}</td>
                        <td class="au-cart__modal-td">-{$msloyalty}</td>
                    </tr>
                {/if}
            </table>
            <div class="au-order__delivery">
                <span class="au-order__delivery-title">{'stik_order_delivery_title' | lexicon}</span>
                <p class="au-order__delivery-text">— {('stik_order_delivery_' ~ $delivery.id) | lexicon}{if $order.properties.order_rates}, {$order.properties.order_rates}{/if};</p>
                <p class="au-order__delivery-text">— {$address.street} {$address.building}{$address.room ? ('-'~$address.room~',') : ''} {$address.city} {$address.index}, {$address.country}.</p>
            </div>
        </div>
        
        <div class="au-order__col  au-order__col_cards">
            <h2 class="au-h2  au-order__subtitle">
                {'stik_order_products_title' | lexicon}
                <span class="au-order__count">{$total.cart_count}</span>
            </h2>
            <ul class="au-cart__cards  au-order__cards">
                {foreach $products as $product}
                    {$_modx->getChunk('order.item', $product)}
                {/foreach}
            </ul>
        </div>
    </div>
</main>