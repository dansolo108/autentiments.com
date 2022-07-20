{if $mode == 'dropdown'}
<div class="dropdown polylang--{$mode} d-inline-block">
    <a class="btn btn-sm dropdown-toggle dropdown-toggle--{$current.culture_key}" href="#" role="button" id="polylang" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
        <span>{$current.name}</span>
    </a>
    <div class="dropdown-menu" aria-labelledby="polylang">
        {foreach $languages as $key => $language}
            {if !$language.active || ($language.active && $showActive)}
                <a class="dropdown-item polylang-toggle polylang-item polylang-item--{$language.culture_key} {$language.classes}" href="{$language.link}">{$language.name}</a>
            {/if}
        {/foreach}
    </div>
</div>
{else}
<ul class="polylang polylang--{$mode}">
    {foreach $languages as $key => $language}
        {if !$language.active || ($language.active && $showActive)}
            <li class="polylang-item polylang-item--{$language.culture_key} {$language.classes}">
                <a class="polylang-toggle" href="{$language.link}">{$language.name}</a>
            </li>
        {/if}
    {/foreach}
</ul>
{/if}
