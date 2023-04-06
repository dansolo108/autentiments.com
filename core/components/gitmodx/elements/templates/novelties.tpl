{extends 'template:1'}

{block 'js-params'}
    <script>
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
{block 'main'}
    {block 'params'}
        {set $where = [
            'Data.new' => 1,
        ]}
        {set $wrapper_classes = ''}
    {/block}
    
    {block 'products'}
        {set $products = '!pdoPage' | snippet : [
            'element' => 'getProducts',
            'ajaxMode' => 'scroll',
            'limit' => 6,
            'parents' => 7,
            'sortby' => '{"sortindex":"ASC", "menuindex":"ASC"}',
            'tpl' => 'product.row',
            'includeThumbs' => 'category',
            'where' => $where,
        ]}
    {/block}
    {* если товары в категории отсутствуют - для au-category добавляем класс empty *}
    <main id="pdopage" class="au-category {$wrapper_classes} {if !$products}empty{/if}">
        <h1 class="au-h1  au-category__title">{$_modx->resource.pagetitle}</h1>
        <div class="au-catalog rows">
            {if $products}
                {$products}
            {else}
                <div class="au-catalog_empty">
                    {if $_modx->resource.catalog_empty_title}
                        <p class="au-catalog_empty-text  empty-text_margin-bottom">{$_modx->resource.catalog_empty_title}</p>
                    {/if}
                    {if $_modx->resource.catalog_empty_text}
                        <p class="au-catalog_empty-text">
                            <span class="au-catalog_empty-span">{$_modx->resource.catalog_empty_text}</span>
                        </p>
                    {/if}
                    {if $_modx->resource.catalog_empty_button && $_modx->resource.catalog_empty_link}
                        <a class="au-catalog_empty-btn  au-btn" href="{$_modx->resource.catalog_empty_link}">{$_modx->resource.catalog_empty_button}</a>
                    {/if}
                </div>
            {/if}
        </div>
        {'page.nav' | placeholder}
    </main>
{/block}