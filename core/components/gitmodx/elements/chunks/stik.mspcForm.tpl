<div class="mspc_form">
    <div class="au-ordering__promo-code">
        <div class="au-promo-code__form {$_pls['coupon'] ? 'applied-code' : ''}{$_modx->getPlaceholder('msloyalty') ? 'disabled-code' : ''}">
            <div class="au-promo-code__head">
                <h2 class="au-h2  au-promo-code__title">{'stik_promocode_form_title' | lexicon}</h2>
                <button class="au-promo-code__cancel" type="submit">Отменить</button>
            </div>
            <div class="custom-form__group">
                <input class="custom-form__input  au-promo-code__input mspc_field {$_pls['coupon'] ? $_pls['disfield'] : ''}"
                    type="text" {if $_pls['coupon'] || $_modx->getPlaceholder('msloyalty')}disabled{/if} value="{$_pls['coupon']}" data-val="{$_pls['coupon']}"
                    placeholder="{'stik_promocode_form_placeholder' | lexicon}"> 
                <span class="au-promo-code__applied"><span class="mspc_coupon_description" style="display: none;">{$_pls['coupon_description']}</span></span>
                <span class="au-promo-code__applied"><span class="mspc_msg"></span></span>
                <button class="au-btn  au-promo-code__submit mspc_btn" type="submit">Ок</button>
            </div>
        </div>
    </div>
</div>
{$_modx->setPlaceholder('discount_amount', $_pls['discount_amount'])}
{$_modx->setPlaceholder('coupon', $_pls['coupon'])}