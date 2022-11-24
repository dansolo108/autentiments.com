<div class="au-modal au-modal-entrance  modal">
    <div class="au-modal__wrapper  au-entrance">
        <button class="au-close au-entrance__close" aria-label="{'stik_modal_close' | lexicon}"></button>
        <div class="au-modal__content  au-entrance__content">
            <h3 class="au-h1  au-entrance__title">{'stik_ss_form_title' | lexicon}</h3>
            <div class="au-entrance__form">
                <div class="custom-form__group">
                    <p class="au-entrance__text">{'stik_ss_form_caption' | lexicon}</p>
                    <span class="au-entrance__size">{'stik_ss_size' | lexicon} <span class="selected-size_js">XS</span></span>
                    <input class="custom-form__input  au-subscribe__input" type="tel" name="phone" placeholder="Введите телефон" value="{$_modx->user.mobilephone | filterFakeEmail}" data-val="{$_modx->user.mobilephone | filterFakeEmail}">
                    <span class="error_email"></span>
                    <button class="au-btn  au-entrance__submit" type="submit">{'stik_ss_form_submit' | lexicon}</button>
                    <p class="au-subscribe__politics-text">{'stik_ss_form_agree1' | lexicon} <a class="au-subscribe__politics-link au-text-tab_js" href="policy">{'stik_ss_form_policy_link' | lexicon}</a></p>
                </div>
            </div>
        </div>
    </div>
</div>