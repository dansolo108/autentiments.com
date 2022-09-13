<div class="au-product__size-item">
    <input class="au-product__size-input" type="radio" name="options[size]" value="{$Размер}" id="{$Размер}" {if $activeРазмер == $Размер}checked{/if}>
    <label class="au-product__size" for="{$Размер}">
        {$Размер | uppercase}
    </label>
</div>