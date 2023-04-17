{$_modx->lexicon->load('sms:default')}
<form method="post" class="smsLoginForm">
    <div class="custom-form__group js_sms_phone">
        <input type="tel" class="custom-form__input sms_phone_input" name="phone" data-val="" placeholder="{'sms_web_phone' | lexicon}">
        <span class="int-tel-error error_field"></span>
    </div>
    <div class="custom-form__group sms_code js_sms_code" style="display:none;">
        <input type="text" class="custom-form__input sms_code_input" name="code" maxlength="{$_modx->config.sms_code_length}" data-val="" placeholder="{'sms_web_code' | lexicon}">
        <span class="error error-code" style="display:none;">{'stik_profile_sms_code_error' | lexicon}</span>
        <div class="input-tel-text count-seconds-wrapper" style="display:none;">
            {'stik_profile_sms_code_time' | lexicon} <span class="count-seconds"><span>60</span> {'stik_profile_sms_code_sec' | lexicon}</span>
        </div>
    </div>
    <div class="custom-form__group">
        <input
            type="checkbox"
            id="join_loyalty"
            name="join_loyalty"
            value="1" checked />
        <label for="join_loyalty">Участвовать в программе лояльности</label>
    </div>
    <div class="js_sms_buttons_group">
        <button type="button" class="au-btn  au-login__submit  au-login__submit-register js_sms_code_send" disabled>{'sms_web_btn_code_send' | lexicon}</button>
        <span class="custom-code-link-box" style="display:none;">
            <a class="au-btn  au-login__submit  au-login__submit-register js_sms_resend_code" href="#">{'stik_profile_sms_code_resend' | lexicon}</a>
        </span>
        <button type="button" class="btn btn-primary sms_code_btn js_sms_code_check">{'sms_web_btn_code_check' | lexicon}</button>
        <p class="au-subscribe__politics-text">
            {'stik_modal_register_agree' | lexicon} <a class="au-subscribe__politics-link au-text-tab_js" href="policy">{'stik_ss_form_policy_link' | lexicon}</a>
        </p>
    </div>
</form>