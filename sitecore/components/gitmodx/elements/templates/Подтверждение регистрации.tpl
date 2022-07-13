{extends 'template:1'}

{block 'content'}
    {'!ConfirmRegister' | snippet : [
        'authenticate' => 1,
        'redirectTo' => 11,
        'errorPage' => 11,
    ]}
{/block}