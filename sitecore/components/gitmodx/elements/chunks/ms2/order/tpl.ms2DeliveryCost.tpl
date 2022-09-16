{*$costs | print*}
{*$order | print*}
{set $russia_arr = ['россия','russian federation']}
{set $country_lower = $order.country | lower}
{set $city_lower = $order.city | lower}
{foreach $costs as $delivery index=$index}
    {* Бесплатная доставка у самовывоза и если включена опция по РФ *}
    {set $is_free = ((!$delivery.delivery.price && !$delivery.delivery.class) || ($delivery.delivery.free_delivery_rf == 1 && $country_lower | in : $russia_arr)) ? true : false}
    {set $checked = !$order.delivery && $index == 0 || $delivery.delivery.id == $order.delivery}
    {set $hidden_by_city =  ($delivery.delivery.id == 7 && $city_lower != 'москва' || $delivery.delivery.id == 6 && $city_lower != 'санкт-петербург')}
    {if $delivery.delivery.free_delivery_rf && ($country_lower | in : $russia_arr) || $language == 'ru' && $delivery.delivery.show_on_ru || $language == 'en' && $delivery.delivery.show_on_en}
        {if !$hidden_by_city}
            <div class="au-ordering__delivery-row {$delivery.error? 'disabled':''}">
                <input type="radio" name="delivery" value="{$delivery.delivery.id}" id="delivery_{$delivery.delivery.id}"
                        class="custom-form__radio {$delivery.error === false && $delivery.cost == 0 ? 'free-delivery' : ''}" data-payments="{$delivery.payments | json_encode}"
                        {$checked ? 'checked' : ''}>
                <label class="custom-form__radio-label" for="delivery_{$delivery.delivery.id}">
                    <span class="au-ordering__delivery-name">{('stik_order_delivery_' ~ $delivery.delivery.id) | lexicon}</span>
                    <span class="au-ordering__delivery-info">
                        {if $delivery.error}
                            {'stik_order_delivery_not_calculated' | lexicon}
                        {elseif $delivery.cost > 0}
                            {'!msMultiCurrencyPrice' | snippet : ['price' => $delivery.cost]} {$_modx->getPlaceholder('msmc.symbol_right')}{if $delivery.rates}, <span class="delivery_rate">{$delivery.rates}</span>{/if}
                        {elseif $delivery.cost == 0}
                            {'stik_order_delivery_free' | lexicon}{if $delivery.rates}, <span class="delivery_rate">{$delivery.rates}</span>{/if}
                        {/if}
                    </span>
                </label>
            </div>
        {/if}
    {/if}
{/foreach}
<img class="dl-ajax-loader" src="assets/tpl/img/loader.svg" alt="">
