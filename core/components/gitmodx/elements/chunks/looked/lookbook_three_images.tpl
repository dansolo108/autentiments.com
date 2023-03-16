<div class="au-lookbook__group  {if $position}row_one-{$position}{else}row_three{/if}">
    <div class="au-lookbook__card  au-scroll-animat">
        {if $position == 'left'}
            {include 'picture' img=$image1 height=600 width=450 class='au-lookbooks__img'}
        {else}
            {include 'picture' img=$image1 height=267 width=200 class='au-lookbooks__img'}
        {/if}
    </div>
    <div class="au-lookbook__card  au-scroll-animat">
        {include 'picture' img=$image2 height=267 width=200 class='au-lookbooks__img'}
    </div>
    <div class="au-lookbook__card  au-scroll-animat">
        {if $position == 'right'}
            {include 'picture' img=$image3 height=600 width=450 class='au-lookbooks__img'}
        {else}
            {include 'picture' img=$image3 height=267 width=200 class='au-lookbooks__img'}
        {/if}
    </div>
</div>