{set $lang = $_modx->config['cultureKey']}

{if !$_modx->hasSessionContext('web')}
    <div class="au-ordering__info-top">
        <p class="au-ordering__login  active">
            <a class="au-ordering__login-link  btn_login_open  au-tab-login-title" href="login">{'stik_order_login' | lexicon}</a> {'stik_order_login_text' | lexicon}
        </p>
    </div>
    {*{elseif !$_modx->user.join_loyalty}
    <div class="au-ordering__loyalty">
        <div class="au-ordering__loyalty_start active">
            <p class="au-ordering__loyalty-text">
                {'stik_loyalty_join_text' | lexicon}
            </p>
            {'!AjaxForm' | snippet : [
                'snippet' => 'joinLoyalty',
                'form' => 'joinLoyalty.form',
            ]}
        </div>
        <div class="au-ordering__loyalty_end">
            <b class="au-ordering__loyalty-title">Поздравляем!</b>
            <p class="au-ordering__loyalty-text">Вы стали участником программы лояльности. Теперь вы сможете накапливать и тратить бонусы за покупки</p>
        </div>
    </div>*}
{/if}
<main class="au-ordering  page-container ajax-loader-block">
    <h1 class="visually-hidden">{$_modx->resource.pagetitle}</h1>
    
    <div class="au-ordering__ordering">
        {'!msCart' | snippet : [
            'tpl' => 'stik.msCart.order',
            'includeThumbs' => 'cart',
        ]}

        {$_modx->setPlaceholder('msloyalty', $form.msloyalty)}
        {'!mspcForm' | snippet : [
            'tpl' => 'stik.mspcForm',
            'form' => $form,
        ]}
        
        <form class="au-ordering__form ms2_form msOrder" method="post" autocomplete="off">
            <input type="hidden" name="order_rates">
            {*<input type="hidden" name="order_discount">*}
            <input type="hidden" name="point">
            <input type="hidden" name="cdek_id">
            
            {if $_modx->user.join_loyalty}
                <div class="au-ordering__bonuses {if $_modx->getPlaceholder('coupon')}disabled-bonuses{/if}">
                    <div class="au-bonuses__form {if $form.msloyalty}used-bonuses{/if}">
                        <h2 class="au-h2  au-bonuses__title">
                            {'stik_order_info_bonuses' | lexicon}
                            {set $maxmaClientBalance = '!maxmaClientBalance' | snippet : ['phone' => $_modx->user.mobilephone]}
                            <span class="au-bonuses__count">
                                <span>{'!msMultiCurrencyPriceFloor' | snippet : ['price' => $maxmaClientBalance]}</span>
                                {$maxmaClientBalance | declension : ('stik_declension_bonuses' | lexicon)}
                            </span>
                        </h2>
                        <div class="au-bonuses__info">
                            <div class="au-bonuses__head">
                                <a class="au-bonuses__subtitle  au-tab-title  active" href="hoard">{'stik_order_bonuses_tab_collect' | lexicon}</a>
                                <a class="au-bonuses__subtitle  au-tab-title" href="spend">{'stik_order_bonuses_tab_spend' | lexicon}</a>
                                <button class="au-bonuses__subtitle  bonus-rules-open" type="button">{'stik_order_bonuses_tab_rules' | lexicon}</button>
                            </div>
                            <div class="au-bonuses__content  au-tab-content  active" data-tab="hoard">
                                <p class="au-bonuses__text">{'stik_order_bonuses_collect_text' | lexicon} <span class="msloyalty_accrual">0</span> <span class="msloyalty_accrual_declension"></span>!</p>
                            </div>
                            <div class="au-bonuses__content  au-tab-content" data-tab="spend">
                              <div class="custom-form__group">
                                <input class="custom-form__input  au-bonuses__input" type="number" name="msloyalty" id="bonus" value="{$form.msloyalty}" data-val="{$form.msloyalty}"> 
                                <label class="custom-form__label" for="bonus">{'stik_order_bonuses_label' | lexicon}</label>
                                <span class="error_field"></span>
                                <button class="au-btn  au-bonuses__submit" type="button">Ок</button>
                              </div>
                            </div>
                        </div>
                        <div class="au-bonuses__used">
                            <p class="au-bonuses__text">{'stik_order_bonuses_writeoff_text' | lexicon} <span class="msloyalty_writeoff_amount">{$form.msloyalty}</span> <span class="msloyalty_writeoff_declension"></span>.</p>
                            <p class="au-bonuses__text">{'stik_order_bonuses_writeoff_promocode' | lexicon}</p>
                            <button class="au-bonuses__cancel" type="button">{'stik_order_bonuses_writeoff_cancel' | lexicon}</button>
                        </div>
                        <div class="au-bonuses__disabled">
                            <p class="au-bonuses__text">{'stik_order_bonuses_collect_text' | lexicon} <span class="msloyalty_accrual">0</span> <span class="msloyalty_accrual_declension"></span>!</p>
                            <p class="au-bonuses__text">{'stik_order_bonuses_collect_with_promocode' | lexicon}</p>
                        </div>
                    </div>
                </div>
            {/if}

            <h2 class="au-h2  au-ordering__title">{'stik_order_delivery_title' | lexicon}</h2>
            <div class="au-ordering__row">
                <span style="margin-bottom: 30px;">
                    Уважаемые клиенты, в некоторых регионах наблюдается недостаточное количество курьеров. Рекомендуем оформлять доставку до ближайшего склада ПВЗ
                </span>
                <div class="custom-form__group  custom-form__group_arrow">
                    <select id="country" name="country" class="custom-form__input{('country' in list $errors) ? ' error' : ''}">
                        {'!getCountry' | snippet : [
                            'lang' => $lang,
                            'selected' => $form.country
                        ]}
                    </select>
                    <label class="custom-form__label" for="country">{('ms2_frontend_country') | lexicon}</label>
                </div>
                {include 'stik.orderInput' form=$form field='city'}
                {include 'stik.orderInput' form=$form field='index'}
            </div>
            <div class="au-ordering__next-step">
                <div class="au-ordering__delivery">
                    <p class="au-ordering__delivery-title">{'stik_order_delivery_choose' | lexicon}</p>
                    <div class="au-ordering__delivery-box dl-ajax-loader-block" id="deliveries">
                        {* при изменении параметров вызова, также изменить здесь assets/components/stik/getAjaxDeliveryCost.php *}
                        {'!ms2DeliveryCost' | snippet: [
                            'language' => $lang,
                            'cost' => 0,
                            'tpl' => 'tpl.ms2DeliveryCost',
                            'required' => 'country,city,index',
                        ]}
                    </div>
                </div>
                <div id="cdek2_map_ajax">
                    {* при изменении параметров вызова, также изменить здесь assets/components/stik_cdek/cdekDeliveryPointsAjax.php *}
                	{'!cdekDeliveryPoints' | snippet}
                </div>
                <div class="au-ordering__row">
                    {include 'stik.orderInput' form=$form field='street'}
                    {include 'stik.orderInput' form=$form field='building'}
                    {include 'stik.orderInput' form=$form field='corpus'}
                    {include 'stik.orderInput' form=$form field='entrance'}
                    {include 'stik.orderInput' form=$form field='room'}
                    <div class="custom-form__group">
                        <textarea class="custom-form__input{('comment' in list $errors) ? ' error' : ''}" cols="100" rows="1" name="comment" id="comment" data-val="">{$form.comment}</textarea>
                        <label class="custom-form__label" for="comment">{'ms2_frontend_comment' | lexicon}</label>
                    </div>
                </div>
                <div class="au-ordering__row  au-ordering__row_buyer">
                    <h2 class="au-h2  au-ordering__title">{'stik_order_buyer_title' | lexicon}</h2>
                    {include 'stik.orderInput' form=$form field='name'}
                    {include 'stik.orderInput' form=$form field='surname'}
                    {include 'stik.orderInput' form=$form field='email'}
                    <div class="custom-form__group  custom-form__group_phone">
                        <input class="custom-form__input{('phone' in list $errors) ? ' error' : ''}"
                            type="tel" name="phone" id="phone" placeholder="{'ms2_frontend_phone' | lexicon}"
                            value="{if $form['phone'] && !('+' | in : $form['phone'])}+{/if}{$form['phone']}" data-val="{$form['phone']}">
                        <span class="int-tel-error error_field"></span>
                    </div>
                </div>
                
                <div id="payments" class="au-ordering__row  au-ordering__row_payment" {if $_modx->config['cultureKey'] == 'ru'}style="display:none;"{/if}>
                    <h2 class="au-h2  au-ordering__title">{'stik_order_payments_title' | lexicon}</h2>
                    <div class="au-ordering__delivery-box" id="payment_method">
                        {foreach $payments as $payment index=$index}
                            {if $language == 'ru' && $payment.id == 2}
                            {else}
                                <div class="au-ordering__delivery-row payment input-parent">
                                    {*var $checked = !($order.payment in keys $payments) && $index == 0 || $payment.id == $order.payment*}
                                    {var $checked = $lang == 'ru' && $payment.id == 5 || $lang != 'ru' && $payment.id == 2}
                                    <input type="radio" name="payment" class="custom-form__radio" value="{$payment.id}" id="payment_{$payment.id}"{$checked ? 'checked' : ''}>
                                    <label class="custom-form__radio-label" for="payment_{$payment.id}">{$payment.name}</label>
                                </div>
                            {/if}
                        {/foreach}
                    </div>
                </div>
                
            </div>
            <input type="checkbox" id="join_loyalty_order" name="join_loyalty" value="1" style="display:none;">
            <button id="submitbtn" type="submit" name="ms2_action" value="order/submit" class="ms2_link" style="display:none;">submit</button>
        </form>
    </div>
    <div class="au-ordering__total msOrder">
        <div class="au-ordering__sticky">
            <h2 class="au-h2  au-ordering__total-title">{'stik_order_info_title' | lexicon}</h2>
            <table class="au-cart__modal-table">
                <tr class="au-cart__modal-tr">
                    <td class="au-cart__modal-td">{'stik_order_info_cart_cost' | lexicon}</td>
                    <td class="au-cart__modal-td"><span class="ms2_total_no_discount">{'!msMultiCurrencyPrice' | snippet : ['price' => $_modx->getPlaceholder('cart_total_cost')]}</span> {$_modx->getPlaceholder('msmc.symbol_right')}</td>
                </tr>
                {if $_modx->runSnippet('!userHasFirstOrderDiscount')}
                    <tr class="au-cart__modal-tr total_discount_wrapper">
                        <td class="au-cart__modal-td">{'stik_order_info_first_order_discount' | lexicon}</td>
                        <td class="au-cart__modal-td">-<span class="">{$_modx->config.stik_first_order_discount}</span>%</td>
                    </tr>
                {/if}
                <tr class="au-cart__modal-tr total_discount_wrapper" {if !$_modx->getPlaceholder('cart_discount')} style="display:none;"{/if}>
                    <td class="au-cart__modal-td">{'stik_order_info_discount' | lexicon}</td>
                    <td class="au-cart__modal-td">-<span class="ms2_total_discount">{'!msMultiCurrencyPrice' | snippet : ['price' => $_modx->getPlaceholder('cart_discount')]}</span> {$_modx->getPlaceholder('msmc.symbol_right')}</td>
                </tr>
                <tr class="au-cart__modal-tr">
                    <td class="au-cart__modal-td">{'stik_order_info_delivery_cost' | lexicon}</td>
                    <td class="au-cart__modal-td"><span class="ms2_delivery_cost">-</span></td>
                </tr>
                <tr class="au-cart__modal-tr mspc_discount_amount" style="display: none;">
                    <td class="au-cart__modal-td">{'stik_order_info_promocode_discount' | lexicon}</td>
                    <td class="au-cart__modal-td">-<span>0</span> {$_modx->getPlaceholder('msmc.symbol_right')}</td>
                </tr>
                <tr class="au-cart__modal-tr loyalty_discount_amount" style="display:none;">
                    <td class="au-cart__modal-td">{'stik_order_info_loyalty_discount' | lexicon}</td>
                    <td class="au-cart__modal-td">-<span>0</span> {$_modx->getPlaceholder('msmc.symbol_right')}</td>
                </tr>
                <tr class="au-cart__modal-tr  au-cart__modal-tr_total">
                    <td class="au-cart__modal-td">{'stik_order_info_total_cost' | lexicon}</td>
                    <td class="au-cart__modal-td"><span class="ms2_order_cost">{$order.cost ?: 0}</span> {$_modx->getPlaceholder('msmc.symbol_right')}</td>
                </tr>
            </table>
            {if !$_modx->user.join_loyalty}
                <div class="custom-form__group-checkbox  au-ordering__group-checkbox-loyalty">
                    <input class="custom-form__check" type="checkbox" id="join_loyalty_visible" name="join_loyalty_visible" value="1">
                    <label class="custom-form__check-label" for="join_loyalty_visible">{'stik_modal_register_loyalty_label' | lexicon} <a class="au-register__loyalty-link au-text-tab_js" href="loyalty">{'stik_modal_register_loyalty_link' | lexicon}</a></label>
                </div>
            {/if}
            <p style="font-size:13px;">Обращаем ваше внимание, что при включенном vpn оплата заказа может проходить с перебоями</p>
            <button type="button" class="au-btn  au-ordering__submit" id="order_submit">{'stik_order_submit_button' | lexicon}</button>
            {*<p id="delivery_error_text" class=" au-subscribe__politics-text" style="display:none;">{'stik_delivery_error_text' | lexicon}</p>*}
            <p class="au-ordering__politics  au-subscribe__politics-text">{'stik_order_payment_agree' | lexicon} <a class="au-subscribe__politics-link au-text-tab_js" href="policy">{'stik_ss_form_policy_link' | lexicon}</a></p>
        </div>
    </div>
    <img class="ajax-loader" src="assets/tpl/img/loader.svg" alt="">
</main>

<!-- modals -->
<div class="au-modal au-modal-bonus-rules  modal">
    <div class="au-modal__wrapper">
        <button class="au-close modal-close  au-bonus-rules__close" aria-label="{'stik_modal_close' | lexicon}"></button>
        <div class="au-modal__content  au-bonus-rules">
            <h3 class="au-h2  au-bonus-rules__title">{'pdoField' | snippet : ['id' => 464,'field' => 'pagetitle',]}</h3>
            <div class="au-bonus-rules__content">
                {'pdoField' | snippet : [
                    'id' => 464,
                    'field' => 'content',
                ]}
            </div>
        </div>
    </div>
</div>
