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

    const filtersClickListener = event => {
        const target = event.target
        if (!target.matches(".au-filter__col")) return
        target.classList.toggle("actived")
        hideOnClickOutside(target)
    }

    function hideOnClickOutside(element) {
        const outsideClickListener = event => {
            if (event.target.closest(".au-filter__col > ul") === null) {
            element.classList.remove("actived")
            removeClickListener();
            }
        }

        const removeClickListener = () => {
            document.removeEventListener('click', outsideClickListener);
        }

        document.addEventListener('click', outsideClickListener);
    }

    filtersNav.addEventListener("click", filtersClickListener, false)

    window.addEventListener("resize", (e) => {
        let w = window.innerWidth;
        if (w >= 540) {
            filtersNav.addEventListener("click", filtersClickListener, false)
        } else {
            filtersNav.removeEventListener("click", filtersClickListener, false)
        }
    }, true);

    

    document.addEventListener(
        "DOMContentLoaded", () => {

            const filters = new MmenuLight(
                document.querySelector( "#au-filters" ),
                "(max-width: 540px)"
            )
            const filtersNav = filters.navigation({
                title: "Фильтры и сортировка"
            })

            const drawer = filters.offcanvas()

            document.querySelector(`a[href="#au-filters"]`)
                .addEventListener("click", (e) => {
                    e.preventDefault()
                    drawer.open()
                })

            document.querySelector(".au-filters__show")
                .addEventListener("click", (e) => {
                    e.preventDefault()
                    drawer.close()
                })

            document.querySelector(".au-filters__close")
                .addEventListener("click", (e) => {
                    e.preventDefault()
                    drawer.close()
                })
        }
    )
</script>
' | jsToBottom : true}

{/block}