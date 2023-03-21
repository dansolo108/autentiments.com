{set $template = $_modx->resource.template}
{set $article = $code?:$article}
<script>
    PageInfo.products['{$key}'] = {$product_id | getJSONPageInfo};
</script>
<li class="au-cart__card {if $template == 11}au-order__card{/if}" id="{$key}" data-product="{$product_id}">
    {* для расчета корректной стоимости корзины с примененным купоном *}
    <input type="hidden" name="price" value="{$real_price = $product_id | resource : 'price'}">
    <div class="au-cart__img-box">
        <picture>
            <source type="image/webp"
                    srcset="{$thumbs[0]['cart'] | replace : ['.jpg', '/cart/'] : ['.webp', '/cart_webp/']}">
            <img class="au-cart__img" src="{$thumbs[0]['cart']}" width="81" height="109" alt="">
        </picture>
    </div>
    <div class="au-cart__description">
        {if $template == 11}
            {if ($product_id | resource : 'template') == 23}
                <ul>
                    {set $cpns = ('!stikGetCoupons' | snippet : [
                    'order'=>$.get['msorder'],
                    'product'=>$product_id,
                    ])}
                    {foreach $cpns as $cpn}
                        <li>
                            {$cpn['code']}
                        </li>
                    {/foreach}
                </ul>
                <br>
            {/if}
            {if $article}
                <span class="au-cart__article">{'stik_basket_article' | lexicon}: {$article} </span>
            {/if}
        {else}
            <div class="au-cart__row">
                {if $article}
                    <span class="au-cart__article">{'stik_basket_article' | lexicon}: {$article}</span>
                {/if}
                <form method="post" class="ms2_form text-md-right">
                    <input type="hidden" name="key" value="{$key}">
                    <button class="au-cart__del" type="submit" name="ms2_action"
                            value="cart/remove">{'stik_basket_delete' | lexicon}</button>
                </form>
            </div>
        {/if}
        <p class="au-cart__card-title">{$pagetitle}</p>
        <div class="au-card__price-box  au-card__cart-price">
            <span class="au-card__price price"><span>{'!msMultiCurrencyPrice' | snippet : ['price' => $price]}</span> {$_modx->getPlaceholder('msmc.symbol_right')}</span>
            <span class="au-card__price  au-card__price_old old_price"
                  {if (($old_price | preg_replace:'/[^0-9]|/': '') <= ($price | preg_replace:'/[^0-9]|/': ''))}style="display:none;"{/if}><span>{'!msMultiCurrencyPrice' | snippet : ['price' =>  $old_price]}</span> {$_modx->getPlaceholder('msmc.symbol_right')}</span>
        </div>
        <div class="au-cart__row">
            <div class="au-cart__sub-row">
                {set $hex = 'msoGetColor' | snippet : ['input' => $color]}
                {set $colorId = 'msoGetColor' | snippet : ['input' => $color, 'return_id' => true]}
                <div class="au-cart__color" {if $hex == '#ffffff'}data-color="white"{/if} style="background: {$hex};"
                     title="{('stik_color_'~$colorId) | lexicon}"></div>
                <span class="au-cart__size">{$size}</span>
                {if $template == 11}
                    <span class="au-order__card-count">{$count} {'stik_product_count_unit' | lexicon}</span>
                {/if}
            </div>
            {if $template != 11}
                {if isset($remains) && $remains == 0}
                    {'stikpr_out_of_stock' | lexicon}
                {else}
                    <form method="post" class="au-cart__card-count-form ms2_form" role="form">
                        <input type="hidden" name="key" value="{$key}"/>
                        <button class="btn btn-sm" type="submit" name="ms2_action" value="cart/change">&#8635;</button>
                        <div class="auten-counter" style="display: flex;">
                            <button class="auten-counter__button au-cart__minus" type="button" data-count="-1"
                                    aria-label="{'stik_count_minus' | lexicon}"></button>
                            <input class="auten-counter__input au-cart__card-count" type="number" name="count"
                                   value="{$count}" {if isset($remains)}max="{$remains}" {else}max="999"{/if}>
                            <button class="auten-counter__button au-cart__plus" type="button" data-count="1"
                                    aria-label="{'stik_count_plus' | lexicon}"></button>
                        </div>
                    </form>
                {/if}
            {/if}
        </div>
    </div>
    <a class="au-full-link" href="{$product_id|url}?{$size?'size='~$size~'&':''}{$color?'color='~$color:''}"
       aria-label="{$pagetitle}"></a>
</li>