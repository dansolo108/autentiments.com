{set $hex = 'msoGetColor' | snippet : ['input' => $value]}
{set $valueId = 'msoGetColor' | snippet : ['input' => $value, 'return_id' => true]}
<div class="au-product__color-item">
    <input class="au-product__color-input" {if $activeColor == $value}checked{/if} type="radio" name="color" value="{$value}" id="color_{$valueId}">
    <label class="au-product__color  {if $activeColor == $value}active{/if}" for="color_{$valueId}" style="background: {$hex};" {if $hex == '#ffffff'} data-color="white" {/if}title="{('stik_color_'~$valueId) | lexicon}">
    </label>
</div>
