{extends 'template:1'}

{block 'main'}
    <main class="au-about">
        <h1 class="au-h1  au-about__title">{$_modx->resource.pagetitle}</h1>
        <div class="au-about__cover">
            {if $_modx->resource.video}
                <video class="au-about__img" webkit-playsinline playsinline autoplay loop muted poster="">
                    <source src="/assets/uploads/{$_modx->resource.video}" type="video/mp4">
                </video>
            {else}
                {include 'picture' img=$_modx->resource.img imgmob=($_modx->resource.img_mob | replace : 'assets/uploads/' : '') height=720 width=1440 heightmob=503 widthmob=375 class='au-about__img'}
            {/if}
        </div>
        <div class="au-about__content">
            <div class="au-about__row  page-container">
                <b class="au-about__concept">{$_modx->resource.introtext}</b>
                <div class="au-about__text-box">
                    {$_modx->resource.content}
                </div>
            </div>
        </div>
    </main>
{/block}