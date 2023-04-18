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
                <a href="#au-filters" class="filters-button ">
                    <svg width="20" height="13" viewBox="0 0 20 13" fill="none" xmlns="http://www.w3.org/2000/svg">
                        <g clip-path="url(#clip0_91_315)">
                          <path
                            d="M5.5 13C6.88071 13 8 11.8807 8 10.5C8 9.11929 6.88071 8 5.5 8C4.11929 8 3 9.11929 3 10.5C3 11.8807 4.11929 13 5.5 13Z"
                            fill="black" />
                          <path
                            d="M13.5 5C14.8807 5 16 3.88071 16 2.5C16 1.11929 14.8807 0 13.5 0C12.1193 0 11 1.11929 11 2.5C11 3.88071 12.1193 5 13.5 5Z"
                            fill="black" />
                          <path d="M0 10.5H20" stroke="black" />
                          <path d="M0 2.5H20" stroke="black" />
                        </g>
                        <defs>
                          <clipPath id="clip0_91_315">
                            <rect width="20" height="13" fill="#fff" />
                          </clipPath>
                        </defs>
                    </svg>
                </a>
                    <nav id="au-filters">
                        <form action="{$_modx->resource.id | url}" method="post" id="mse2_filters" class="au-filters__form">
                        <ul class="au-filter__row">
                            {$filters}
                        </ul>
                        <button type="button" class="au-filters__close">
                            <svg width="12" height="12" viewBox="0 0 12 12" fill="none" xmlns="http://www.w3.org/2000/svg">
                                <line x1="0.648505" y1="10.9579" x2="11.3165" y2="0.413017" stroke="#000"/>
                                <line x1="11.2548" y1="11.0195" x2="0.709892" y2="0.351478" stroke="#000"/>
                            </svg>
                        </button>
                        <button type="button" class="au-filters__show">Показать товары (<span id="mse2_total">{$total ?: 0}</span>)</button>
                        <button type="reset" class="au-filters__reset hidden" form="mse2_filters">{'stik_catalog_reset_button' | lexicon} <span class="mse2_total_filters"></span></button>
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