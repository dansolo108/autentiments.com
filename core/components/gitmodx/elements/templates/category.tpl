{extends 'template:1'}

{block 'main'}
    {set $options = [
        'limit' => 9,
        'tpl' => 'product.row',
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
        'showLog' => 0,
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

{$_modx->regClientHTMLBlock('
<link rel="stylesheet" href="/assets/css/mmenu-light.css">')}

{'<script src="/assets/js/mmenu-light.js"></script>' | jsToBottom}

{'
<script>
    // const filters = document.getElementById("mse2_filters")
    // filters.addEventListener("click", (e) => {
    //     const target = e.target
    //     if (target.matches(".au-filter__title")) {
    //         let parent = target.parentNode
    //         parent.classList.toggle("actived")
    //     }
    // }, false)

    // $(document).ready(function() {
    //     $(document).on("change", "#mse2_sort", function() {
    //         var selected = $(this).find("option:selected");
    //         var sort = selected.data("sort");
    //         sort += mse2Config.method_delimeter + selected.val();
    //         mse2Config.sort =  sort;
    //         mSearch2.submit();
    //     });
    // });

    document.addEventListener(
        "DOMContentLoaded", () => {
            const filters = new MmenuLight(
                document.querySelector( "#au-filters" ),
                "(max-width: 540px)"
            )
            const filtersNav = filters.navigation()
            const drawer = filters.offcanvas()

            document.querySelector(`a[href="#au-filters"]`)
                .addEventListener("click", (e) => {
                    e.preventDefault()
                    drawer.open()
                })
        }
    )

</script>
' | jsToBottom : true}

{/block}