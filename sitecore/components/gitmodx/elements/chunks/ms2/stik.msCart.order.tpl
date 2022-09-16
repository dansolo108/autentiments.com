<div class="au-ordering__cart" id="msCart">
    <h2 class="au-h2  au-cart__title">
        {'stik_basket_modal_title' | lexicon}
        <span class="au-cart__count ms2_total_count">{$total.count}</span>
    </h2>
    {if count($products)}
        {set $productIds = []}
        <ul class="au-cart__cards">
            {foreach $products as $product}
                {$_modx->getChunk('cart.item', $product)}
                {set $productIds[] = $product.id}
            {/foreach}
        </ul>
        {*<script>
        fbq('track', 'InitiateCheckout',{
            currency: 'RUB',
            num_items:{$total.count},
            content_ids: [
             '{implode("','",$productIds)}'
            ],
            value:{$total.cost|replace:',':'.'|replace:' ':''} })
        </script>*}
        {set $total_no_discount = ($total.cost | replace : ' ' : '') + ($total.discount | replace : ' ' : '')}
        {$_modx->setPlaceholder('cart_total_cost', $total_no_discount)}
        {$_modx->setPlaceholder('cart_discount', $total.discount)}
    {/if}
</div>