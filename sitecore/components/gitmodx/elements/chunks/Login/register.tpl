{'!Register' | snippet : [    
    'submitVar' => 'registerbtn',
    'activation' => 1,
    'activationEmailSubject' => 'stik_register_activation_email_subject' | lexicon,
    'activationResourceId' => 12,
    'submittedResourceId' => 32,
    'activationEmailTpl' => 'stik.lgnActivateEmailTpl',
    'successMsg' => 'stik_register_success_msg' | lexicon,
    'usergroups' => 'Users',
    'validate' => 'link:blank,
        password:required:minLength=^8^,
        email:required:email',
    'usernameField' => 'email',
    'placeholderPrefix' => 'reg.',
    'preHooks' => 'registerPreHook',
    'postHooks' => 'registerJoinLoyalty,registerAmoCrmId',
]}

<form action="{9|url}" method="post" class="au-login__form_register">
    <h3 class="au-h1  au-login__title">{'stik_modal_register_title' | lexicon}</h3>
    <input type="hidden" name="link" value="{$_modx->getPlaceholder('reg.link')}" /> {* nospam *}
    <div class="custom-form__group">
        <input class="custom-form__input{if $_modx->getPlaceholder('reg.error.email')} error{/if}" type="email" name="email" id="email_register" value="{$_modx->getPlaceholder('reg.email')}" data-val="{$_modx->getPlaceholder('reg.email')}" required=""> 
        <label class="custom-form__label" for="email_register">Email</label>
        <span class="error_field">{$_modx->getPlaceholder('reg.error.email')}</span>
    </div>
    <div class="custom-form__group">
        <input class="custom-form__input{if $_modx->getPlaceholder('reg.error.password')} error{/if}" type="password" name="password" id="password_register" value="{$_modx->getPlaceholder('reg.password')}" data-val="{$_modx->getPlaceholder('reg.password')}" required=""> 
        <label class="custom-form__label" for="password_register">{'stik_register_password' |lexicon}</label>
        <span class="error_field">{$_modx->getPlaceholder('reg.error.password')}</span>
        <button class="au-login__password-hide-btn" type="button"></button>
    </div>
    <div class="custom-form__group-checkbox">
        <input class="custom-form__check  loyalty-check_js" type="checkbox" id="join_loyalty" name="join_loyalty" value="1" {if $_modx->getPlaceholder('reg.join_loyalty')}checked{/if}>
        <label class="custom-form__check-label" for="join_loyalty">{'stik_modal_register_loyalty_label' | lexicon} <a class="au-register__loyalty-link au-text-tab_js" href="loyalty">{'stik_modal_register_loyalty_link' | lexicon}</a></label>
    </div>
    <div class="custom-form__group  custom-form__register_phone"{if $_modx->getPlaceholder('reg.join_loyalty')} style="display: block;"{/if}>
        <input class="custom-form__input required{if $_modx->getPlaceholder('reg.error.mobilephone')} error{/if}" type="tel" name="mobilephone" id="phone_register" value="{$_modx->getPlaceholder('reg.mobilephone')}" data-val="{$_modx->getPlaceholder('reg.mobilephone')}">
        <label class="custom-form__label" for="phone_register">{'stik_register_mobilephone' |lexicon}</label>
        <span class="error_field">{$_modx->getPlaceholder('reg.error.mobilephone')}</span>
    </div>
    <input class="au-btn  au-login__submit  au-login__submit-register" type="submit" name="registerbtn" value="{'stik_register_submit' |lexicon}">
    <p class="au-subscribe__politics-text">{'stik_modal_register_agree' | lexicon} <a class="au-subscribe__politics-link au-text-tab_js" href="policy">{'stik_ss_form_policy_link' | lexicon}</a></p>
</form>
<div class="au-login__bottom">
    <a class="au-btn-light  au-login__tab-link  au-tab-login-title" href="login">{'stik_modal_register_to_login' | lexicon}</a>
</div>