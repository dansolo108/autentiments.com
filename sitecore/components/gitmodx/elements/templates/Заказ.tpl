{extends 'template:1'}

{block 'main'}
    {'!msGetOrder' | snippet : [
        'tpl' => 'stik.msGetOrderAccount',
        'includeThumbs' => 'cart',
    ]}
{/block}