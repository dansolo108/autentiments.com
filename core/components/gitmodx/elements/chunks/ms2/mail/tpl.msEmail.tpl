{var $style = [
'logo' => 'display:block;margin: 30px auto;',
'a' => 'color:#348eda;',
'p' => 'font-family: Arial;color: #666666;font-size: 12px;',
'h' => 'font-family:Arial;color: #111111;font-weight: 200;line-height: 1.2em;margin: 40px 20px;',
'h1' => 'font-size: 36px;',
'h2' => 'font-size: 28px;',
'h3' => 'font-size: 22px;',
'th' => 'font-family: Arial;text-align: left;color: #111111;',
'td' => 'font-family: Arial;text-align: left;color: #111111;',
]}

{set $currency = $order.properties.msmc.symbol_right ?: 'ms2_frontend_currency' | lexicon}
{set $cid = $order.properties.msmc.id ?: 1}
{set $cource = $order.properties.msmc.val ?: 0}
{set $msloyalty = $order.properties.msloyalty ?: 0}

{var $site_url = ('site_url' | option) | preg_replace : '#/$#' : ''}
{var $assets_url = 'assets_url' | option}
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN"
        "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    <title>{'site_name' | option}</title>
</head>
<body style="margin:0;padding:0;background:#f6f6f6;">
<div style="height:100%;padding-top:20px;background:#f6f6f6;">
    {block 'logo'}
        <a href="{$site_url}">
            <img style="{$style.logo}"
                 src="{$site_url}{$assets_url}tpl/img/logo-email.png"
                 alt="{$site_url}"
                 width="150"/>
        </a>
    {/block}
    <!-- body -->
    <table class="body-wrap" style="padding:0 20px 20px 20px;width: 100%;background:#f6f6f6;margin-top:10px;">
        <tr>
            <td></td>
            <td class="container" style="border:1px solid #f0f0f0;background:#ffffff;width:800px;margin:auto;">
                <div class="content">
                    <table style="width:100%;">
                        <tr>
                            <td>
                                <h3 style="{$style.h}{$style.h3}">
                                    {block 'title'}
                                        miniShop2
                                    {/block}
                                </h3>

                                {block 'products'}
                                    <table style="width:90%;margin:auto;">
                                        <thead>
                                        <tr>
                                            <th>&nbsp;</th>
                                            <th style="{$style.th}">{'ms2_cart_title' | lexicon}</th>
                                            <th style="{$style.th}">{'ms2_cart_count' | lexicon}</th>
                                            <th style="{$style.th}">{'ms2_cart_cost' | lexicon}</th>
                                        </tr>
                                        </thead>
                                        {set $couponsCount = 0}
                                        {foreach $products as $product}
                                            <tr>
                                                <td style="{$style.th}">
                                                    {if $product.thumb?}
                                                        <img src="{$site_url}{$product.thumb | replace : "/small/" : "/cart/"}"
                                                             alt="{$product.pagetitle}"
                                                             title="{$product.pagetitle}"
                                                             width="162" height="218"/>
                                                    {else}
                                                        <img src="{$site_url}{$assets_url}components/minishop2/img/web/ms2_small@2x.png"
                                                             alt="{$product.pagetitle}"
                                                             title="{$product.pagetitle}"
                                                             width="120" height="90"/>
                                                    {/if}
                                                </td>
                                                <td style="{$style.th}">
                                                    {if $product.id?}
                                                        <a href="{$product.id | url : ['scheme' => 'full']}"
                                                           style="{$style.a}">
                                                            {$product.name}
                                                        </a>
                                                        {if ($product.id | resource : 'template') == 23}
                                                            {set $cpns = ('!stikGetCoupons' | snippet : [
                                                            'order'=>$order['id'],
                                                            'product'=>$product.id,
                                                            ])}
                                                            {foreach $cpns as $cpn}
                                                                <br>
                                                                {$cpn['code']}
                                                            {/foreach}
                                                        {/if}
                                                    {else}
                                                        {$product.name}
                                                    {/if}
                                                    {if $product.options?}
                                                        <div class="small">
                                                            {if $product.options.size}
                                                                {'stik_basket_size' | lexicon}: {$product.options.size};
                                                            {/if}
                                                            {if $product.options.color}
                                                                <br>
                                                                {set $colorId = 'msoGetColor' | snippet : ['input' => $product.options.color, 'return_id' => true]}
                                                                {'stik_basket_color' | lexicon}: {('stik_color_'~$colorId) | lexicon};
                                                            {/if}
                                                            {if $product.options.code}
                                                                <br>
                                                                Код подарочного сертификата: {$product.options.code}
                                                            {/if}
                                                        </div>
                                                    {/if}
                                                </td>
                                                <td style="{$style.th}">{$product.count} {'ms2_frontend_count_unit' | lexicon}</td>
                                                <td style="{$style.th}">{'!msMultiCurrencyPrice' | snippet : ['price' => $product.price, 'cid' => $cid, 'cource' => $cource]} {$currency}</td>
                                            </tr>
                                        {/foreach}
                                        <tfoot>
                                        <tr>
                                            <th colspan="2"></th>
                                            <th style="{$style.th}">
                                                {$total.cart_count} {'ms2_frontend_count_unit' | lexicon}
                                            </th>
                                            <th style="{$style.th}">
                                                {'!msMultiCurrencyPrice' | snippet : ['price' => $total.cart_cost, 'cid' => $cid, 'cource' => $cource]} {$currency}
                                            </th>
                                        </tr>
                                        </tfoot>
                                    </table>
                                    {if $order.properties.msloyalty?}
                                        {set $msloyalty = '!msMultiCurrencyPriceFloor' | snippet : [
                                        'price' => $msloyalty,
                                        'cid' => $cid,
                                        'cource' => $cource
                                        ]}
                                        <h3 style="{$style.h}{$style.h3}">
                                            {'stik_order_info_bonuses' | lexicon}: -{$msloyalty} {$currency}
                                        </h3>
                                    {/if}
                                    <h3 style="{$style.h}{$style.h3}">
                                        {'ms2_frontend_order_cost' | lexicon}:
                                        {if $total.delivery_cost}
                                            {'!msMultiCurrencyPrice' | snippet : ['price' => $total.cart_cost, 'cid' => $cid, 'cource' => $cource]} {$currency} + {'!msMultiCurrencyPrice' | snippet : ['price' => $total.delivery_cost, 'cid' => $cid, 'cource' => $cource]}
                                            {$currency} =
                                        {/if}
                                        <strong>{'!msMultiCurrencyPrice' | snippet : ['price' => $total.cost, 'cid' => $cid, 'cource' => $cource]}</strong> {$currency}
                                    </h3>
                                {/block}
                            </td>
                        </tr>
                    </table>

                </div>
                <!-- /content -->
            </td>
            <td></td>
        </tr>
    </table>
    <!-- /body -->
    <!-- footer -->
    <table style="clear:both !important;width: 100%;">
        <tr>
            <td></td>
            <td class="container">
                <!-- content -->
                <div class="content">
                    <table style="width:100%;text-align: center;">
                        <tr>
                            <td align="center">
                                <p style="{$style.p}">
                                    {block 'footer'}
                                        <a href="{$site_url}" style="color: #999999;">
                                            {'site_name' | option}
                                        </a>
                                    {/block}
                                </p>
                            </td>
                        </tr>
                    </table>
                </div>
                <!-- /content -->
            </td>
            <td></td>
        </tr>
    </table>
    <!-- /footer -->
</div>
</body>
</html>
