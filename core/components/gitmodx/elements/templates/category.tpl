{extends 'template:1'}

{block 'main'}
    {set $options = [
        'parents' => $_modx->resource.id,
        'element' => 'getProducts',
        'limit' => 9,
        'tpl' => 'product.row',
        'tplOuter' => 'ds.mFilter2.outer',
        'suggestions' => false,
        'ajaxMode' => 'scroll',
        'filters' => '
            msoption|size:size,
            msoption|color:default,
            msoption|material:default,
            ms|price:price,
        ',
        'tplFilter.outer.ms|price' => 'ds.mFilter2.filter.slider',
        'tplFilter.row.ms|price' => 'ds.mFilter2.filter.number',
        'tplFilter.outer.default' => 'stik.mFilter2.filter.outer',
        'tplFilter.row.default' => 'stik.mFilter2.filter.checkbox',
        'showLog'=>0,
    ]}
{*    {if $_modx->resource.id != 709}*}
{*        {set $options['sortby'] = 'RAND()'}*}
{*    {/if}*}
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

{'
<script>
    const filters = document.getElementById("mse2_filters")
    filters.addEventListener("click", (e) => {
        const target = e.target
        if (!target.matches(".au-filter__title")) return
        let parent = target.parentNode
        parent.classList.toggle("actived")
    }, false)
</script>
' | jsToBottom : true}

{/block}