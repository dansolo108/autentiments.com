{foreach $options as $name => $values}
    {if $values | iterable}
        <div class="au-product__color-box">
            <span class="au-product__subtitle">{('ms2_product_' ~ $name) | lexicon}:</span>
            <div class="au-product__colors">
                {foreach $values as $value index=$index}
                    {set $hex = 'msoGetColor' | snippet : ['input' => $value]}
                    {set $colorId = 'msoGetColor' | snippet : ['input' => $value, 'return_id' => true]}
                    {if $index == 0}
                        {$_modx->setPlaceholder('first_color_' ~ $_modx->resource.id, $value)}
                    {/if}
                    <div class="au-product__color-item">
                        <input class="au-product__color-input" type="radio" name="options[{$name}]"
                            value="{$value}" data-product="{$_modx->resource.id}" id="color_{$colorId}"
                            {if ($index == 0 && !$.get['color']) || ($.get['color'] == $value)}checked{/if}>
                        <label class="au-product__color  {if $index == 0}active{/if}" for="color_{$colorId}"
                            style="background: {$hex};" {if $hex == '#ffffff'}data-color="white"{/if}
                                title="{('stik_color_'~$colorId) | lexicon}">
                        </label>
                    </div>
                {/foreach}
            </div>
        </div>
    {/if}
{/foreach}
{if $.get['color']}
    {$_modx->setPlaceholder('first_color_' ~ $_modx->resource.id, $.get['color'])}
{/if}