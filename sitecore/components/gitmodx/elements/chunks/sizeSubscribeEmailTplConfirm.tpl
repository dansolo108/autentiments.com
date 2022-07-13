{extends 'tpl.msEmail'}

{block 'title'}
    {'stik_ss_confirm_title' | lexicon}
{/block}

{block 'products'}
    <p style="margin-left:20px;{$style.p}">
        {if $activated != 1}
            {'stik_ss_confirm_text_not_activated' | lexicon}
        {else}
            {'stik_ss_confirm_text_activated' | lexicon}
        {/if}
    </p>
    {if $activated != 1}
        {set $link = '!PolylangMakeUrl' | snippet : [
            'id' => 28,
            'scheme' => 'full',
        ]}
        <p style="margin-left:20px;{$style.p}"><a href="{$link}?ss_hash={$hash}">{'stik_ss_confirm_link' | lexicon}</a></p>
    {/if}
{/block}