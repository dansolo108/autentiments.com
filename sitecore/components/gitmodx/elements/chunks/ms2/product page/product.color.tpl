{set $hex = 'msoGetColor' | snippet : ['input' => $Цвет]}
{set $colorId = 'msoGetColor' | snippet : ['input' => $Цвет, 'return_id' => true]}
<div class="au-product__color-item">
    <input class="au-product__color-input" {if $activeЦвет == $Цвет}checked{/if} type="radio" name="options[color]" value="{$Цвет}" data-product="{$product_id}" id="color_{$colorId}">
    <label class="au-product__color  {if $activeЦвет == $Цвет}active{/if}" for="color_{$colorId}" style="background: {$hex};" {if $hex == '#ffffff'} data-color="white" {/if}title="{('stik_color_'~$colorId) | lexicon}">
    </label>
</div>
