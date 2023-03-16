<section class="auten-cart" data-step="cart">
    <div class="auten-cart__title">
        Корзина (<span class="ms2_total_count">{$total.count}</span>)
    </div>
    <div class="auten-cart__items">
        {foreach $products as $product}
            {$_modx->getChunk('cart.item', $product)}
            {set $productIds[] = $product.id}
        {/foreach}
    </div>
</section>