{if $.get['query'] && !$total}
    <main class="au-page  page-container">
        <h1>{'stik_search_results' | lexicon}</h1>
        <p>{'stik_no_search_results' | lexicon}</p>
    </main>

{else}
    <main class="au-category  au-category__category empty empty_search msearch2" id="mse2_mfilter">
        <div class="au-category__head">
            <h1 class="au-h1  au-category__title">{$_modx->resource.pagetitle}</h1>
            <div class="au-filters__content">
                <a href="#au-filters">Фильтры</a>
                    <nav id="au-filters">
                        <form action="{$_modx->resource.id | url}" method="post" id="mse2_filters" class="au-filters__form">
                        <ul class="au-filter__row">
                            {$filters}
                            <li class="au-filter__col  au-filter__col_sort">
                                <span class="au-filter__title">Сортировка</span>
                                <ul class="au-filter__sorts" id="mse2_sort">
                                    <a href="#" data-sort="msoption|new" data-dir="desc" data-default="desc" class="au-filter__label  au-filter__sort sort static-dir{if $sort == 'msoption|new:desc'} active{/if}">{'stik_catalog_sort_new' | lexicon}</a>
                                    <a href="#" data-sort="ms|price" data-dir="asc" data-default="asc" class="au-filter__label  au-filter__sort sort static-dir{if $sort == 'ms|price:asc'} active{/if}">{'stik_catalog_sort_cheap' | lexicon}</a>
                                    <a href="#" data-sort="ms|price" data-dir="desc" data-default="desc" class="au-filter__label  au-filter__sort sort static-dir{if $sort == 'ms|price:desc'} active{/if}">{'stik_catalog_sort_expensive' | lexicon}</a>
                                </ul>
                            </li>
                        </ul>
                        <button type="button">Показать товары</button>
                        <button type="reset" class="au-filters__reset" form="mse2_filters">{'stik_catalog_reset_button' | lexicon}</button>
                    </form>
                    </nav>
            </div>
        </div>
        <div class="au-category__row">
            <div class="au-category__sidebar  sidebar">
                <ul class="au-category__list  theiaStickySidebar">
                    <li class="au-category__item">
                        <a class="au-category__link" href="{7|url}">{'stik_catalog_view_all' | lexicon}</a>
                    </li>
                    {'pdoMenu' | snippet : [
                        'parents' => 7,
                        'level' => 1,
                        'sortby' => 'menuindex',
                        'tplOuter' => '@INLINE {$wrapper}',
                        'tpl' => '@INLINE <li class="au-category__item"><a class="au-category__link" href="{$link}">{$menutitle}</a></li>',
                        'tplHere' => '@INLINE <li class="au-category__item"><a class="au-category__link  active" href="{$link}">{$menutitle}</a></li>',
                        'where' => [
                            'class_key:!=' => 'msProduct',
                        ]
                    ]}
                </ul>
            </div>
            <div class="au-catalog  theiaStickySidebar" id="mse2_results">
                {if $total > 0}
                    {$results}
                {else}
                    <div class="au-catalog_empty">
                        {if $_modx->resource.catalog_empty_title}
                            <p class="au-catalog_empty-text">{$_modx->resource.catalog_empty_title}</p>
                            {if $_modx->resource.catalog_empty_text}
                                <p class="au-catalog_empty-text">{$_modx->resource.catalog_empty_text}</p>
                            {/if}
                        {else}
                            <p class="au-catalog_empty-text">{'stik_catalog_empty_default_title' | lexicon}</p>
                            <p class="au-catalog_empty-text">{'stik_catalog_empty_default_text' | lexicon}</p>
                        {/if}
                    </div>
                {/if}
            </div>
            <div class="mse2_pagination">
                {'page.nav' | placeholder}
            </div>
        </div>
    </main>
{/if}