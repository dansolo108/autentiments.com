<div class="au-product__size-item">
    <input class="au-product__size-input" type="radio" name="id" value="{$id}" id="size_{$size}" data-value="{$size}" {if !($remains > 0) || $soon}disabled{/if} {if $activeSize == $size || (!($activeSize is set) && $idx == 0)}checked{/if}>
    <label class="au-product__size {if !($remains > 0) || $soon}not-size{/if}" for="size_{$size}" >
        {$size | uppercase}
    </label>
</div>