{if $_modx->resource.id != 15}
<div class="au-modal-cookie">
    <div class="au-modal-cookie__content">
        <button class="au-close-light  au-modal-cookie__close" aria-label="{'stik_modal_close' | lexicon}"></button>
        <span class="au-modal-cookie__title">{'stik_modal_cookie_title' | lexicon}</span>
        <p class="au-modal-cookie__text">{'stik_modal_cookie_text' | lexicon}</p>
    </div>
</div>
{/if}
{if !$_modx->isAuthenticated('web')}
<div class="au-modal au-modal-sale modal " style="max-width:510px">
    <div class="au-info-size__wrapper">
        <button class="au-close au-modal-size__close" onclick="localStorage.discountClose = true;" aria-label="Закрыть"></button>
        <div class="au-modal__content  au-info-size__content">
            <h3 class="au-info-discount__title">Дарим скидку 10% на первую покупку</h3>
            <h2 class="au-info-discount__sub-title">новым участникам программы лояльности</h2>
            <div style="margin-bottom:60px; display:flex;flex-direction:column;padding:0 20px;">
                {'!smsLoad' | snippet : ['js' => '/assets/components/sms/js/web/custom.js?v=0.1']}
                {$_modx->lexicon->load('sms:default')}
                <form method="post" class="smsLoginForm">
                    <div class="custom-form__group js_sms_phone" style="flex-direction:column">
                        <label for="sms_phone" class="au-info-discount__info-text">авторизируйтесь чтобы получить скидку</label>
                        <input type="tel" id="sms_phone" class="custom-form__input sms_phone_input" name="phone" data-val="" placeholder="{'sms_web_phone' | lexicon}">
                        <span class="int-tel-error error_field"></span>
                    </div>
                    <div class="custom-form__group sms_code js_sms_code" style="display:none;">
                        <input type="text" class="custom-form__input sms_code_input" name="code" maxlength="{$_modx->config.sms_code_length}" data-val="" placeholder="{'sms_web_code' | lexicon}">
                        <span class="error error-code" style="display:none;">{'stik_profile_sms_code_error' | lexicon}</span>
                        <div class="input-tel-text count-seconds-wrapper" style="display:none;">
                            {'stik_profile_sms_code_time' | lexicon} <span class="count-seconds"><span>60</span> {'stik_profile_sms_code_sec' | lexicon}</span>
                        </div>
                    </div>
                    <div class="js_sms_buttons_group">
                        <button type="button" class="au-btn  au-login__submit  au-login__submit-register js_sms_code_send" disabled>{'sms_web_btn_code_send' | lexicon}</button>
                        <span class="custom-code-link-box" style="display:none;">
                            <a class="au-btn  au-login__submit  au-login__submit-register js_sms_resend_code" href="#">{'stik_profile_sms_code_resend' | lexicon}</a>
                        </span>
                        <button type="button" class="btn btn-primary sms_code_btn js_sms_code_check">{'sms_web_btn_code_check' | lexicon}</button>
                    </div>
                </form>
                <button style="color:#999;margin-top:20px" onClick="closeForModal();localStorage.discountClose = 1;">Продолжить без скидки</button>
            </div>
            
        </div>
    </div>
</div>
<!--script>
    setTimeout(()=>{
        if(localStorage.discountClose){
        }
        else{
            openModalАdditionally($('.au-modal-overlay'));
            $('.au-modal-sale').addClass('active');
        }
    },8000);
</script-->
{/if}
<div class="au-modal au-modal-sale modal " style="max-width:510px">
    <div class="au-info-size__wrapper">
        <button class="au-close au-modal-size__close" onclick="localStorage.discountClose = true;" aria-label="Закрыть"></button>
        <div class="au-modal__content  au-info-size__content">
            <h3 class="au-info-discount__title">Дарим скидку 10% на первую покупку</h3>
            <h2 class="au-info-discount__sub-title">новым участникам программы лояльности</h2>
            <div style="margin-bottom:60px; display:flex;flex-direction:column;padding:0 20px;">
                {'!smsLoad' | snippet : ['js' => '/assets/components/sms/js/web/custom.js?v=0.1']}
                {$_modx->lexicon->load('sms:default')}
                <form method="post" class="smsLoginForm">
                    <div class="custom-form__group js_sms_phone" style="flex-direction:column">
                        <label for="sms_phone" class="au-info-discount__info-text">авторизируйтесь чтобы получить скидку</label>
                        <input type="tel" id="sms_phone" class="custom-form__input sms_phone_input" name="phone" data-val="" placeholder="{'sms_web_phone' | lexicon}">
                        <span class="int-tel-error error_field"></span>
                    </div>
                    <div class="custom-form__group sms_code js_sms_code" style="display:none;">
                        <input type="text" class="custom-form__input sms_code_input" name="code" maxlength="{$_modx->config.sms_code_length}" data-val="" placeholder="{'sms_web_code' | lexicon}">
                        <span class="error error-code" style="display:none;">{'stik_profile_sms_code_error' | lexicon}</span>
                        <div class="input-tel-text count-seconds-wrapper" style="display:none;">
                            {'stik_profile_sms_code_time' | lexicon} <span class="count-seconds"><span>60</span> {'stik_profile_sms_code_sec' | lexicon}</span>
                        </div>
                    </div>
                    <div class="js_sms_buttons_group">
                        <button type="button" class="au-btn  au-login__submit  au-login__submit-register js_sms_code_send" disabled>{'sms_web_btn_code_send' | lexicon}</button>
                        <span class="custom-code-link-box" style="display:none;">
                            <a class="au-btn  au-login__submit  au-login__submit-register js_sms_resend_code" href="#">{'stik_profile_sms_code_resend' | lexicon}</a>
                        </span>
                        <button type="button" class="btn btn-primary sms_code_btn js_sms_code_check">{'sms_web_btn_code_check' | lexicon}</button>
                    </div>
                </form>
                <button style="color:#999;margin-top:20px" onClick="closeForModal();localStorage.discountClose = 1;">Продолжить без скидки</button>
            </div>
            
        </div>
    </div>
</div>
{*
{'!AjaxForm' | snippet : [
    'snippet' => 'newsletterSubscribe',
    'form' => 'welcomeSubscribe.form',
    'emailTo' => $_modx->config.ms2_email_manager,
    'subject' => 'stik_newsletter_form_subject' | lexicon,
    'subjectConfirm' => 'stik_newsletter_form_subject_confirm' | lexicon,
    'tpl' => 'newsletterSubscribe.email',
    'tplConfirm' => 'newsletterSubscribeEmailTplConfirm',
    'submitVar' => 'welcomeform',
]}
*}

{if !$_modx->hasSessionContext('web')}
    <div class="au-modal  au-modal-login  modal">
        <div class="au-modal__wrapper  au-login__wrapper">
            <button class="au-close  au-modal-login__close  au-desktop" aria-label="{'stik_modal_close' | lexicon}"></button>
            <div class="au-modal__content  au-modal-login__content">
                <div class="au-login__img-box  au-desktop">
                    <picture>
                        <!-- <source type="image/webp" srcset=""> -->
                        <img width="400" height="550" class="au-login__img" src="/assets/tpl/img/au-login-img_login.jpg" alt="">
                    </picture>
                    <picture>
                        <!-- <source type="image/webp" srcset=""> -->
                        <img width="400" height="550" class="au-login__img  au-login__img_register" src="/assets/tpl/img/au-login-img_register.jpg" alt="">
                    </picture>
                </div>
    
                <div class="au-login__tabs">
                    <div class="au-login__tab  au-tab-login-content  active" data-tab="login">
                        {if $_modx->resource.template != 8}
                            {include 'login'}
                        {/if}
                    </div>
    
                    {*<div class="au-login__tab  au-tab-login-content" data-tab="register">
                        {if $_modx->resource.template != 13}
                            {include 'register'}
                        {/if}
                    </div>
    
                    <div class="au-login__tab  au-tab-login-content" data-tab="recovery">
                        <a class="au-login__recovery-back  au-tab-login-title" href="login">
                            <svg width="20" height="20" viewBox="0 0 20 20" fill="none" xmlns="http://www.w3.org/2000/svg">
                                <path d="M5.93411 16.7176L6.64122 17.4247L14.0659 10L6.64117 2.5754L5.93407 3.2825L12.6516 10.0001L5.93411 16.7176Z" fill="#1A1714"/>
                            </svg>
                        </a>
                        {if $_modx->resource.template != 10}
                            {include 'reset_pw'}
                        {/if}
                    </div>*}
                </div>
            </div>
        </div>
    </div>
{/if}
{if $_modx->isAuthenticated('web') && !$_modx->user['email']}
    <div class="au-modal au-modal-mailSubs modal " style="max-width:510px">
        <div class="au-info-size__wrapper">
            <button class="au-close au-modal-size__close" onclick="localStorage.discountClose = true;" aria-label="Закрыть"></button>
            <div class="au-modal__content  au-info-size__content">
                <h3 class="au-info-discount__title">Секретное предложение!</h3>
                <h2 class="au-info-discount__sub-title">Подпишитесь на рассылку акций и новостей, и мы отправим вам полезный подарок на почту!</h2>
                <div style="margin-bottom:60px; display:flex;flex-direction:column;padding:0 20px;">
                    <form method="post" action="https://cp.unisender.com/ru/subscribe?hash=6n88d8uguunamkbz3dcn7n3dfz8bn9zqo9h74amsht8kw8wk5kdgo" class="mailSubs__form" us_mode="embed">
                        <input type="hidden" name="default_list_id" value="1">
                        <input type="hidden" name="overwrite" value="2">
                        <input type="hidden" name="is_v5" value="1">
                        <input type="hidden" name="language" value="ru">
                        <div class="custom-form__group js-mailSubs-email" style="flex-direction:column">
                            <label for="emailSubs" class="au-info-discount__info-text">Введите email, чтобы получить подарок</label>
                            <input type="email" class="custom-form__input" id="emailSubs" name="email" data-val="" placeholder="">
                            <span class="error_field"></span>
                        </div>
                        <button class="au-btn" type="submit" disabled style="margin: 0 auto;">Получить подарок</button>
                    </form>
                    <button style="color:#999;margin-top:20px" onClick="closeForModal();localStorage.mailSubs = 1;">Продолжить без подарка</button>
                </div>

            </div>
        </div>
    </div>
{ignore}
<script>
    setTimeout(()=>{
        if(localStorage.mailSubs){
        }
        else{
            openModalАdditionally($('.au-modal-overlay'));
            $('.au-modal-mailSubs').addClass('active');
        }
    },500);
    let form = document.querySelector('.mailSubs__form');
    let input = form.querySelector('#emailSubs');
    input.addEventListener('input',e=>{
        form.querySelector('.au-btn[type="submit"]').disabled = !validateEmail(input.value);
    })
    form.addEventListener('submit',e=>{
        $.post("/assets/components/autentiments/changeEmail.php", { email:input.value });
    })
</script>
{/ignore}
{/if}
<div class="au-modal au-modal-cart  modal" id="ms2_cart_modal">
    {* содержимое в чанке stik.msCart.ajax *}
</div>

<div class="au-modal-text-page  container  modal">
    {* содержимое в чанке ajaxModalInfo *}
</div>