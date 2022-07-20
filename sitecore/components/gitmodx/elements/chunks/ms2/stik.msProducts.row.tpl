{block 'params'}
    {set $wrapper_classes = 'au-card  au-scroll-animat'}
    {if $_modx->resource.template == 3}
        {set $wrapper_classes = 'au-card'}
    {/if}
{/block}


{if is_array($color)}
    {set $activeColor = $color[0]}
{else}
    {set $activeColor = $color}
{/if}
{set $availableColors = '!getAvailableColors' | snippet : [
    'tpl' => 'stik.msOptions.card',
    'id' => $id,
    'idx'=>$idx,
    'active'=>$activeColor,
]}
<script>
    PageInfo.products['{$id}'] = {$id | getJSONPageInfo};
</script>
<form class="{$wrapper_classes}" id="product-{$id}-{$idx}">
    <a class="au-card__like msfavorites" href="" aria-label="Добавить в избранное" data-click data-data-list="default" data-data-type="resource" data-data-key="{$id}" {if $_modx->resource.template == 5}data-msfavorites-mode="list"{/if}></a>
    <a class="au-card__link" href="{$id | url}">
        <div class="au-card__img-box">
            <div class="au-card__gallery js_card-img">
                {'!msGallery' | snippet : [
                    'product' => $id,
                    'tpl' => 'stik.msGallery.card',
                    'where' => [
                        'description' => $activeColor,
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
        {$availableColors}
        <div class="au-card__description">
            <span class="au-card__title">{$pagetitle}</span>
            <div class="au-card__price-box js_card-prices">
                {'!getColorPrice' | snippet : [
                    'id' => $id,
                    'color' => $activeColor,
                    'tpl' => 'stik.cardPrices.tpl',
                ]}
            </div>
        </div>
    </a>
</form>