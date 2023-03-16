{extends 'template:1'}

{block 'main'}
    <main class="au-home">
        <h1 class="visually-hidden">Autentiments</h1>
        <div class="au-home__slider  swiper-container">
            <div class="swiper-wrapper">
                {foreach json_decode($_modx->resource.home_banner, true) as $row}
                    <a href="{$row.link ?: 'javascript:;'}" class="au-home__slide  swiper-slide">
                        <div class="au-home__slide-img-box">
                            {if $row.video_mob}
                                <video class="au-home__video au-home__video-mobile" webkit-playsinline playsinline
                                       autoplay loop muted poster="{$row.img_mob ?: ''}">
                                    <source src="/assets/uploads/{$row.video_mob}" type="video/mp4">
                                </video>
                            {else}
                                {include 'picture' imgmob=$row.img_mob heightmob=607 widthmob=375 class='au-home__slide-img au-home__slide-img-mobile'}
                            {/if}
                            {if $row.video}
                                <video class="au-home__video au-home__video-desktop" webkit-playsinline playsinline
                                       autoplay loop muted poster="{$row.img ?: ''}">
                                    <source src="/assets/uploads/{$row.video}" type="video/mp4">
                                </video>
                            {else}
                                {include 'picture' img=$row.img height=802 width=1440 class='au-home__slide-img au-home__slide-img-desktop'}
                            {/if}
                        </div>
                        <div class="au-home__slide-text {$row.position}">
                            {if $row.subtitle}
                                <span class="au-home__slide-subtitle">{$row.subtitle}</span>
                            {/if}
                            {if $row.title}
                                <h2 class="au-home__slide-title">{$row.title}</h2>
                            {/if}
                            {if $row.link && $row.button}
                                <span class="au-home__slide-link">{$row.button}</span>
                            {/if}
                        </div>
                    </a>
                {/foreach}
            </div>
            <div class="au-swiper-buttons  au-desktop_xl">
                <div class="au-home__prev  au-swiper-button-prev  swiper-button-prev"></div>
                <div class="au-home__next  au-swiper-button-next  swiper-button-next"></div>
            </div>
            <div class="au-home__pagination  au-swiper-pagination  swiper-pagination"></div>
        </div>

        <section class="au-home__categories  container">
            {foreach json_decode($_modx->resource.home_second_banner, true) as $row}
                <a class="au-home__category  au-scroll-animat {$row.size}" href="{$row.link ?: 'javascript:;'}">
                    <div class="au-home__category-img-box">
                        {include 'picture' img=$row.img imgmob=$row.img_mob height=732 width=614 heightmob=400 widthmob=335 class='au-home__category-img'}
                    </div>
                    {if $row.title}
                        <h2 class="au-home__category-title">{$row.title}</h2>
                    {/if}
                </a>
            {/foreach}
        </section>
    </main>
{/block}