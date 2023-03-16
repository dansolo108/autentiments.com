<form action="{13|url}" method="post" class="au-login__form_recovery [[+loginfp.errors:notempty=`submit-resend`]]">
    <h3 class="au-h1  au-login__title">{'stik_reset_pw_title' | lexicon}</h3>
    <p class="au-login__text  au-login__text-recovery">{'stik_reset_password_caption' | lexicon}</p>
    <p class="au-login__text  au-login__text-resend">[[+loginfp.errors]]</p>
    <div class="custom-form__group">
        <input type="email" name="email" placeholder="Email" class="custom-form__input" value="[[+loginfp.post.email]]" data-val="[[+loginfp.post.email]]" required>
    </div>
    <input class="returnUrl" type="hidden" name="returnUrl" value="[[+loginfp.request_uri]]" />
    <input class="loginFPService" type="hidden" name="login_fp_service" value="forgotpassword" />
    
    <input type="submit" name="login_fp" class="au-btn  au-login__submit  au-login__submit-recovery" value="[[%login.reset_password]]" />
    
    <div class="au-login__recovery-resend">
        <p class="au-login__recovery-text">{'stik_reset_pw_send_again_title' | lexicon}</p>
        <input type="submit" name="login_fp" class="au-btn  au-login__submit  au-login__submit-recovery-resend" value="{'stik_reset_pw_send_again_button' | lexicon}" />
    </div>
</form>