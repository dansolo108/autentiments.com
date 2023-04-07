{var $key = $filter}
<li class="au-filter__item">
    <span>
        <input class="au-filter__checkbox" type="checkbox" name="{$filter_key}" id="mse2_{$key}_{$idx}" value="{$value}" {$checked} {$disabled} form="mse2_filters"/>
        <label class="au-filter__label" for="mse2_{$key}_{$idx}">
            {if $filter_key == 'color'}
                {set $colorId = 'msoGetColor' | snippet : ['input' => $value, 'return_id' => true]}
                {('stik_color_'~$colorId) | lexicon}
            {else}
                {$title}
            {/if}
        </label>
    </span>
</li>