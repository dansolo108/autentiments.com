<main class="auten-main">
    <a href="#" class="auten-back">Назад</a>
    <a href="#" class="auten-help">Помощь</a>
    <div class="auten-ordering">
        {'!msCart' | snippet : [
        'tpl' => 'cart',
        'includeThumbs' => 'cart',
        ]}
        <form class="maxma_form auten-loyalty">
            <div class=" auten-promo ">
                <label class="auten-field maxma_field auten-promo__field {if $form["promocode"]}inactive{/if}">
                    <div class="auten-field__title">
                        Промокод
                    </div>
                    <input type="text" class="auten-field__input" name="promocode" value="{$form["promocode"]}">
                </label>
                <button type="submit" data-action="promocode" class="auten-button auten-promo__button">
                    Применить
                </button>
                <button type="submit" data-action="promocode" data-value="" class="auten-button auten-promo__button">
                    отменить
                </button>
            </div>
            <div class="auten-bonuses maxma_field {if $form["bonuses"]}applied{/if}" data-step="cart">
                {if !$_modx->hasSessionContext('web')}
                    <div class="auten-bonuses__login">
                        <a href="">Авторизуйтесь</a>, чтобы использовать накопленные баллы и участвовать в программе
                        лояльности
                    </div>
                {else}
                    {set $bonuses = "!getBonuses" | snippet | number_format : 0 : "." : " "}
                    <div class="auten-bonuses__field">
                        Бонусы <span class="auten-bonuses__amount maxma_bonuses_amount">{$bonuses}</span>&nbsp;бонусов
                    </div>
                    <div class="auten-bonuses__field auten-bonuses__applied">
                        Бонусов применено: <span
                                class="auten-bonuses__amount maxma_bonuses_applied">{$form["bonuses"] | number_format : 0 : "." : " "}</span>
                    </div>
                    <button type="submit" data-action="bonuses" data-value="1"
                            class="auten-button auten-bonuses__button">
                        Потратить
                    </button>
                    <button type="submit" data-action="bonuses" data-value=""
                            class="auten-button auten-bonuses__button">
                        Отменить
                    </button>
                    <div class="auten-bonuses__text" data-step="cart">
                        За эту покупку на Ваш счёт будет зачислено <span
                                class="maxma_order_bonuses_amount">{"!getOrderBonuses" | snippet | number_format : 0 : "." : " "}</span>
                        бонусов!
                        <a href="{20 | url}">
                            Правила начисления и списания бонусов
                        </a>
                    </div>
                {/if}
            </div>
        </form>
        <form class="auten-order" id="msOrder">
            <fieldset class="auten-order-group" data-step="deliveryMethods" data-step-open>
                <legend class="auten-order__title">Доставка</legend>
                <div class="auten-order-group__inner">
                    {set $fields = [
                        [
                            "title"=>"Страна",
                            "name"=>"country",
                            "value"=>$form["country"]
                        ],[
                            "title"=>"Город",
                            "name"=>"city",
                            "value"=>$form["city"]
                        ]
                    ]}
                    {foreach $fields as $field}
                        {if $field["name"] in list $hidden_fields}
                            {set $field["style"] = ($field["style"]?:"") ~ "display:none;"}
                        {/if}
                        {if $field["name"] in list $requires}
                            {set $field["classes"][] = "required"}
                        {/if}
                        {$_modx->getChunk("field", $field)}
                    {/foreach}
                    <div class="auten-delivery-methods" data-msCalcDelivery-wrapper>
                        {"!msCalcDelivery" | snippet}
                    </div>
                    <button type="button" class="auten-button auten-mobile__button" data-go-step="deliveryMethod">
                        Продолжить
                    </button>
                </div>
            </fieldset>
            <fieldset class="auten-order-group" data-step="deliveryMethod">
                <div class="auten-order-group__inner auten-order-group__grid">
                    {set $fields = [
                        [
                            "title"=>"Улица",
                            "name"=>"street",
                            "value"=>$form["street"],
                            "style"=>"--column: 8;"
                        ],[
                            "title"=>"Дом",
                            "name"=>"building",
                            "value"=>$form["building"],
                            "style"=>"--column: 4;"
                        ],[
                            "title"=>"Корупс",
                            "name"=>"corpus",
                            "value"=>$form["corpus"],
                            "style"=>"--column: 4;"
                        ],[
                            "title"=>"Подъезд",
                            "name"=>"entrance",
                            "value"=>$form["entrance"],
                            'style'=>"--column: 4;--mobile-column: 6;"
                        ],
                        [
                            "title"=>"Квартира/офис",
                            "name"=>"room",
                            "value"=>$form["room"],
                            "style"=>"--column: 4;--mobile-column: 6;"
                        ],[
                            "title"=>"Комментарий",
                            "name"=>"comment",
                            'value'=>$form["comment"]
                        ]
                    ]}
                    {foreach $fields as $field}
                        {if $field["name"] in list $hidden_fields}
                            {set $field["style"]= ($field["style"]?:"") ~ "display:none;"}
                        {/if}
                        {if $field["name"] in list $requires}
                            {set $field["classes"][] = "required"}
                        {/if}
                        {$_modx->getChunk("field", $field)}
                    {/foreach}
                    <button type="button" class="auten-button auten-mobile__button"
                            data-go-step="recipient">
                        Продолжить
                    </button>
                </div>
            </fieldset>
            <fieldset class="auten-order-group" data-step="recipient">
                <legend class="auten-order__title">Получатель</legend>
                <div class="auten-order-group__inner auten-order-group__grid">
                    {set $fields = [
                        [
                            "title"=>("ms2_frontend_name" | lexicon),
                            "name"=>"name",
                            "value"=>$form["name"],
                            "style"=>"--column: 6;"
                        ],[
                            "title"=>("ms2_frontend_surname" | lexicon),
                            "name"=>"surname",
                            "value"=>$form["surname"],
                            "style"=>"--column: 6;"
                        ],[
                            "title"=>("ms2_frontend_email" | lexicon),
                            "name"=>"email",
                            "value"=>$form["email"],
                            "style"=>"--column: 6;"
                        ],[
                            "title"=>("ms2_frontend_phone" | lexicon),
                            "name"=>"phone",
                            "value"=>$form["phone"],
                            "style"=>"--column: 6;"
                        ]
                    ]}
                    {foreach $fields as $field}
                        {if $field["name"] in list $hidden_fields}
                            {set $field["style"]= ($field["style"]?:"") ~ "display:none;"}
                        {/if}
                        {if $field["name"] in list $requires}
                            {set $field["classes"][] = "required"}
                        {/if}
                        {$_modx->getChunk("field", $field)}
                    {/foreach}
                    <div class="auten-mobile__button">
                        <button type="submit" name="ms2_action" value="order/submit" class="auten-button">
                            Перейти к оплате
                        </button>
                        <!--button type="submit" data-payment="" value="order/submit" class="auten-button outline">
                            Оплата при получении
                        </button-->
                    </div>
                </div>
            </fieldset>
        </form>
    </div>
    <aside class="auten-order-aside" data-step="cart">
        <div class="auten-order-aside__title">
            Ваш заказ
        </div>
        <div class="auten-order-aside__inner">
            <div class="auten-order-aside__field">
                Количество
                <div>
                    <span class="ms2_total_count">{$cart.total_count}</span>&nbsp;товара
                </div>
            </div>
            <div class="auten-order-aside__field">
                Всего
                <span class="ms2_total_cost">
                    {'!msMultiCurrencyPrice' | snippet : ['price' => $cart.real_total_cost]}
                    {$_modx->getPlaceholder('msmc.symbol_right')}
                </span>
            </div>
            {if $order.discount_cost != 0}
            <div class="auten-order-aside__field">
                {'stik_order_info_discount' | lexicon}
                <span class="ms2_total_discount">
                    - {'!msMultiCurrencyPrice' | snippet : ['price' => $order.discount_cost]}
                    {$_modx->getPlaceholder('msmc.symbol_right')}
                </span>
            </div>
            {/if}
            <div class="auten-order-aside__field">
                {'stik_order_info_delivery_cost' | lexicon}
                <span class="ms2_order_delivery_cost">
                    {if $order.delivery_cost == 0}
                        {"stik_order_delivery_free" | lexicon}
                    {else}
                        {'!msMultiCurrencyPrice' | snippet : ['price' => $order.delivery_cost]}
                        {$_modx->getPlaceholder('msmc.symbol_right')}
                    {/if}
                </span>
            </div>
            <div class="auten-order-aside__field" {if !$order.promocode_discount}style="display:none;"{/if}>
                {'stik_order_info_promocode_discount' | lexicon}
                <span class="ms2_order_promocode_discount">
                    {'!msMultiCurrencyPrice' | snippet : ['price' => $order.promocode_discount]}
                    {$_modx->getPlaceholder('msmc.symbol_right')}
                </span>
            </div>
            <div class="auten-order-aside__field" {if !$order.bonuses_discount}style="display:none;"{/if}>
                {'stik_order_info_bonuses' | lexicon}
                <span class="ms2_order_bonuses_discount">
                    {'!msMultiCurrencyPrice' | snippet : ['price' => $order.bonuses_discount]}
                    {$_modx->getPlaceholder('msmc.symbol_right')}
                </span>
            </div>
            <div class="auten-order-aside__field auten-order-aside__final">
                {'stik_order_info_total_cost' | lexicon}
                <span class="ms2_order_cost">
                   {if $order.cost == 0}
                       {"stik_order_delivery_free" | lexicon}
                   {else}
                       {'!msMultiCurrencyPrice' | snippet : ['price' => $order.cost]} {$_modx->getPlaceholder('msmc.symbol_right')}
                   {/if}
                </span>
            </div>
        </div>
        <div class="auten-promo-mobile {if $form["promocode"]}active{/if}">
            <div class="auten-promo-mobile__pre">
                Ввести промокод
            </div>
            <form class="maxma_form auten-promo auten-promo-mobile__form" method="post">
                <label class="auten-field maxma_field auten-promo__field {if $form["promocode"]}inactive{/if}">
                    <div class="auten-field__title">
                        Промокод
                    </div>
                    <input type="text" class="auten-field__input" name="promocode" value="{$form["promocode"]}">
                </label>
                <button type="submit" data-action="promocode" class="auten-button">
                    Применить
                </button>
                <button type="submit" data-action="promocode" data-value="" class="auten-button">
                    отменить
                </button>
            </form>
        </div>
        <form>
            <button type="submit" name="ms2_action" value="order/submit" class="auten-button only-pc">
                Перейти к оплате
            </button>
            <!--button type="submit" name="ms2_action" value="order/submit" style="display: none" class="auten-button outline only-pc">
                Оплатить при получении
            </button-->
            <button type="button" class="auten-button only-mobile" data-go-step="deliveryMethods">
                Оформить заказ (<span class="ms2_order_cost">
                {if $order.cost == 0}
                    {"stik_order_delivery_free" | lexicon}
                {else}
                    {'!msMultiCurrencyPrice' | snippet : ['price' => $order.cost]} {$_modx->getPlaceholder('msmc.symbol_right')}
                {/if}</span>)
            </button>
        </form>


    </aside>
</main>