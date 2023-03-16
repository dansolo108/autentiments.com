{extends 'template:14'}

{block 'js-params'}
    <script>
        PIXEL_ID = 'VK-RTRG-1431017-ajJfw';
        PRICE_LIST_ID = '297043';

        document.addEventListener('DOMContentLoaded',e=>{
            let thisPage = {$_modx->resource.id | getJSONPageInfo};
            {ignore}
            PageInfo = { ...PageInfo, ...thisPage };
            setTimeout(()=>{
                setEvent('view_item_list',PageInfo);
            },500);
            {/ignore}
        });
    </script>
{/block}
{block 'params'}
    {set $where = [
        'Data.sale' => 1,
    ]}
    {set $wrapper_classes = ''}
{/block}