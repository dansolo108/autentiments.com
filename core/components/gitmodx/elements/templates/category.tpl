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
        'tplFilter.outer.default' => 'ds.mFilter2.filter.outer',
        'tplFilter.row.default' => 'ds.mFilter2.filter.checkbox',
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
    const filtersNav = document.getElementById("mse2_filters");

    function filterDeskFunc(e) {
        const target = e.target
        if (target.matches(".au-filter__title")) {
            let parent = target.parentNode
            parent.classList.toggle("actived")
        }
    }

    window.addEventListener("resize", (e) => {
        let w = window.innerWidth;
        if (w >= 540) {
            filtersNav.addEventListener("click", filterDeskFunc, false)
        } else {
            filtersNav.removeEventListener("click", filterDeskFunc, false)
        }
    }, true);

    filtersNav.addEventListener("click", filterDeskFunc, false)

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