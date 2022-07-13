<a class="au-lookbooks__card  au-scroll-animat" href="{$id|url}">
    <div class="au-lookbooks__img-box">
        {if $_pls['tv.video']}
            <video class="au-lookbooks__img" webkit-playsinline playsinline autoplay loop muted poster="">
                <source src="{$_pls['tv.video']}" type="video/mp4">
            </video>
        {else}
            {include 'picture' img=$_pls['tv.img'] height=325 width=525 class='au-lookbooks__img'}
        {/if}
    </div>
    <div class="au-lookbooks__description">
        <span class="au-lookbooks__card-title">{$pagetitle}</span>
        <div class="au-btn-light  au-lookbooks__btn">{'stik_lookbook_view' | lexicon}</div>
    </div>
</a>