{foreach $options as $name => $values}
    {if $values | iterable}
        <div class="au-card__color-box">
            <div class="au-card__colors">
                {foreach $values as $value index=$index}
                    {set $hex = 'msoGetColor' | snippet : ['input' => $value]}
                    {set $colorId = 'msoGetColor' | snippet : ['input' => $value, 'return_id' => true]}
                    {set $unique_id = 'color_'~$colorId~'_'~$id~$idx}
                    <div class="au-card__color-item">
                        <input class="au-card__color-input" type="radio" name="color_{$id}" value="{$value}" data-product="{$id}" id="{$unique_id}" {if $active == $value}checked{/if}>
                        <label class="au-card__color  {if $active == $value}active{/if}" for="{$unique_id}" style="background: {$hex};" {if $hex == '#ffffff'}data-color="white"{/if} title="{('stik_color_'~$colorId) | lexicon}"></label>
                    </div>
                {/foreach}
            </div>
        </div>
    {/if}
{/foreach}
