{extends 'template:14'}

{block 'params'}
    {set $where = [
        'Data.soon' => 1,
        'Data.image:!=' => null,
    ]}
    {set $wrapper_classes = ''}
{/block}