{extends 'template:1'}


{block 'main'}
    {'!getMsOrderId' | snippet}
    <main class="au-payment-end">
        {'!msGetOrder' | snippet : [
            'tpl' => 'stik.msGetOrder',
        ]}
    </main>
{/block}