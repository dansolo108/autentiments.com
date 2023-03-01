{if $img || $imgmob}
    {set $prefix = $noprefix ? '' : '/assets/uploads/'}
    {if $img}
        {set $thumb = ($prefix ~ $img) | phpThumb : ('w='~($width * 2)~'&h='~($height * 2)~'&zc=1&f=jpg')}
        {set $thumb_webp = ($prefix ~ $img) | phpThumb : ('w='~($width * 2)~'&h='~($height * 2)~'&zc=1&f=webp')}
    {/if}
    {if $imgmob}
        {set $thumb_mob = ($prefix ~ $imgmob) | phpThumb : ('w='~($widthmob * 2)~'&h='~($heightmob * 2)~'&zc=1&f=jpg')}
        {set $thumb_mob_webp = ($prefix ~ $imgmob) | phpThumb : ('w='~($widthmob * 2)~'&h='~($heightmob * 2)~'&zc=1&f=webp')}
    {/if}
    <picture>
        {if $img}
            <source media="(min-width: 1024px)" type="image/webp" srcset="{$thumb_webp}">
            {if $imgmob}
                <source media="(min-width: 1024px)" srcset="{$thumb_mob}">
            {/if}
        {/if}
        {if $imgmob}
            <source type="image/webp" srcset="{$thumb_mob_webp}">
        {/if}
        <img width="{$widthmob ?: $width}" height="{$heightmob ?: $height}" class="{$class}" src="{$thumb_mob ?: $thumb}" alt="">
    </picture>
{/if}