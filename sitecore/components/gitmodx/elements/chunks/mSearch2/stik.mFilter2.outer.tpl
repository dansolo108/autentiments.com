{if $.get['query'] && !$total}
    <main class="au-page  page-container">
        <h1>{'stik_search_results' | lexicon}</h1>
        <p>{'stik_no_search_results' | lexicon}</p>
    </main>

{else}
    <main class="au-category  au-category__category empty empty_search msearch2" id="mse2_mfilter">
        <div class="au-category__head">
            <h1 class="au-h1  au-category__title">{$_modx->resource.pagetitle}</h1>
            <div class="au-category__filter-btns">
                <button class="au-category__filter  filter-btn-open">
                    {'stik_catalog_filters_title' | lexicon}
                    <span class="au-category__filter-count mse2_total_filters"></span>
                </button>
                <button class="au-close  au-filter__close  au-mobile_xl" aria-label="{'stik_modal_close' | lexicon}"></button>
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
        <div class="modal  au-modal  au-modal-filter">
            <button class="au-close  au-filter__close  au-desktop_xl" aria-label="{'stik_modal_close' | lexicon}"></button>
            <div class="au-modal__content  au-filters__content  container">
                <form action="{$_modx->resource.id | url}" method="post" id="mse2_filters" class="au-filters__form">
                    <h3 class="au-filters__title  au-h2  au-desktop_xl">
                        {'stik_catalog_filters_title' | lexicon}
                        <span class="au-filter__count mse2_total_filters"></span>
                    </h3>
                    <div class="au-filter__row">
                        {$filters}
                        <div class="au-filter__col  au-filter__col_sort">
                            <span class="au-filter__title">{'stik_catalog_sort_title' | lexicon}</span>
                            <div class="au-filter__sorts" id="mse2_sort">
                                <a href="#" data-sort="msoption|new" data-dir="desc" data-default="desc" class="au-filter__label  au-filter__sort sort static-dir{if $sort == 'msoption|new:desc'} active{/if}">{'stik_catalog_sort_new' | lexicon}</a>
                                <a href="#" data-sort="ms|price" data-dir="asc" data-default="asc" class="au-filter__label  au-filter__sort sort static-dir{if $sort == 'ms|price:asc'} active{/if}">{'stik_catalog_sort_cheap' | lexicon}</a>
                                <a href="#" data-sort="ms|price" data-dir="desc" data-default="desc" class="au-filter__label  au-filter__sort sort static-dir{if $sort == 'ms|price:desc'} active{/if}">{'stik_catalog_sort_expensive' | lexicon}</a>
                            </div>
                        </div>
                    </div>
                    <button type="reset" class="au-filters__reset">{'stik_catalog_reset_button' | lexicon}</button>
                </form>
            </div>
        </div>
    </main>
{/if}