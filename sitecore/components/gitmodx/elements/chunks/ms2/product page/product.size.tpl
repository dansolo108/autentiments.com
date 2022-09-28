<div class="au-product__size-item">
    <input class="au-product__size-input" type="radio" name="id" value="{$id}" id="size_{$size}" data-value="{$size}" {if !($remains > 0)  || $hide_remains || $soon}disabled{/if} {if $activeSize == $size || (!($activeSize is set) && $idx == 0)}checked{/if}>
    <label class="au-product__size {if $activeSize == $size || (!($activeSize is set) && $idx == 0)}active{/if} {if !($remains > 0) || $hide_remains || $soon}not-size{/if}" for="size_{$size}">
        {$size | uppercase}
    </label>
</div>