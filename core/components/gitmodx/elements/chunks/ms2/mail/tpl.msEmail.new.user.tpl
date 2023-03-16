{extends 'tpl.msEmail'}

{block 'title'}
    {'ms2_email_subject_new_user' | lexicon : $order}
{/block}

{block 'products'}
    <ul style="font-family: Arial;color: #666666;font-size: 15px;">
        <li>Фамилия: {$address.surname}</li>
        <li>Имя: {$address.name}</li>
        <li>Телефон: {$address.phone}</li>
        <li>Почта: {$user.email}</li>
        <li>
            Способ доставки: {$delivery.name}
            {if $order.properties.order_rates?}
                ({$order.properties.order_rates})
            {/if}
        </li>
        {if $address.point}
            <li>Точка самовывоза: {$address.point}</li>
        {else}
            <li>Адрес: {$address['street']}, {$address['building']}, {if $address['room']}кв. {$address['room']}, {/if} {if $address['corpus']}к. {$address['corpus']}, {/if} {$address['city']}{$address['country'] ? ', '~$address['country'] : ''}{$address['index'] ? ', '~$address['index'] : ''}</li>
        {/if}
        <li>Тип оплаты: {$payment.name}</li>
        {if $address.comment}
            <li>Комментрарий: {$address.comment}</li>
        {/if}
    </ul>
    <hr>
    {parent}
    {if $payment_link?}
        <p style="margin-left:20px;{$style.p}">
            {'ms2_payment_link' | lexicon : ['link' => $payment_link]}
        </p>
    {/if}
{/block}
