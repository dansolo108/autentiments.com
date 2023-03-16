{'!Login' | snippet : [
    'logoutResourceId' => 1,
    'preHooks' => 'checkEmailHook',
    'loginTpl' => '',
    'logoutTpl' => '',
]}

{'!smsLoad' | snippet : ['js' => '/assets/components/sms/js/web/custom.js?v=0.1']}
{include 'au_sms_form'}