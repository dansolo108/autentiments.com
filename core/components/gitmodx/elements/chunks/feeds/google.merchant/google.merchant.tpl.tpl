<item>
    <g:id>{$id}{$_modx->getPlaceholder("id_postfix_pls")}</g:id>
    <g:item_group_id>{$article ?: $id}</g:item_group_id>
    <g:link>{$product_id | url : ["scheme" => "full"]}?{$color?("color="~($color)~"&amp;"):""}{$size?("size="~$size):""}</g:link>
    <g:price>{"!msMultiCurrencyPrice" | snippet : ["price" => $price, "cid" => $_modx->getPlaceholder("currency_id_pls")] | replace : " " : ""} {$_modx->getPlaceholder("currency_code_pls")}</g:price>
    <g:condition>new</g:condition>
    <g:image_link>{$_modx->getPlaceholder("site_url_pls")}{$thumbs[0]["category"] | escape}</g:image_link>
    <g:title>{$pagetitle}</g:title>
    <g:brand>Autentiments</g:brand>
    <g:availability>in stock</g:availability>
    <g:description>{($content ?: ($longtitle ?: $pagetitle)) | strip_tags | strip | escape}</g:description>
    <g:google_product_category>1604</g:google_product_category>
    {if $material}
        <g:material>{$material | join : "/"}</g:material>
    {/if}
    <g:color>{$color}</g:color>
    <g:size>{$size}</g:size>
    <g:mpn>{$article}</g:mpn>
    <g:adult>no</g:adult>
    <g:gender>female</g:gender>
    <g:age_group>adult</g:age_group>
    <g:google_product_category>{$parent}</g:google_product_category>
    {foreach 1..(count($thumbs)-1) as $i}
        <g:additional_image_link>{$_modx->getPlaceholder("site_url_pls")}{$thumbs[$i]["category"] | escape}</g:additional_image_link>
    {/foreach}
</item>