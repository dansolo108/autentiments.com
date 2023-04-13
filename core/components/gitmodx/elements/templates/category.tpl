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

    const filters = document.getElementById("mse2_filters")

    filters.addEventListener("click", e => {
        let trigger = e.target
        if (trigger.matches(".au-filter__title")) {
            let target = trigger.nextElementSibling
            target.style.display = 
            (target.style.display == "none") ? "block" : "none"
            onClickClose(target)
        }
    }, false)

    // const toggleDisplay = (target, trigger) => {
    //     if(!target || !trigger) return 
    //     let defaultDisplay =
    //         window.getComputedStyle(target).getPropertyValue("display")
    //     trigger.addEventListener("click", e => {
    //         target.style.display = 
    //         (target.style.display == "none") ?
    //         defaultDisplay:
    //         "none"
    //     })
    // }

    function isVisible(elem) {
        return !!elem && !!(elem.offsetWidth || elem.offsetHeight || elem.getClientRects().length)
    }

    function onClickClose(elem) {
        function outsideClickListener(event) {
            if (!elem.contains(event.target) && isVisible(elem)) {
                elem.style.display = "none"
                document.removeEventListener("click", outsideClickListener)
            }
        }
        document.addEventListener("click", outsideClickListener)
    }

    document.addEventListener(
        "DOMContentLoaded", () => {

            const triggers = document.querySelectorAll(".au-filter__title")

            triggers.forEach(trigger => {
                let target = trigger.nextElementSibling
                toggleDisplay(target, trigger)
                onClickClose(target)
            })

            const filters = new MmenuLight(
                document.querySelector("#au-filters"),
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