{var $key = $table ~ $delimeter ~ $filter}
<label class="au-filter__cost" for="mse2_{$key}_{$idx}">
    <input type="text" name="{$filter_key}" id="mse2_{$key}_{$idx}" value="{$value}"
           data-current-value="{$current_value}" class="au-filter__cost"/>
</label>