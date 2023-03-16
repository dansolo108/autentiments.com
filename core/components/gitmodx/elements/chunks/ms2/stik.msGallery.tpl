{set $pagetitle = $_modx->resource.pagetitle}
<div id="msGallery" class="au-product__gallery">
    {if $files}
        <div class="au-product__slider  swiper-container">
            <div class="swiper-wrapper  au-product__gallery-row">
                {foreach $files as $file}
                    <div class="au-product__card  swiper-slide">
                    {if $file['type'] === 'mp4'}
                        <video class="au-product__img" webkit-playsinline playsinline autoplay loop muted>
                            <source src="{$file.url}" type="video/mp4">
                        </video>
                    {else}
                        <picture>
                            <source type="image/webp" srcset="{$file['category_webp']}">
                            <img width="437" height="583" class="au-product__img" src="{$file['category']}" alt="{$pagetitle}" title="{$pagetitle}">
                        </picture>
                    {/if}
                    </div>
                {/foreach}
            </div>
            <div class="au-product__pagination  au-swiper-pagination  swiper-pagination  au-mobile_xl"></div>
        </div>
    {/if}
</div>