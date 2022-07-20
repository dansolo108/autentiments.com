{set $template = $_modx->resource.template}
<script>
    PageInfo.products['{$key}'] = {$id | getJSONPageInfo};
</script>
<li class="au-cart__card {if $template == 11}au-order__card{/if}" id="{$key}" data-product="{$id}" >
    {* для расчета корректной стоимости корзины с примененным купоном *}
    <input type="hidden" name="price" value="{$real_price = $id | resource : 'price'}">
    <div class="au-cart__img-box">
        <picture>
            <source type="image/webp" srcset="{$cart | replace : ['.jpg', '/cart/'] : ['.webp', '/cart_webp/']}">
            <img class="au-cart__img" src="{$cart}" width="81" height="109" alt="">
        </picture>
    </div>
    <div class="au-cart__description">
        {if $template == 11}
                {if ($id | resource : 'template') == 23}
                    <ul>
                    {set $cpns = ('!stikGetCoupons' | snippet : [
                        'order'=>$.get['msorder'],
                        'product'=>$id,
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
                    <button class="au-cart__del" type="submit" name="ms2_action" value="cart/remove">{'stik_basket_delete' | lexicon}</button>
                </form>
            </div>
        {/if}
        <p class="au-cart__card-title">{$pagetitle}</p>
        <div class="au-card__price-box  au-card__cart-price">
            <span class="au-card__price price"><span>{'!msMultiCurrencyPrice' | snippet : ['price' => $price]}</span> {$_modx->getPlaceholder('msmc.symbol_right')}</span>
            <span class="au-card__price  au-card__price_old old_price" {if (!$options.old_price || $options.old_price <= ($price |preg_replace:'/[^0-9]|/': ''))}style="display:none;"{/if}><span>{'!msMultiCurrencyPrice' | snippet : ['price' => $options.old_price]}</span> {$_modx->getPlaceholder('msmc.symbol_right')}</span>
        </div>
        <div class="au-cart__row">
            <div class="au-cart__sub-row">
                {set $hex = 'msoGetColor' | snippet : ['input' => $options.color]}
                {set $colorId = 'msoGetColor' | snippet : ['input' => $options.color, 'return_id' => true]}
                <div class="au-cart__color" {if $hex == '#ffffff'}data-color="white"{/if} style="background: {$hex};" title="{('stik_color_'~$colorId) | lexicon}"></div>
                <span class="au-cart__size">{$options.size}</span>
                {if $template == 11}
                    <span class="au-order__card-count">{$count} {'stik_product_count_unit' | lexicon}</span>
                {/if}
            </div>
            {if $template != 11}
                {if isset($options['max_count']) && $options['max_count'] == 0}
                     {'stikpr_out_of_stock' | lexicon}
                {else}
                <form method="post" class="au-cart__card-count-form ms2_form" role="form">
                    <input type="hidden" name="key" value="{$key}"/>
                    <button class="btn btn-sm" type="submit" name="ms2_action" value="cart/change">&#8635;</button>
                    
                    <button class="au-cart__minus" type="button" aria-label="{'stik_count_minus' | lexicon}"></button>
                    <span>
                        <input class="au-cart__card-count" type="number" name="count" value="{$count}" {if isset($options['max_count'])}max="{$options['max_count']}"{else}max="999"{/if}>
                    </span>
                    <button class="au-cart__plus" type="button" aria-label="{'stik_count_plus' | lexicon}"></button>
                </form>
                {/if}
            {/if}
        </div>
    </div>
    <a class="au-full-link" href="{$id|url}" aria-label="{$pagetitle}"></a>
</li>