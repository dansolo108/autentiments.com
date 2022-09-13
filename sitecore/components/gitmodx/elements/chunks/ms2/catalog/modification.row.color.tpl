{set $hex = 'msoGetColor' | snippet : ['input' => $Цвет]}
{set $colorId = 'msoGetColor' | snippet : ['input' => $Цвет, 'return_id' => true]}
<div class="au-card__color-item">
    <div class="au-card__color-item">
        {set $unique_id = 'color_'~$colorId~'_'~$id~'_'}
        <input class="au-card__color-input" hidden type="radio" name="color" value="{$Цвет}" id="{$unique_id}" data-product="{$product_id}" {if $active == $Цвет}checked{/if}>
        <label class="au-card__color  {if $active == $Цвет}active{/if}" style="background: {$hex};" for="{$unique_id}" {if $hex == '#ffffff'}data-color="white"{/if} title="{('stik_color_'~$colorId) | lexicon}">
        </label>
    </div>
</div>

