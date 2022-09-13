{extends 'template:1'}

{block 'main'}
    {set $options = [
        'parents' => $_modx->resource.id,
        'element' => 'getProducts',
        'limit' => 9,
        'tpl' => 'stik.msProducts.row',
        'tplOuter' => 'stik.mFilter2.outer',
        'suggestions' => false,
        'ajaxMode' => 'scroll',
        'filters' => '
            msoption|size:size,
            msoption|color:default,
            msoption|material:default,
            ms|price:price,
        ',
        'tplFilter.outer.ms|price' => 'stik.mFilter2.filter.slider',
        'tplFilter.row.ms|price' => 'stik.mFilter2.filter.number',
        'tplFilter.outer.default' => 'stik.mFilter2.filter.outer',
        'tplFilter.row.default' => 'stik.mFilter2.filter.checkbox',
        'showLog'=>1,
    ]}
    {if $_modx->resource.id != 709}
        {*set $options['sortby'] = 'RAND()'*}
    {/if}
    {'!mFilter2' | snippet : $options}
    <script>
        document.addEventListener('DOMContentLoaded',e=>{
            let thisPage = {$_modx->resource.id | getJSONPageInfo};
            {ignore}
            PageInfo = { ...PageInfo, ...thisPage };
            setTimeout(()=>{
                setEvent('view_item_list',PageInfo);
            },100);
            {/ignore}
        });
    </script>
{/block}