{set $hex = 'msoGetColor' | snippet : ['input' => $value]}
{set $valueId = 'msoGetColor' | snippet : ['input' => $value, 'return_id' => true]}
<div class="au-card__color-item">
    <div class="au-card__color-item">
        {set $unique_id = 'color_'~$valueId~'_'~$productidx~'_'~$idx}
        <input class="au-card__color-input" hidden type="radio" name="color" value="{$value}" id="{$unique_id}" data-product="{$product_id}" {if $active == $value}checked{/if}>
        <label class="au-card__color" style="background: {$hex};" for="{$unique_id}" {if $hex == '#ffffff'}data-color="white"{/if} title="{('stik_color_'~$valueId) | lexicon}">
        </label>
    </div>
</div>

