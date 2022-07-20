<form class="au-login__form_login" action="{10|url}" method="post">
    <h3 class="au-h1  au-login__title">{'stik_menu_mobile_login' | lexicon}</h3>
    <div class="loginMessage">{$errors}</div>
    <div class="custom-form__group">
        <input class="custom-form__input" type="email" name="username" id="email_login" data-val=""> 
        <label class="custom-form__label" for="email_login">Email</label>
    </div>
    <div class="custom-form__group">
        <input class="custom-form__input" type="password" name="password" id="password_login" data-val=""> 
        <label class="custom-form__label" for="password_login">{'stik_form_login_password' | lexicon}</label>
    </div>
    <input type="hidden" name="returnUrl" value="[[+request_uri]]" />
    {$_pls['login.recaptcha_html']}
    <input type="hidden" name="service" value="login" />
    <div class="au-login__row">
        <input type="submit" name="Login" class="au-btn  au-login__submit-login" value="{'stik_form_login_submit' | lexicon}">
        <a class="au-login__recovery-link  au-tab-login-title" href="recovery">{'stik_form_login_pw_reset' | lexicon}</a>
    </div>
</form>
<div class="au-login__bottom">
    <p class="au-login__bottom-text">{'stik_form_login_no_profile' | lexicon}</p>
    <a class="au-btn-light  au-login__tab-link  au-tab-login-title" href="register">{'stik_modal_registration' | lexicon}</a>
</div>