{extends 'tpl.msEmail'}

{block 'title'}
    Сообщение из контактной формы
{/block}

{block 'products'}
    <p style="margin-left:20px;{$style.p}">
        <b>ФИО</b>: [[+name]]<br>
        <b>E-mail</b>: [[+email]]<br>
        <b>Сообщение</b>:<br> [[+message]]<br>
    </p>
{/block}