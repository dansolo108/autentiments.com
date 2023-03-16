{if $id in list [7,57]}
    <li class="au-header__item  au-header__sub-open">
        <a href="{$link}" class="au-header__link {$classnames}">{$menutitle}</a>
        {if $id == 7}
            <div class="au-header__sub-box  sub-catalog">
                <button class="au-header__btn-back  au-mobile_xl  au-header__sub-close">{'stick_sub_close' | lexicon}</button>
                <a href="{$link}" class="au-header__sub-title">Одежда</a>
                <div class="au-header__sub-wrapper sub-wrapper__bottom-arrow">
                    <ul class="au-header__sub-list">
                        <li class="au-header__sub-item">
                            <a class="au-header__sub-link" href="{7|url}">{'stik_menu_view_all' | lexicon}</a>
                        </li>
                        {$wrapper}
                    </ul>
                    {if $_modx->config.menu_clothes_img_1}
                        <a class="au-desktop_xl" href="{$_modx->config.menu_clothes_link_1}">
                            <div class="au-header__sub-img-box">
                                {include 'picture' img=$_modx->config.menu_clothes_img_1 height=294 width=196 class='au-header__sub-img' noprefix=true}
                            </div>
                        </a>
                    {/if}
                    {if $_modx->config.menu_clothes_img_2}
                        <a class="au-desktop_xxl" href="{$_modx->config.menu_clothes_link_2}">
                            <div class="au-header__sub-img-box">
                                {*{include 'picture' img=$_modx->config.menu_clothes_img_2 height=294 width=196 class='au-header__sub-img' noprefix=true}*}
                            </div>
                        </a>
                    {/if}
                </div>
            </div>
        {elseif $id == 57}
            <div class="au-header__sub-box">
                <button class="au-header__btn-back  au-mobile_xl  au-header__sub-close">{'stick_sub_close' | lexicon}</button>
                <span class="au-header__sub-title">{'stik_menu_accessorize' | lexicon}</span>
                <div class="au-header__sub-wrapper">
                    <ul class="au-header__sub-list">
                        {$wrapper}
                    </ul>
                    {if $_modx->config.menu_accessorise_img}
                        <a class="au-desktop_xl" href="{$_modx->config.menu_accessorise_link}">
                            <div class="au-header__sub-img-box">
                                {*{include 'picture' img=$_modx->config.menu_accessorise_img height=294 width=196 class='au-header__sub-img' noprefix=true}*}
                            </div>
                        </a>
                    {/if}
                </div>
            </div>
        {/if}
    </li>
{else}
    <li class="au-header__item">
        <a class="au-header__link {$classnames}" href="{$link}">{$menutitle}</a>
    </li>
{/if}