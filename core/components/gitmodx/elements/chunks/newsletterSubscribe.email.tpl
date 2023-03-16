{extends 'tpl.msEmail'}

{block 'title'}
    Новый подписчик
{/block}

{block 'products'}
    <p style="margin-left:20px;{$style.p}">
        <strong>Email:</strong> {$email}
    </p>
{/block}