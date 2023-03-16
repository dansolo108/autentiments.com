{block 'params'}
    {set $wrapper_classes = 'au-card  au-scroll-animat'}
    {if $_modx->resource.template == 3}
        {set $wrapper_classes = 'au-card'}
    {/if}
{/block}
<script>
    PageInfo.products['{$product_id}'] = {$product_id | getJSONPageInfo};
</script>
<form class="{$wrapper_classes}" id="product-{$product_id}-{$idx}" product-id="{$product_id}">
    <a class="au-card__like msfavorites" href="" aria-label="Добавить в избранное" data-click data-data-list="default" data-data-type="resource" data-data-key="{$product_id}" {if $_modx->resource.template == 5}data-msfavorites-mode="list"{/if}></a>
    <a class="au-card__link" href="{$product_id | url}?{$color?'color='~$color:''}">
        <div class="au-card__img-box">
            <div class="au-card__gallery js_card-img">
                {'!msGallery' | snippet : [
                    'product' => $product_id,
                    'tpl' => 'stik.msGallery.card',
                    'where' => [
                        'description' => $color,
                    ],
                ]}
            </div>
            {if $new || $sale || $soon}
                <div class="au-card__marks">
                    {if $new?}
                        <span class="au-card__mark">New</span>
                    {/if}
                    {if $sale?}
                        {set $discount = $old_price | discount : $price}
                        {if $discount > 0}
                            <span class="au-card__mark">Sale -{$discount}%</span>
                        {/if}
                    {/if}
                    {if $soon?}
                        <span class="au-card__mark">Soon</span>
                    {/if}
                </div>
            {/if}
        </div>

        <div class="au-card__color-box">
            <div class="au-card__colors">
                {'!getProductDetails' | snippet : [
                    'details'=>'color',
                    'id'=>$product_id,
                    'productidx'=>$idx,
                    'active'=>$color,
                    'tpl'=>'product.row.color',
                ]}
            </div>
        </div>
        <div class="au-card__description">
            <span class="au-card__title">{$pagetitle}</span>
            <div class="au-card__price-box js_card-prices">
                {$_modx->getChunk('product.row.price',['old_price'=>$old_price,'price'=>$price])}
            </div>
        </div>
    </a>
</form>