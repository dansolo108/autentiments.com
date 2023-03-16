{if $files?}
    {foreach $files as $file index=$index}
        {if $index < 2}

            {if $file['type'] === 'mp4'}
                <video class="au-card__img{if $index > 0} au-card__img_hover{/if}" webkit-playsinline playsinline autoplay loop muted>
                    <source src="{$file.url}" type="video/mp4">
                </video>
            {else}
                <picture>
                    <source type="image/webp" srcset="{$file['category_webp']}">
                    <img width="516" height="687" class="au-card__img{if $index > 0} au-card__img_hover{/if}" src="{$file['category']}" alt="{$pagetitle}" title="{$pagetitle}">
                </picture>
            {/if}
        {/if}
    {/foreach}
{/if}