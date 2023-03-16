{if $options?}
    {foreach $options as $size => $remain}
        <div class="au-product__size-item">
            <input class="au-product__size-input" type="radio" name="options[size]" value="{$size}"
                id="{$size}" {if $remain == 0}disabled=""{/if} {*if $options | count == 1 && ($remain != 0 && !$_modx->resource.soon)}checked{/if*}>
            <label class="au-product__size{if $remain == 0 || $_modx->resource.soon} not-size{/if}" for="{$size}">
                {$size | uppercase}
            </label>
        </div>
    {/foreach}
{/if}