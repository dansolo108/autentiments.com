{extends 'template:1'}

{block 'main'}
    {* если товары в категории отсутствуют - для au-category добавляем класс empty *}
    <main id="pdopage" class="msfavorites-parent au-category  au-category_like empty">
        <h1 class="au-h1  au-category__title">
            {$_modx->resource.pagetitle}
            <span class="au-category__title-count msfavorites-total"></span>
        </h1>
        <div class="au-catalog rows">
            {set $favorites = '!pdoPage' | snippet : [
                'element' => 'msProducts',
                'ajaxMode' => 'scroll',
                'limit' => 6,
                'parents' => 7,
                'resources' => '!msFavorites.ids' | snippet,
                'tpl' => 'stik.msProducts.row',
                'includeThumbs' => 'category',
                'where' => [
                    'Data.image:!=' => null,
                ],
            ]}
            {if $favorites?}
                {$favorites}
            {else}
                <div class="au-catalog_empty">
                    {if $_modx->resource.catalog_empty_title}
                        <p class="au-catalog_empty-text">{$_modx->resource.catalog_empty_title}</p>
                    {/if}
                    {if $_modx->resource.catalog_empty_text}
                        <p class="au-catalog_empty-text">{$_modx->resource.catalog_empty_text}</p>
                    {/if}
                    {if $_modx->resource.catalog_empty_button && $_modx->resource.catalog_empty_link}
                        <a class="au-catalog_empty-btn  au-btn" href="{$_modx->resource.catalog_empty_link}">{$_modx->resource.catalog_empty_button}</a>
                    {/if}
                </div>
            {/if}
        </div>
        {'page.nav' | placeholder}
        
        {if !$favorites}
            {set $mightlike = 'msProducts' | snippet : [
                'parents' => 7,
                'tpl' => 'stik.msProducts.swiper.row',
                'limit' => 8,
                'includeThumbs' => 'category',
                'sortby' => 'Rand()',
                'where' => [
                    'Data.image:!=' => null,
                ]
            ]}
            {if $mightlike}
                <div class="au-liked">
                    <h2 class="au-h2  au-liked__title">{'stik_product_also_like_title' | lexicon}</h2>
                    <div class="au-liked__slider  swiper-container">
                        <div class="swiper-wrapper">
                            {$mightlike}
                        </div>
                    </div>
                    <div class="au-swiper-buttons  au-desktop_xl">
                        <div class="au-liked__prev  au-swiper-button-prev  swiper-button-prev"></div>
                        <div class="au-liked__next  au-swiper-button-next  swiper-button-next"></div>
                    </div>
                    <div class="au-liked__pagination  au-swiper-pagination  swiper-pagination"></div>
                </div>
            {/if}
        {/if}
    </main>
{/block}
