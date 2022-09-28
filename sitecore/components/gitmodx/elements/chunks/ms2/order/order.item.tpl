{set $article = $code?:$article}
<li class="au-cart__card au-order__card" id="{$key}" data-product="{$product_id}" >
    {* для расчета корректной стоимости корзины с примененным купоном *}
    <input type="hidden" name="price" value="{$real_price = $product_id | resource : 'price'}">
    <div class="au-cart__img-box">
        <picture>
            <source type="image/webp" srcset="{$cart | replace : ['.jpg', '/cart/'] : ['.webp', '/cart_webp/']}">
            <img class="au-cart__img" src="{$cart}" width="81" height="109" alt="">
        </picture>
    </div>
    <div class="au-cart__description">
        {if $article}
            <span class="au-cart__article">{'stik_basket_article' | lexicon}: {$article} </span>
        {/if}
        <p class="au-cart__card-title">{$pagetitle}</p>
        <div class="au-card__price-box  au-card__cart-price">
            <span class="au-card__price price"><span>{'!msMultiCurrencyPrice' | snippet : ['price' => $price]}</span> {$_modx->getPlaceholder('msmc.symbol_right')}</span>
            <span class="au-card__price  au-card__price_old old_price" {if (($old_price | preg_replace:'/[^0-9]|/': '') <= ($price | preg_replace:'/[^0-9]|/': ''))}style="display:none;"{/if}><span>{'!msMultiCurrencyPrice' | snippet : ['price' =>  $old_price]}</span> {$_modx->getPlaceholder('msmc.symbol_right')}</span>
        </div>
        <div class="au-cart__row">
            <div class="au-cart__sub-row">
                {set $hex = 'msoGetColor' | snippet : ['input' => $options.color]}
                {set $colorId = 'msoGetColor' | snippet : ['input' => $options.color, 'return_id' => true]}
                <div class="au-cart__color" {if $hex == '#ffffff'}data-color="white"{/if} style="background: {$hex};" title="{('stik_color_'~$colorId) | lexicon}"></div>
                <span class="au-cart__size">{$options.size}</span>
                <span class="au-order__card-count">{$count} {'stik_product_count_unit' | lexicon}</span>
            </div>
        </div>
    </div>
    <a class="au-full-link" href="{$product_id|url}?{$options.size?'size='~$options.size~'&':''}{$options.color?'color='~$options.color:''}" aria-label="{$pagetitle}"></a>
</li>