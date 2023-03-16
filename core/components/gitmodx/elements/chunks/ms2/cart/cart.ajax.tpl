<div class="au-modal__wrapper  au-cart" id="msCart">
    <button class="au-close au-cart__close" aria-label="{'stik_modal_close' | lexicon}"></button>
    <div class="au-modal__content  au-cart__content {if !count($products)}empty-cart{/if}">
        <div class="au-cart__cart">
            <h3 class="au-h1  au-cart__title">
                {'stik_basket_modal_title' | lexicon}
                <span class="au-cart__count ms2_total_count">{$total.count}</span>
            </h3>
            {if $products?}
                <ul class="au-cart__cards">
                    {foreach $products as $product}
                        {$_modx->getChunk('cart.item.old', $product)}
                    {/foreach}
                </ul>
            {/if}
            <div class="au-cart__modal-bottom">
                <table class="au-cart__modal-table">
                    <tr class="au-cart__modal-tr">
                        <td class="au-cart__modal-td">{'stik_order_info_cart_cost' | lexicon}</td>
                        {set $total_no_discount = ($total.cost | replace : ' ' : '') + ($total.discount | replace : ' ' : '')}
                        <td class="au-cart__modal-td"><span class="ms2_total_no_discount">{'!msMultiCurrencyPrice' | snippet : ['price' => $total_no_discount]}</span> {$_modx->getPlaceholder('msmc.symbol_right')}</td>
                    </tr>
                    <tr class="au-cart__modal-tr total_discount_wrapper"{if !$total.discount} style="display:none;"{/if}>
                        <td class="au-cart__modal-td">{'stik_order_info_discount' | lexicon}</td>
                        <td class="au-cart__modal-td">- <span class="ms2_total_discount_custom">{'!msMultiCurrencyPrice' | snippet : ['price' => $total.discount]}</span> {$_modx->getPlaceholder('msmc.symbol_right')}</td>
                    </tr>
                    <tr class="au-cart__modal-tr">
                        <td class="au-cart__modal-td">{'stik_order_info_total_cost' | lexicon}</td>
                        <td class="au-cart__modal-td"><span class="ms2_total_cost">{'!msMultiCurrencyPrice' | snippet : ['price' => $total.cost]}</span> {$_modx->getPlaceholder('msmc.symbol_right')}</td>
                    </tr>
                </table>
                <a class="au-btn" href="{15|url}">{'stik_basket_place_order' | lexicon}</a>
                <button class="au-close  au-btn-light  au-cart__resume">{'stik_basket_continue_shopping' | lexicon}</button>
            </div>
        </div>
        <div class="au-cart__empty">
            <h3 class="au-h1  au-cart__title">{'stik_empty_basket_title' | lexicon}</h3>
            <p class="au-cart__text">{'stik_empty_basket_text' | lexicon}</p>
            <a class="au-btn" href="{7|url}">{'stik_empty_basket_view_catalog' | lexicon}</a>
        </div>
    </div>
</div>
