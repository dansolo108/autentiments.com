    {if $_modx->hasSessionContext('web')}
        <div class="alert alert-warning" role="alert">
            {'stik_reset_password_already_loggedin' | lexicon}
        </div>
    {else}
        {set $reset = '!ResetPassword' | snippet}
        {if !$reset}
            {'!ForgotPassword' | snippet : [
                'resetResourceId' => $_modx->resource.id,
                'tpl' => 'stikForgotPassTpl',
                'emailSubject' => 'stik_reset_password_email_subject' | lexicon,
                'sentTpl' => 'stikForgotPassSentTpl',
            ]}
        {else}
            <div class="alert alert-success" role="alert">
                {'stik_reset_password_success' | lexicon}
            </div>
        {/if}
    {/if}