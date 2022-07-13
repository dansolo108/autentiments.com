<item>
    <g:id>{$id}{$_modx->getPlaceholder('id_postfix_pls')}</g:id>
    <g:item_group_id>{$article ?: $id}</g:item_group_id>
    <link>{$id | url : ["scheme" => "full"]}</link>
    <g:price>{'!msMultiCurrencyPrice' | snippet : ['price' => $price, 'cid' => $_modx->getPlaceholder('currency_id_pls')] | replace : " " : ""} {$_modx->getPlaceholder('currency_code_pls')}</g:price>
    <g:condition>new</g:condition>
    <g:image_link>{$_modx->getPlaceholder('site_url_pls') ~ $image}</g:image_link>
    <title>{$pagetitle}</title>
    <g:brand>Autentiments</g:brand>
    <g:availability>in stock</g:availability>
    <description>{($content ?: ($longtitle ?: $pagetitle)) | strip_tags | strip | escape}</description>
    <g:google_product_category>1604</g:google_product_category>
    {if $material}
        <g:material>{$material | join : '/'}</g:material>
    {/if}
    <g:color>{foreach $color as $item last=$last}{set $colorId = 'msoGetColor' | snippet : ['input' => $item, 'return_id' => true]}{('stik_color_'~$colorId) | lexicon | htmlent}{if !$last}/{/if}{/foreach}</g:color>
    <g:size>{$size | join : '/'}</g:size>
    <g:mpn>{$article}</g:mpn>
    <g:adult>no</g:adult>
</item>