{extends 'tpl.msEmail'}

{block 'title-wrapper'}
    <h2 style="{$style.h}{$style.h2}text-align:center;">
        {'stik_ss_email_size_in_stock' | lexicon}
    </h2>
{/block}

{block 'products'}
    {set $site_url = ('site_url' | option) | preg_replace : '#/$#' : ''}
    {set $link = '!PolylangMakeUrl' | snippet : [
        'id' => $product_id,
        'scheme' => 'full',
    ]}
    {set $pagetitle = '!pdoField' | snippet : [
        'id' => $product_id,
        'field' => 'pagetitle',
    ]}
    <p style="margin-left:20px;{$style.p}">
        {'stik_ss_email_text' | lexicon : [
            'size' => $size,
            'link' => $link,
            'product' => $pagetitle,
        ]}
    </p>
    <table style="width:90%;margin-left:20px;">
        <tbody>
            <tr>
                <td>
                    <a href="{$link}">
                        <img src="{$site_url}{($product_id | resource : 'thumb') | replace : 'small' : 'cart'}"
                            alt="{$pagetitle}"
                            title="{$pagetitle}"
                            width="200" height="292"/>
                    </a>
                </td>
            </tr>
            <tr>
                <td>
                    <table style="padding: 8px;background:{($product_id | resource : 'hexcolor') | msoColorName : 'hex'};border:1px solid #111111;"></table>
                </td>
            </tr>
        </tbody>
    </table>
{/block}