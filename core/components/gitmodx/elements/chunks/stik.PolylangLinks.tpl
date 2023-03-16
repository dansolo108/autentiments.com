<div class="au-header__lang">
    {foreach $languages as $key => $language}
        {if !$language.active || ($language.active && $showActive)}
            <a class="au-header__lang-link polylang-toggle {$language.classes}" href="{$language.link}">{$language.name | upper}</a>
        {/if}
    {/foreach}
</div>