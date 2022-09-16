<item>
    <g:id>{$id}{$_modx->getPlaceholder('id_postfix_pls')}</g:id>
    <g:item_group_id>{$article ?: $id}</g:item_group_id>
    <link>{$product_id | url : ["scheme" => "full"]}?{$color?('size='~($color)~"&amp;"):''}{$size?('size='~$size):''}</link>
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
    <g:color>{$color}</g:color>
    <g:size>{$size}</g:size>
    <g:mpn>{$article}</g:mpn>
    <g:adult>no</g:adult>
</item>