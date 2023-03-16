{extends 'template:1'}

{block 'header'}
    <div class="au-lookbook__gallery">
        <img class="au-lookbooks__gallery-img" src="">
        <button class="au-close" aria-label="{'stik_modal_close' | lexicon}"></button>
    </div>
    {parent}
{/block}

{block 'main'}
    <main class="au-lookbook">
        <div class="au-lookbook__cover">
            {if $_modx->resource.video}
                <video class="au-lookbooks__img" webkit-playsinline playsinline autoplay loop muted poster="">
                    <source src="{$_modx->resource.video}" type="video/mp4">
                </video>
            {else}
                {include 'picture' img=$_modx->resource.img height=417 width=1000 class='au-lookbooks__img'}
            {/if}
        </div>
        <div class="au-lookbook__content">
            <h1 class="au-h1  au-lookbook__title">{$_modx->resource.pagetitle}</h1>
            <p class="au-lookbook__text">{$_modx->resource.introtext}</p>
            <div class="au-lookbook__cards">
                {foreach json_decode($_modx->resource.lookbook) as $item}
                    {($item.MIGX_formname) | chunk: $item}
                {/foreach}
            </div>

            {set $intresting = 'pdoResources' | snippet : [
                'includeTVs' => 'img,video',
                'prepareTVs' => 'img,video',
                'processTVs' => 'img,video',
                'parents' => $_modx->resource.parent,
                'resources' => -$_modx->resource.id,
                'tpl' => 'tpl.lookbookSlider',
                'sortby' => 'menuindex',
                'sortdir' => 'ASC',
                'limit' => 3,
            ]}
    
            {if $intresting}
                <section class="au-lookbook__aside">
                    <h2 class="au-h2  au-lookbook__aside-title">{'stik_lookbook_see_also' | lexicon}</h2>
                    <div class="au-lookbook__slider-box">
                        <div class="au-lookbook__slider  swiper-container">
                            <div class="swiper-wrapper">
                                {$intresting}
                            </div>
                        </div>
                        <div class="au-swiper-buttons  au-desktop_xl">
                            <div class="au-lookbook__prev  au-swiper-button-prev  swiper-button-prev"></div>
                            <div class="au-lookbook__next  au-swiper-button-next  swiper-button-next"></div>
                        </div>
                        <div class="au-lookbook__pagination  au-swiper-pagination  swiper-pagination"></div>
                    </div>
                </section>
            {/if}
        </div>
    </main>
{/block}