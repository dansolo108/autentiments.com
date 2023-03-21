{set $url = ($product_id | url : ["scheme"=>-1] : ['size' => $size,"color"=>$color])}
<form class="ms2_form auten-cart-item" id="{$key}">
    <input type="hidden" value="{$key}" name="key">
    <a class="auten-cart-item__image" href="{$url}">
        <picture>
            <source type="image/webp" srcset="{$thumbs[0]['cart'] | replace : ['.jpg', '/cart/'] : ['.webp', '/cart_webp/']}">
            <img src="{$thumbs[0]['cart']}" width="81" height="109" alt="">
        </picture>
    </a>
    <a class="auten-cart-item__title" href="{$url}">
        {$pagetitle}
    </a>
    <button type="submit" value="cart/remove" name="ms2_action" class="auten-cart-item__delete">
            удалить
    </button>
    {if $article}
        <a class="auten-cart-item__article" href="{$url}">
            {'stik_basket_article' | lexicon}. {$article}
        </a>
    {/if}
    <div class="auten-cart-item__prices">
        <div class="auten-cart-item__price">
            <span class="ms_price">{'!msMultiCurrencyPrice' | snippet : ['price' => $price]}</span> {$_modx->getPlaceholder('msmc.symbol_right')}
        </div>
        {if $old_price}
        <div class="auten-cart-item__old-price">
            <span class="ms_old_price">{'!msMultiCurrencyPrice' | snippet : ['price' => $old_price]}</span> {$_modx->getPlaceholder('msmc.symbol_right')}
        </div>
        {/if}
    </div>
    <div class="auten-cart-item__params">
        {set $hex = 'msoGetColor' | snippet : ['input' => $color]}
        {set $colorId = 'msoGetColor' | snippet : ['input' => $color, 'return_id' => true]}
        <div class="auten-cart-item__color" title="{('stik_color_'~$colorId) | lexicon}" style="background-color: {$hex};"></div>
        {set $sizes = '!getProductDetails' | snippet : [
            'details'=>['size'],
            'id'=>$product_id,
        ]}
        <div class="auten-select">
            {if count($sizes) > 1}
            <div class="auten-select__items">
                {foreach $sizes as $sz}
                    <label class="auten-select-item"><input type="radio" name="size" value="{$sz.value}">{$sz.value}</label>
                {/foreach}
            </div>
            {/if}
            <div class="auten-select__active">{$size}</div>
        </div>
        <div class="ms2_form auten-counter auten-cart-item__amount" >
            <button type="submit" name="ms2_action" value="cart/change" class="auten-counter__button" data-count="-1">–</button>
            <input class="auten-counter__input" name="count" min="1" value="{$count}" {if isset($remains)}max="{$remains}"{else}max="999"{/if}>
            <button type="submit" name="ms2_action" value="cart/change" class="auten-counter__button" data-count="1">+</button>
        </div>
    </div>
</form>
