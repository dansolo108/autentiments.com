{'!ajax' | snippet}
{'!setUtms' | snippet}

<!DOCTYPE html>
<html lang="{$_modx->config['cultureKey']}">
<head>
    <!-- Google Tag Manager -->
    <script>(function (w, d, s, l, i) {
            w[l] = w[l] || [];
            w[l].push({
                'gtm.start':
                    new Date().getTime(), event: 'gtm.js'
            });
            var f = d.getElementsByTagName(s)[0],
                j = d.createElement(s), dl = l != 'dataLayer' ? '&l=' + l : '';
            j.async = true;
            j.src =
                'https://www.googletagmanager.com/gtm.js?id=' + i + dl;
            f.parentNode.insertBefore(j, f);
        })(window, document, 'script', 'dataLayer', 'GTM-MT26SQ6');</script>
    <!-- End Google Tag Manager -->
    <meta charset="utf-8">
    <meta name="viewport" content="initial-scale=1, maximum-scale=1, user-scalable=no">
    <meta name="format-detection" content="telephone=no">
    <title>{$_modx->resource.longtitle ?: $_modx->resource.pagetitle}</title>
    <meta name="description" content="{$_modx->resource.og_description ?: $_modx->resource.description}"/>
    <base href="{$_modx->config.site_url}">
    {'!PolylangCanonical' | snippet : [
    'tpl' => '@INLINE <link  rel="alternate" hreflang="{if $lang == "en"}en-us{else}{$lang}{/if}" href="{$url}"/>'
    ]}
    {'!msFavorites.initialize' | snippet}
    {set $msMultiCurrency = '!msMultiCurrency' | snippet : [
    'tpl' => 'stik.msMultiCurrency',
    'frontendCss' => '',
    ]}
    <meta name="author" content="Autentiments">
    <meta property="og:type" content="website">
    <meta property="og:site_name" content="Autentiments">
    <meta property="og:title"
          content="{$_modx->resource.og_title ? $_modx->resource.og_title: $_modx->resource.pagetitle}">
    <meta property="og:description"
          content="{$_modx->resource.og_description ? $_modx->resource.og_description : $_modx->resource.description}">
    <meta property="og:url" content="{$_modx->resource.id | url : ['scheme' => 'full']}">
    {if $_modx->resource.img}
        {set $tv_img = '/assets/uploads/' ~ $_modx->resource.img}
    {/if}
    {if $_modx->resource.og_image}
        {set $tv_og_image = '/' ~ $_modx->resource.og_image}
    {/if}
    {set $og_image = ($tv_og_image ?: $_modx->resource.image) ?: $tv_img}
    {if $og_image}
        <meta property="og:image" content="{$_modx->config.site_url | preg_replace : '#/$#' : ''}{$og_image}">
    {/if}

    <link rel="preload" href="/assets/tpl/fonts/Circe-Regular.woff2" as="font" crossorigin="">
    <link rel="preload" href="/assets/tpl/fonts/Circe-Light.woff2" as="font" crossorigin="">
    {block "styles"}
        <link rel="stylesheet" href="/assets/tpl/css/jquery-ui.css">
        <link rel="stylesheet" href="/assets/tpl/css/swiper-bundle.css">
        <link rel="stylesheet" href="/assets/tpl/css/datepicker.css">
        <link rel="stylesheet" href="/assets/components/minishop2/css/web/lib/jquery.jgrowl.min.css">
        <link rel="stylesheet" href="/assets/tpl/css/intlTelInput.min.css">
        <link rel="stylesheet" href="/assets/tpl/css/style.css">
    {/block}

    <link rel="icon" href="/assets/tpl/favicon/favicon.ico">
    <link rel="icon" href="/assets/tpl/favicon/icon.svg" type="image/svg+xml">
    <link rel="apple-touch-icon" href="/assets/tpl/favicon/apple-touch-icon.png">
    <link rel="manifest" href="/assets/tpl/favicon/manifest.json">
    <script src="/assets/tpl/js/vendor/jquery-3.6.0.min.js"></script>
    <!-- Global site tag (gtag.js) - Google Analytics -->
    {ignore}
        <script>
            window.dataLayer = window.dataLayer || [];

            function gtag(){dataLayer.push(arguments);}
            gtag('js', new Date());

            gtag('config', 'G-HRY09800NV');
        </script>
    {/ignore}
    <!-- Global site tag (gtag.js) - Google Analytics -->
</head>

<body {block 'body-padding'}class="padding-loyality"{/block}>

<!--ROISTAT CODE BEGIN -->
<script>
    {ignore}
    (function (w, d, s, h, id) {
        w.roistatProjectId = id;
        w.roistatHost = h;
        var p = d.location.protocol == "https:" ? "https://" : "http://";
        var u = /^.*roistat_visit=[^;]+(.*)?$/.test(d.cookie) ? "/dist/module.js" : "/api/site/1.0/" + id + "/init?referrer=" + encodeURIComponent(d.location.href);
        var js = d.createElement(s);
        js.charset = "UTF-8";
        js.async = 1;
        js.src = p + h + u;
        var js2 = d.getElementsByTagName(s)[0];
        js2.parentNode.insertBefore(js, js2);
    })(window, document, 'script', 'cloud.roistat.com', '0ba013c55152c87f7b8bbde3f871c0ef');
    {/ignore}
</script>
<!-- BEGIN WHATSAPP INTEGRATION WITH ROISTAT -->
<script type="bogus"
        class="js-whatsapp-message-container">{ignore} Обязательно отправьте это сообщение и дождитесь ответа. Ваш номер: roi-{roistat_visit}{/ignore}</script>
<script>
    {ignore}
    (function () {
        if (window.roistat !== undefined) {
            handler();
        } else {
            var pastCallback = typeof window.onRoistatAllModulesLoaded === "function" ? window.onRoistatAllModulesLoaded : null;
            window.onRoistatAllModulesLoaded = function () {
                if (pastCallback !== null) {
                    pastCallback();
                }
                handler();
            };
        }

        function handler() {
            function init() {
                appendMessageToLinks();

                var delays = [1000, 5000, 15000];
                setTimeout(function func(i) {
                    if (i === undefined) {
                        i = 0;
                    }
                    appendMessageToLinks();
                    i++;
                    if (typeof delays[i] !== 'undefined') {
                        setTimeout(func, delays[i], i);
                    }
                }, delays[0]);
            }

            function replaceQueryParam(url, param, value) {
                var explodedUrl = url.split('?');
                var baseUrl = explodedUrl[0] || '';
                var query = '?' + (explodedUrl[1] || '');
                var regex = new RegExp("([?;&])" + param + "[^&;]*[;&]?");
                var queryWithoutParameter = query.replace(regex, "$1").replace(/&$/, '');
                return baseUrl + (queryWithoutParameter.length > 2 ? queryWithoutParameter + '&' : '?') + (value ? param + "=" + value : '');
            }

            function appendMessageToLinks() {
                var message = document.querySelector('.js-whatsapp-message-container').text;
                var text = message.replace(/{roistat_visit}/g, window.roistatGetCookie('roistat_visit'));
                    text = encodeURI(text);
                var linkElements = document.querySelectorAll('[href*="//wa.me"], [href*="//api.whatsapp.com/send"], [href*="//web.whatsapp.com/send"], [href^="whatsapp://send"]');
                for (var elementKey in linkElements) {
                    if (linkElements.hasOwnProperty(elementKey)) {
                        var element = linkElements[elementKey];
                        element.href = replaceQueryParam(element.href, 'text', text);
                    }
                }
            }

            if (document.readyState === 'loading') {
                document.addEventListener('DOMContentLoaded', init);
            } else {
                init();
            }
        };
    })();
    {/ignore}
</script>
<!-- END WHATSAPP INTEGRATION WITH ROISTAT -->
<script>
    //(function(a,m,o,c,r,m) { a[m]= { id:"186111",hash:"d0802b42752e87dd4c003ffa54348e41f2eb084a414f31afceaa978207b8488d",locale:"ru",inline:true,setMeta:function(p){ this.params=(this.params||[]).concat([p])}};a[o]=a[o]||function(){ (a[o].q=a[o].q||[]).push(arguments)};var d=a.document,s=d.createElement('script');s.async=true;s.id=m+'_script';s.src='https://gso.amocrm.ru/js/button.js?1657957902';d.head&&d.head.appendChild(s)}(window,0,'amoSocialButton',0,0,'amo_social_button'));
</script>
<!--ROISTAT CODE END -->
<script>
    (function (a, m, o, c, r, m) {
        a[m] = {
            id: "186111",
            hash: "d0802b42752e87dd4c003ffa54348e41f2eb084a414f31afceaa978207b8488d",
            locale: "ru",
            inline: false,
            setMeta: function (p) {
                this.params = (this.params || []).concat([p])
            }
        };
        a[o] = a[o] || function () {
            (a[o].q = a[o].q || []).push(arguments)
        };
        var d = a.document, s = d.createElement('script');
        s.async = true;
        s.id = m + '_script';
        s.src = 'https://gso.amocrm.ru/js/button.js?1657957902';
        d.head && d.head.appendChild(s)
    }(window, 0, 'amoSocialButton', 0, 0, 'amo_social_button'));
</script>
<script>
    if (sessionStorage.history) {
        let history = JSON.parse(sessionStorage.history);
        if (history[history.length - 1] !== window.location.href)
            sessionStorage.history = JSON.stringify([...history, window.location.href]);
    } else {
        sessionStorage.history = JSON.stringify([window.location.href]);
    }
    var PIXEL_ID = 'VK-RTRG-1264846-a4dJ9';
    var PRICE_LIST_ID = 208234;
    var SALE_PIXEL_ID = 'VK-RTRG-1264846-a4dJ9';
    var SALE_PRICE_LIST_ID = 208234;
    let PageInfo = {
        products: {},
    };
</script>
{block 'js-params'}
{/block}
<script>
    window.vkAsyncInit = function () {
        VK.Retargeting.Init(PIXEL_ID);
    };
    (function (e) {
        e.ClTrack = e.ClTrack || function () {
            e.ClTrack.queue.push([arguments, +new Date()])
        };
        e.ClTrack.queue = e.ClTrack.queue || [];
    })(window);
</script>
<!-- Google Tag Manager (noscript) -->
<noscript>
    <iframe src="https://www.googletagmanager.com/ns.html?id=GTM-MT26SQ6"
            height="0" width="0" style="display:none;visibility:hidden"></iframe>
</noscript>
<!-- End Google Tag Manager (noscript) -->
<!-- Top.Mail.Ru counter -->
{ignore}
    <script type="text/javascript">
        var _tmr = window._tmr || (window._tmr = []);
        _tmr.push({id: "3269446", type: "pageView", start: (new Date()).getTime(), pid: "USER_ID"});
        (function (d, w, id) {
            if (d.getElementById(id)) return;
            var ts = d.createElement("script");
            ts.type = "text/javascript";
            ts.async = true;
            ts.id = id;
            ts.src = "https://top-fwz1.mail.ru/js/code.js";
            var f = function () {
                var s = d.getElementsByTagName("script")[0];
                s.parentNode.insertBefore(ts, s);
            };
            if (w.opera == "[object Opera]") {
                d.addEventListener("DOMContentLoaded", f, false);
            } else {
                f();
            }
        })(document, window, "tmr-code");
    </script>
    <noscript>
        <div><img src="https://top-fwz1.mail.ru/counter?id=3269446;js=na" style="position:absolute;left:-9999px;"
                  alt="Top.Mail.Ru"/></div>
    </noscript>
    <!-- /Top.Mail.Ru counter -->
{/ignore}
<div class="au-modal-overlay"></div>
{block 'loyality-discount'}
    <div class="top-banner__wrapper">
        <div class="top-banner__items">
            {*if !$_modx->isAuthenticated('web')*}
            {* <div class="top-banner-item active" onClick="openModalАdditionally($('.au-modal-overlay'));$('.au-modal-sale').addClass('active');">ДАРИМ СКИДКУ 10% НА ПЕРВУЮ ПОКУПКУ | ПОДРОБНЕЕ</div>*}
            {*/if*}
            <div class="top-banner-item active"><span>Бесплатная доставка</span> по России</div>
            <div class="top-banner-item">Скидка 10% на первый заказ</div>
            {*<div class="top-banner-item">ДОСТАВКА С ПРИМЕРКОЙ НАШИМ КУРЬЕРОМ ПО МОСКВЕ. ВЫЕЗД СТИЛИСТА</div>*}
        </div>
    </div>
{/block}

<div class="au-header__wrapper">
    {block 'header'}
        <header class="au-header container">
            <div class="au-header__head">
                <div class="au-header__row-left">
                    <button class="au-header__burger  au-header__btn  au-mobile_xl  au-btn-burger_open"
                            aria-label="Открыть меню">
                        <svg width="20" height="20" viewBox="0 0 20 20" fill="none" xmlns="http://www.w3.org/2000/svg">
                            <path d="M2 4H17V5H2V4Z" fill="#1A1714"/>
                            <path d="M2 9H12V10H2V9Z" fill="#1A1714"/>
                            <path d="M2 14H17V15H2V14Z" fill="#1A1714"/>
                        </svg>
                    </button>
                    <button class="au-close  au-login__header-close" aria-label="{'stik_modal_close' | lexicon}">
                        <svg width="20" height="20" viewBox="0 0 20 20" fill="none" xmlns="http://www.w3.org/2000/svg">
                            <path d="M14.0659 3.28239L13.3588 2.57529L5.93414 9.99992L13.3588 17.4246L14.0659 16.7175L7.34837 9.99991L14.0659 3.28239Z"
                                  fill="#1A1714"/>
                        </svg>
                    </button>
                    <button class="au-header__btn au-header__btn-lang au-mobile"
                            aria-label="Переключить язык/валюту">{$_modx->config['cultureKey'] | upper}
                        <span>|</span>{$_pls['msmc.symbol_right']}...
                    </button>
                    <div class="au-header__lang-box">
                        {'!PolylangLinks' | snippet : [
                        'tpl' => 'stik.PolylangLinks',
                        'showActive' => true,
                        'activeClass' => 'active',
                        'css' => '',
                        ]}
                        {$msMultiCurrency}
                    </div>
                </div>
                <a class="au-header__logo" href="/">
                    <svg viewBox="0 0 150 14" fill="none" xmlns="http://www.w3.org/2000/svg">
                        <path d="M5.66708 8.90851C3.75303 8.90851 4.27451 8.88086 3.19798 11.0339L2.44737 12.6655C2.30318 13.4141 2.08985 13.3094 0.578757 13.4181C0.300243 13.4378 0 13.5326 0 13.0961C0 12.0492 0.888876 13.0013 1.45973 11.4428C1.80738 10.4907 6.19645 3.38366 5.68881 1.98516C5.51301 1.50121 5.08239 0.959986 6.38608 0.559004C6.94903 0.38518 7.32631 1.18517 7.53569 1.60985C8.68728 3.94266 11.5534 10.2932 13.181 11.9781C14.625 13.4754 13.0783 13.1909 12.1085 13.0092C10.9016 12.782 11.4606 12.5964 10.3268 10.4334C9.59196 9.03296 9.71443 9.09221 7.53569 8.9737L5.66708 8.90851ZM94.3512 8.47988L98.3768 1.88837C99.1176 0.596535 99.1511 0.582708 100.255 0.977764C101.073 1.27011 100.342 1.20295 100.846 4.23895L102.337 11.4428C102.717 13.0388 103.007 13.2245 101.64 13.5465C100.781 13.748 101.105 13.0309 100.64 10.3149L99.2677 2.70416C97.4998 5.43005 95.0465 9.07246 94.9734 10.1549C94.926 10.866 94.0589 11.1643 93.6204 10.068C93.2786 9.21271 90.0925 4.53919 89.3696 3.45674C89.091 4.64783 87.5187 12.3001 87.6965 13.0092C87.9098 13.8645 86.9321 13.6215 86.3968 13.4714C85.5296 13.2265 86.3019 12.1756 86.7839 10.1865C87.1237 8.7821 88.7513 2.07009 88.2436 1.55652C88.1883 1.50121 87.9572 1.01134 88.4688 0.890851C88.9449 0.780236 89.8041 0.634065 89.9068 1.20295C90.1261 2.40787 93.5453 7.27298 94.3512 8.47988ZM109.636 6.56978L112.824 6.62311C113.31 6.84237 112.145 7.67791 111.933 7.68582L108.67 7.81421C108.67 13.904 112.919 12.7465 113.533 13.0744C113.729 13.1791 114.098 13.9534 112.233 13.9534C110.373 13.9534 109.099 13.5386 108.413 12.709C107.179 11.2176 107.038 9.85665 107.038 8.09272C107.038 5.23054 107.518 3.25329 108.476 2.15701C110.695 -0.373328 114.039 0.444438 113.156 1.50319C112.514 2.27355 109.105 -0.369377 108.67 6.91348L109.636 6.56978ZM118.072 10.8403L118.373 3.92883C118.373 1.88047 116.312 2.58564 116.528 1.67504C116.528 1.67306 117.62 0.48987 118.181 0.622213L124.298 7.41718C125.543 8.75444 126.323 9.56036 126.639 9.83294C126.989 7.7352 127.139 4.49376 126.639 2.49083C126.406 1.56442 127.165 1.48146 128.121 1.48146C129.029 1.48146 128.472 2.91551 128.292 4.10068C127.812 7.25125 127.99 8.80382 127.99 11.6779C127.99 12.1934 126.635 13.2936 126.446 13.0092C125.853 12.1223 126.92 12.2053 123.976 9.03888L119.51 4.31599C119.392 6.82262 119.214 9.98109 119.35 12.4087C119.406 13.3963 119.202 13.264 118.244 13.503C116.449 13.9514 117.675 12.707 117.934 11.8398C118.011 11.5751 118.059 11.2413 118.072 10.8403ZM135.097 2.68243L131.834 2.72589C131.103 2.72589 131.204 1.26615 131.963 1.26615C133.594 1.26615 134.809 1.53282 137.126 1.4064C137.598 1.38072 140.741 1.15554 140.924 1.34122C141.27 1.68689 140.334 2.45922 140.033 2.4691L136.922 2.59749C136.61 4.4029 136.411 8.36334 136.492 10.7119C136.525 11.6858 137.258 12.5312 138.511 12.3771L138.726 12.3435C138.761 12.3435 139.55 12.1282 139.239 13.0527C138.989 13.8092 136.144 14.4077 135.097 12.0216C134.767 11.269 135.008 10.71 135.097 9.78949C135.307 7.60483 135.303 4.9461 135.097 2.68243ZM143.427 12.5588C143.427 11.3105 145.244 13.341 146.356 12.1618C147.86 10.5677 144.006 6.37818 144.006 4.10068C144.006 3.03403 145.736 0 147.506 0C147.913 0 149.223 0.179751 149.223 0.622213C149.223 1.98516 147.482 0.742705 146.26 1.98516C143.907 4.38117 149.384 8.28037 147.569 12.3653C146.805 14.0857 145.714 14.2951 144.146 13.6966C143.599 13.4872 143.427 13.2541 143.427 12.5588ZM5.15153 7.92087H9.03888L6.87002 3.59501C6.62311 3.41131 6.52632 4.13821 6.35447 4.48586L4.78808 7.77075L5.15153 7.92087ZM16.7662 1.52492C16.7662 0.217281 17.4792 0.481968 18.4294 1.11603C20.0392 2.18861 17.8032 1.30369 18.3978 8.36136C18.5657 10.3465 19.4032 11.4566 21.3607 11.9465C25.8031 13.0586 25.6826 3.99797 25.1809 1.99701C24.8826 0.803939 27.0278 1.09431 27.0278 1.95355C27.0278 3.19403 26.2771 8.58654 25.7399 10.0245C25.1611 11.5771 23.3083 13.268 21.5543 13.268C20.2802 13.268 18.3187 12.4956 17.7321 11.3342C16.857 9.60184 16.7662 4.01179 16.7662 1.52492ZM33.3388 2.68243L30.0756 2.72589C29.3448 2.72589 29.4455 1.26615 30.206 1.26615C31.8356 1.26615 33.0504 1.53282 35.3674 1.4064C35.8395 1.38072 38.9821 1.15554 39.1678 1.34122C39.5135 1.68689 38.5772 2.45922 38.277 2.4691L35.1639 2.59749C34.8538 4.4029 34.6543 8.36334 34.7353 10.7119C34.7689 11.6858 35.4997 12.5312 36.7521 12.3771L36.9674 12.3435C37.0029 12.3435 37.791 12.1282 37.4829 13.0527C37.2301 13.8092 34.3857 14.4077 33.3388 12.0216C33.0089 11.269 33.2519 10.71 33.3388 9.78949C33.5501 7.60483 33.5462 4.9461 33.3388 2.68243ZM44.8467 6.56978L48.0348 6.62311C48.5208 6.84237 47.3534 7.67791 47.144 7.68582L43.8808 7.81421C43.8808 13.904 48.1297 12.7465 48.742 13.0744C48.9395 13.1791 49.3089 13.9534 47.4442 13.9534C45.5835 13.9534 44.3095 13.5386 43.6221 12.709C42.3875 11.2176 42.2493 9.85665 42.2493 8.09272C42.2493 5.23054 42.7273 3.25329 43.6873 2.15701C45.9035 -0.373328 49.2496 0.444438 48.3667 1.50319C47.7247 2.27355 44.3134 -0.369377 43.8808 6.91348L44.8467 6.56978ZM53.2832 10.8403L53.5834 3.92883C53.5834 1.88047 51.5232 2.58564 51.7365 1.67504C51.7385 1.67306 52.8308 0.48987 53.3898 0.622213L59.5092 7.41718C60.7537 8.75444 61.5339 9.56036 61.848 9.83294C62.1976 7.7352 62.3497 4.49376 61.848 2.49083C61.6169 1.56442 62.3754 1.48146 63.3294 1.48146C64.24 1.48146 63.681 2.91551 63.5013 4.10068C63.0233 7.25125 63.201 8.80382 63.201 11.6779C63.201 12.1934 61.846 13.2936 61.6564 13.0092C61.0638 12.1223 62.1285 12.2053 59.1873 9.03888L54.7212 4.31599C54.6007 6.82262 54.4249 9.98109 54.5612 12.4087C54.6165 13.3963 54.413 13.264 53.455 13.503C51.6595 13.9514 52.8861 12.707 53.1429 11.8398C53.2219 11.5751 53.2693 11.2413 53.2832 10.8403ZM70.3061 2.68243L67.043 2.72589C66.3121 2.72589 66.4148 1.26615 67.1733 1.26615C68.8029 1.26615 70.0177 1.53282 72.3347 1.4064C72.8088 1.38072 75.9495 1.15554 76.1352 1.34122C76.4809 1.68689 75.5446 2.45922 75.2443 2.4691L72.1313 2.59749C71.8212 4.4029 71.6217 8.36334 71.7027 10.7119C71.7362 11.6858 72.4691 12.5312 73.7214 12.3771L73.9347 12.3435C73.9703 12.3435 74.7584 12.1282 74.4503 13.0527C74.1974 13.8092 71.353 14.4077 70.3061 12.0216C69.9763 11.269 70.2192 10.71 70.3061 9.78949C70.5175 7.60483 70.5135 4.9461 70.3061 2.68243ZM79.7736 6.80484C79.7736 6.26559 79.823 3.28489 79.5386 2.96292C78.8255 2.15503 79.2719 1.83108 80.0324 1.22467C81.1188 0.35555 81.5988 1.33134 81.5988 1.33134C81.5988 1.52887 81.3736 2.17281 81.342 3.26316L81.1919 11.743C81.1919 12.7011 81.1978 13.8684 80.5045 13.8684C80.1904 13.8684 79.9889 13.5386 79.904 12.8808C79.8171 12.223 79.7736 10.1964 79.7736 6.80484Z"
                              fill="black"/>
                    </svg>
                </a>
                <div class="au-header__row-right">
                    <button class="au-header__btn  au-header__btn_search  btn_search_open  au-desktop_xl">{'stik_header_menu_search' | lexicon}</button>
                    {if $_modx->hasSessionContext('web')}
                        <a class="au-header__btn  au-header__btn_login  au-desktop_xl"
                           href="{11|url}">{'stik_header_menu_profile' | lexicon}</a>
                    {else}
                        <button class="au-header__btn  au-header__btn_login  btn_login_open  au-desktop_xl">{'stik_header_menu_login' | lexicon}</button>
                    {/if}
                    <a class="au-header__btn au-header__btn_favorite msfavorites msfavorites-total-all visible"
                       href="{14|url}" aria-label="Избранное">
                       <svg width="20" height="19" viewBox="0 0 20 19" fill="none" xmlns="http://www.w3.org/2000/svg">
                        <path d="M9.6631 0.0736485C9.54545 0.110836 9.39572 0.201153 9.32086 0.270218C9.24599 0.339285 8.60963 1.55059 7.8877 3.00627C6.88235 5.02511 6.55615 5.63608 6.45989 5.67858C6.39572 5.70514 5.14439 5.8964 3.68984 6.10359C2.23529 6.31079 0.935829 6.50205 0.802139 6.53393C0.358289 6.63487 0.00534759 7.04395 0 7.45303C0 7.89398 0.026738 7.92586 2.25668 10.0988C3.90374 11.7085 4.38503 12.2026 4.38503 12.3035C4.38503 12.3726 4.16578 13.6955 3.8984 15.2362C3.35294 18.4026 3.35829 18.3122 3.74332 18.6948C3.97861 18.9285 4.30481 19.0401 4.59358 18.987C4.70053 18.9657 5.94652 18.3441 7.36898 17.6003C8.7861 16.8566 9.97326 16.2509 10.0053 16.2509C10.0321 16.2509 11.2193 16.8566 12.6364 17.6003C14.0535 18.3441 15.2995 18.9657 15.4064 18.987C15.6952 19.0401 16.0214 18.9285 16.2567 18.6948C16.6417 18.3122 16.6471 18.4026 16.1016 15.2415C15.8342 13.7008 15.615 12.3779 15.615 12.3089C15.615 12.2026 16.0535 11.7457 17.7112 10.136C19.8663 8.03743 20 7.88336 20 7.4849C20 7.1077 19.6952 6.68799 19.3369 6.56049C19.246 6.52861 15.6578 5.99734 14.0909 5.78483C13.8556 5.75295 13.6043 5.69983 13.5401 5.67326C13.4385 5.63608 13.123 5.03574 12.1123 3.00627C10.738 0.238342 10.6952 0.163963 10.2781 0.0470848C10.0214 -0.0219822 9.91444 -0.0166683 9.6631 0.0736485ZM10.123 0.801491C10.1711 0.83868 10.7807 2.00748 11.4706 3.3941C12.1604 4.78073 12.7861 5.99734 12.861 6.08766C12.9358 6.18329 13.0695 6.30017 13.1497 6.34267C13.2353 6.38517 14.1444 6.53924 15.1658 6.68799C16.1925 6.83675 17.4973 7.0227 18.0642 7.1077C19.1872 7.26708 19.3155 7.32021 19.2193 7.55928C19.1925 7.62835 18.2299 8.59527 17.0749 9.71094C15.6952 11.0497 14.9626 11.7935 14.9251 11.9051C14.893 11.9954 14.8663 12.1654 14.8663 12.2876C14.8663 12.4098 15.0802 13.7486 15.3422 15.2681C15.6631 17.1009 15.8021 18.0572 15.7754 18.1157C15.6898 18.2644 15.5562 18.2804 15.3262 18.1582C15.2032 18.0997 14.0267 17.4835 12.7005 16.7928C10.4064 15.5921 10.2781 15.5337 10 15.5337C9.72193 15.5337 9.59358 15.5921 7.40642 16.7344C4.31016 18.3547 4.46524 18.2751 4.36364 18.2379C4.31551 18.2219 4.25134 18.1635 4.2246 18.1157C4.19251 18.0519 4.33155 17.1275 4.65775 15.2627C4.91979 13.7486 5.13369 12.4098 5.13369 12.2876C5.13369 12.1654 5.10695 11.9954 5.07487 11.9051C5.03743 11.7935 4.31551 11.0551 2.92513 9.71094C1.77005 8.59527 0.807487 7.62835 0.780749 7.55928C0.679144 7.3149 0.802139 7.2724 2.14973 7.07582C2.83422 6.9802 4.13904 6.78894 5.04813 6.65612C5.95187 6.52861 6.76471 6.38517 6.85027 6.34267C6.93048 6.29485 7.06417 6.18329 7.13904 6.08766C7.2139 5.99734 7.83957 4.78604 8.52406 3.40473C9.2139 2.02342 9.80749 0.859932 9.83957 0.81743C9.91979 0.7218 10.0053 0.716488 10.123 0.801491Z" fill="#000"></path>
                    </svg>
                        <span class="au-header__number  au-header__favorite-number msfavorites-total badge-count"
                              data-data-list="default" data-data-type="resource" data-value=""></span>
                    </a>
                    {'!msMiniCart' | snippet : [
                    'tpl' => 'stik.msMiniCart',
                    ]}
                </div>
            </div>
            <div class="au-burger">
                <div class="au-burger__head">
                    <button class="au-close au-btn-burger_close" aria-label="{'stik_modal_close' | lexicon}"></button>
                    <button class="au-burger__head-btn  btn_search_open">{'stik_header_menu_search' | lexicon}</button>
                    {if $_modx->hasSessionContext('web')}
                        <a class="au-burger__head-btn" href="{11|url}">{'stik_header_menu_profile' | lexicon}</a>
                    {else}
                        <button class="au-burger__head-btn  btn_login_open">{'stik_header_menu_login' | lexicon}</button>
                    {/if}
                </div>
                <div class="au-search modal">
                    <button class="au-close  au-search__close  au-desktop_xl"
                            aria-label="{'stik_modal_close' | lexicon}"></button>
                    <div class="au-modal__content  au-modal__search-content">
                        <h3 class="au-search__title  au-h2 au-desktop_xl">{'stik_search_title' | lexicon}</h3>
                        <form method="get" action={7|url}>
                            <div class="custom-form__group">
                                <input class="custom-form__input  custom-form__input_search" type="text" name="query"
                                       value="{$.get['query']}" id="search"
                                       placeholder="{'stik_search_placeholder' | lexicon}">
                                <button class="au-search__submit" type="submit">
                                    <svg viewBox="0 0 20 20" fill="none" xmlns="http://www.w3.org/2000/svg">
                                        <path fill-rule="evenodd" clip-rule="evenodd"
                                              d="M8 1C11.866 1 15 4.13401 15 8C15 9.76078 14.3499 11.3697 13.2766 12.5999L18.3536 17.6768L17.6464 18.3839L12.5673 13.3048C11.3414 14.3613 9.7453 15 8 15C4.13401 15 1 11.866 1 8C1 4.13401 4.13401 1 8 1ZM8 14C11.3137 14 14 11.3137 14 8C14 4.68629 11.3137 2 8 2C4.68629 2 2 4.68629 2 8C2 11.3137 4.68629 14 8 14Z"
                                              fill="white"/>
                                    </svg>
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
                <nav class="au-header__nav">
                    <ul class="au-header__menu">
                        {'pdoMenu' | snippet : [
                        'parents' => 0,
                        'resources' => '-1,',
                        'level' => 3,
                        'sortby' => 'menuindex',
                        'tplOuter' => '@INLINE {$wrapper}',
                        'tpl' => 'mainMenu.tpl',
                        'tplInnerRow' => '@INLINE <li class="au-header__sub-item"><a class="au-header__sub-link" href="{$link}">{$menutitle}</a></li>',
                        'where' => [
                        'class_key:!=' => 'msProduct',
                        ]
                        ]}
                        <li class="au-header__item  au-header__sub-open  au-mobile_xl">
                            <span class="au-header__link">{'stik_menu_info' | lexicon}</span>
                            <div class="au-header__sub-box">
                                <button class="au-header__btn-back  au-header__sub-close">{'stik_link_back' | lexicon}</button>
                                <span class="au-header__sub-title">{'stik_menu_info' | lexicon}</span>
                                <div class="au-header__sub-wrapper">
                                    <ul class="au-header__sub-list">
                                        <li class="au-header__sub-item">
                                            <a class="au-header__sub-link"
                                               href="{2|url}">{$linkAbout = 'pdoField' | snippet : ['id' => 2]}</a>
                                        </li>
                                        {'pdoMenu' | snippet : [
                                        'parents' => 16,
                                        'resources' => -2,
                                        'sortby' => 'menuindex',
                                        'level' => 1,
                                        'tplOuter' => '@INLINE {$wrapper}',
                                        'tpl' => '@INLINE <li class="au-header__sub-item"><a class="au-header__sub-link  au-text-tab_js" href="{$link}">{$menutitle}</a></li>',
                                        ]}
                                    </ul>
                                </div>
                            </div>
                        </li>
                    </ul>
                    <div class="au-burger__contacts">
                        <a class="au-burger__phone"
                           href="tel:{$_modx->config.phone | preg_replace : '/[^0-9+]/' : ''}">{$_modx->config.phone}</a>
                        <div class="au-burger__social-box">
                            <a class="au-burger__social" href="{$_modx->config.instagram}" target="_blank"
                               aria-label="{'stik_link_instagram' | lexicon}">
                                <svg width="20" height="20" viewBox="0 0 20 20" fill="none"
                                     xmlns="http://www.w3.org/2000/svg">
                                    <path fill-rule="evenodd" clip-rule="evenodd"
                                          d="M5.75 1.99609C3.67897 1.99609 2 3.67516 2 5.74648V14.2472C2 16.3186 3.67897 17.9976 5.75 17.9976H14.25C16.321 17.9976 18 16.3186 18 14.2472V5.74648C18 3.67516 16.321 1.99609 14.25 1.99609H5.75ZM1 5.74648C1 3.12296 3.1266 0.996094 5.75 0.996094H14.25C16.8734 0.996094 19 3.12296 19 5.74648V14.2472C19 16.8708 16.8734 18.9976 14.25 18.9976H5.75C3.1266 18.9976 1 16.8708 1 14.2472V5.74648Z"
                                          fill="white"/>
                                    <path fill-rule="evenodd" clip-rule="evenodd"
                                          d="M10.4156 7.19738C9.8261 7.10996 9.22403 7.21066 8.69505 7.48513C8.16607 7.75961 7.7371 8.1939 7.46917 8.72623C7.20123 9.25856 7.10797 9.86181 7.20265 10.4502C7.29733 11.0386 7.57513 11.5821 7.99653 12.0035C8.41793 12.4249 8.96148 12.7027 9.54987 12.7974C10.1383 12.8921 10.7415 12.7988 11.2738 12.5309C11.8062 12.263 12.2405 11.834 12.5149 11.305C12.7894 10.776 12.8901 10.174 12.8027 9.58447C12.7135 8.98314 12.4333 8.42645 12.0035 7.9966C11.5736 7.56675 11.0169 7.28655 10.4156 7.19738ZM8.23448 6.59751C8.95016 6.22616 9.76472 6.08993 10.5623 6.2082C11.3758 6.32884 12.129 6.70793 12.7106 7.28949C13.2921 7.87105 13.6712 8.62423 13.7919 9.43778C13.9101 10.2354 13.7739 11.0499 13.4026 11.7656C13.0312 12.4813 12.4436 13.0616 11.7234 13.4241C11.0032 13.7866 10.187 13.9128 9.391 13.7847C8.59495 13.6566 7.85956 13.2808 7.28942 12.7106C6.71929 12.1405 6.34345 11.4051 6.21535 10.6091C6.08726 9.81302 6.21343 8.99685 6.57593 8.27664C6.93843 7.55643 7.5188 6.96887 8.23448 6.59751Z"
                                          fill="white"/>
                                    <path fill-rule="evenodd" clip-rule="evenodd"
                                          d="M14 4.50073C14 4.22459 14.2239 4.00073 14.5 4.00073H14.5086C14.7847 4.00073 15.0086 4.22459 15.0086 4.50073C15.0086 4.77687 14.7847 5.00073 14.5086 5.00073H14.5C14.2239 5.00073 14 4.77687 14 4.50073Z"
                                          fill="white"/>
                                </svg>
                            </a>
                            <a class="au-burger__social"
                               href="https://wa.me/{$_modx->config.whatsapp | preg_replace : '/[^0-9]/' : ''}"
                               target="_blank" aria-label="Написать в Whatsapp">
                                <svg width="20" height="20" viewBox="0 0 20 20" fill="none"
                                     xmlns="http://www.w3.org/2000/svg">
                                    <path fill-rule="evenodd" clip-rule="evenodd"
                                          d="M9.72628 7.73562V7.73062C9.72051 7.71673 9.71378 7.70327 9.70613 7.69032L9.70109 7.68032L9.69605 7.67534L8.25979 5.31785C8.23194 5.27153 8.19288 5.23294 8.14621 5.20565C8.09953 5.17836 8.04675 5.16325 7.9927 5.16168C7.99062 5.16158 7.98853 5.16147 7.98645 5.16136C7.97845 5.16093 7.97045 5.1605 7.96246 5.16088C7.93845 5.16327 7.91479 5.16835 7.89191 5.17603C7.17359 5.39654 6.1962 6.25512 5.92147 6.76291C5.64674 7.27071 5.61203 7.86591 5.72997 8.47057C5.96585 9.67989 6.80292 10.9797 7.86167 12.0622C8.92043 13.1447 10.1971 14.0112 11.4145 14.2635C12.0232 14.3897 12.6293 14.3563 13.1531 14.0822C13.6769 13.808 14.5623 12.8282 14.8212 12.0974C14.8451 12.0288 14.8451 11.9543 14.8214 11.8857C14.7976 11.8171 14.7514 11.7586 14.6902 11.7196L12.3922 10.2789L12.3871 10.2739C12.3758 10.2664 12.364 10.2597 12.3519 10.2537C12.3181 10.2318 12.2805 10.2164 12.241 10.2084H12.2309C12.2226 10.2064 12.2142 10.2047 12.2057 10.2034H12.2007C12.1469 10.2003 12.0932 10.2107 12.0445 10.2336C12.0268 10.2421 12.0099 10.2523 11.9941 10.2638C11.9836 10.27 11.9735 10.2767 11.9638 10.284C11.9603 10.2889 11.957 10.294 11.9537 10.2992C11.9411 10.31 11.9293 10.3218 11.9185 10.3344L11.4145 10.8432C10.5008 10.3835 9.79823 9.6127 9.1669 8.57134L9.63054 8.11293C9.63939 8.10501 9.6478 8.0966 9.65573 8.08775C9.66275 8.08133 9.66947 8.07461 9.67589 8.0676V8.0626C9.6829 8.05619 9.68963 8.04946 9.69605 8.04245V8.03746C9.69955 8.0325 9.70291 8.02745 9.70613 8.0223C9.70956 8.01905 9.71292 8.01571 9.71621 8.01231C9.71992 8.00406 9.72328 7.99566 9.72628 7.98712V7.98212L9.73133 7.97213C9.73492 7.96553 9.73828 7.95881 9.7414 7.95198C9.74342 7.94366 9.7451 7.93526 9.74644 7.9268V7.9218C9.7486 7.91181 9.75028 7.90173 9.75148 7.89158V7.88658C9.75181 7.87819 9.75181 7.86978 9.75148 7.86138V7.85639C9.75181 7.848 9.75181 7.8396 9.75148 7.83121V7.82121C9.75014 7.81275 9.74846 7.80434 9.74644 7.79602V7.79102C9.74143 7.77197 9.73468 7.75343 9.72628 7.73562ZM8.57235 7.75292L7.7296 6.36962C7.61935 6.44269 7.50056 6.53039 7.37882 6.63138C7.04758 6.90617 6.84862 7.15073 6.801 7.23876C6.67006 7.48078 6.62226 7.82174 6.71147 8.27912C6.89517 9.22089 7.58778 10.352 8.57658 11.363C9.56693 12.3756 10.6745 13.0889 11.6175 13.2843C12.0702 13.3782 12.4261 13.334 12.6894 13.1962C12.7854 13.146 13.0396 12.9442 13.3288 12.6084C13.4407 12.4785 13.5392 12.3501 13.6217 12.23L12.281 11.3894L12.125 11.5469C11.8202 11.8546 11.352 11.9312 10.9651 11.7365C9.82217 11.1614 8.99726 10.2205 8.31178 9.08976C8.07191 8.6941 8.13479 8.18555 8.46381 7.86024L8.57235 7.75292Z"
                                          fill="white"/>
                                    <path fill-rule="evenodd" clip-rule="evenodd"
                                          d="M3.06807 14.5559C3.22868 14.8024 3.2726 15.1071 3.18816 15.3889L2.47189 17.7791L4.80285 16.9747C5.08644 16.8768 5.399 16.9114 5.6543 17.069C6.91452 17.8468 8.40545 18.2968 10.0003 18.2968C14.5781 18.2968 18.2968 14.5781 18.2968 10.0003C18.2968 5.42245 14.5781 1.70377 10.0003 1.70377C5.42433 1.70377 1.70377 5.42248 1.70377 10.0003C1.70377 11.6622 2.19514 13.2162 3.06807 14.5559ZM2.23024 15.1018C1.2562 13.607 0.703766 11.8645 0.703766 10.0003C0.703766 4.87004 4.8722 0.703766 10.0003 0.703766C15.1304 0.703766 19.2968 4.87017 19.2968 10.0003C19.2968 15.1304 15.1304 19.2968 10.0003 19.2968C8.21523 19.2968 6.5431 18.7927 5.12908 17.92L1.73319 19.092C1.6597 19.1173 1.58063 19.1219 1.5047 19.1052C1.42877 19.0885 1.35891 19.0512 1.30281 18.9974C1.24672 18.9436 1.20656 18.8753 1.18676 18.8001C1.16696 18.7249 1.16828 18.6458 1.19058 18.5713L2.23024 15.1018Z"
                                          fill="white"/>
                                </svg>
                            </a>
                        </div>
                    </div>
                </nav>
            </div>
        </header>
    {/block}
</div>


{block 'main'}
    <main class="page-container">
        <h1 class="au-h1  au-about__title">{$_modx->resource.pagetitle}</h1>
        {block 'content'}
            {$_modx->resource.content}
        {/block}
    </main>
{/block}

{block 'footer'}
    <footer class="au-footer  page-container">
        {if $_modx->resource.template != 6}
        <section class="au-subscribe">
            <script type="text/javascript"
                    src="https://cp.unisender.com/v5/template-editor-new/js/lib/moment/moment-with-langs.min.js"></script>
            <script type="text/javascript"
                    src="https://cp.unisender.com/v5/template-editor-new/js/lib/datepicker/pikaday.js"></script>
            <script type="text/javascript"
                    src="https://cp.unisender.com/v5/template-editor-new/js/app/lang/ru.js"></script>
            <script type="text/javascript"
                    src="https://cp.unisender.com/v5/template-editor-new/js/app/preview/form/form-js.js"></script>
            <h2 class="au-h2 au-subscribe__title">
                <span class="au-subscribe__start">{'stik_newsletter_form_title' | lexicon}</span>
                <span class="au-subscribe__end">{'stik_newsletter_form_title_success' | lexicon}</span>
            </h2>
            <div class="au-subscribe__subtitle-box">
                <p class="au-subscribe__subtitle au-subscribe__start">{'stik_newsletter_form_subtitle' | lexicon}</p>
                <p class="au-subscribe__subtitle au-subscribe__end">{'stik_newsletter_form_subtitle_success' | lexicon}</p>
            </div>
            <form method="POST"
                  action="https://cp.unisender.com/ru/subscribe?hash=6n88d8uguunamkbz3dcn7n3dfz8bn9zqo9h74amsht8kw8wk5kdgo"
                  name="subscribtion_form" us_mode="embed">
                <input type="hidden" name="default_list_id" value="1">
                <input type="hidden" name="overwrite" value="2">
                <input type="hidden" name="is_v5" value="1">
                <input type="hidden" name="language" value="{'cultureKey' | option}">
                <div class="custom-form__group  au-subscribe__input-group">
                    <input class="custom-form__input  au-subscribe__input" type="text" name="email" _validator="email"
                           _required="1" placeholder="{'stik_form_pls_email' | lexicon}"
                           value="{$_modx->user.email | filterFakeEmail}">
                    <button href="javascript:" target="_blank" class="au-subscribe__submit">
                        <svg width="19" height="9" viewBox="0 0 19 9" fill="none" xmlns="http://www.w3.org/2000/svg">
                            <path fill-rule="evenodd" clip-rule="evenodd"
                                  d="M13.3322 0L18.2526 4.3737L13.3322 8.74741L12.6678 8L16.1849 4.8737H0V3.8737H16.1849L12.6678 0.747409L13.3322 0Z"
                                  fill="#1A1714"/>
                        </svg>
                    </button>
                    <spanclass
                    ="error-block"></span>
                    <p class="au-subscribe__politics-text">{'stik_newsletter_form_agree' | lexicon} <a
                                class="au-subscribe__politics-link au-text-tab_js"
                                href="policy">{'stik_ss_form_policy_link' | lexicon}</a></p>
                </div>
            </form>
        </section>
        {/if}
        {*'!AjaxForm' | snippet : [
            'snippet' => 'newsletterSubscribe',
            'form' => 'newsletterSubscribe.form',
            'emailTo' => $_modx->config.ms2_email_manager,
            'subject' => 'stik_newsletter_form_subject' | lexicon,
            'subjectConfirm' => 'stik_newsletter_form_subject_confirm' | lexicon,
            'tpl' => 'newsletterSubscribe.email',
            'tplConfirm' => 'newsletterSubscribeEmailTplConfirm',
            'submitVar' => 'footerform',
        ]*}
        <ul class="au-footer__menu">
            <li class="au-footer__item">
                <a class="au-footer__link" href="{2|url}">{$linkAbout}</a>
            </li>
            {'pdoMenu' | snippet : [
            'parents' => 16,
            'resources' => '20,17,18,19',
            'sortby' => 'menuindex',
            'level' => 1,
            'tplOuter' => '@INLINE {$wrapper}',
            'tpl' => '@INLINE <li class="au-footer__item"><a class="au-footer__link  au-text-tab_js" href="{$link}">{$menutitle}</a></li>',
            ]}
        </ul>
        <ul class="au-footer__menu">
            <li class="au-footer__item">
                <a class="au-footer__link" href="https://t.me/autentiments"
                   target="_blank">{'stik_link_telegram' | lexicon}</a>
            </li>
            <li class="au-footer__item">
                <a class="au-footer__link"
                   href="https://wa.me/{$_modx->config.whatsapp | preg_replace : '/[^0-9]/' : ''}"
                   target="_blank">{'stik_link_whatsapp' | lexicon}</a>
            </li>
        </ul>
        <div class="au-footer__info-box">
            <div class="au-footer__box-link">
                {'pdoMenu' | snippet : [
                'parents' => 16,
                'resources' => '31,30',
                'sortby' => 'menuindex',
                'level' => 1,
                'tplOuter' => '@INLINE {$wrapper}',
                'tpl' => '@INLINE <a class="au-footer__policy-link  au-text-tab_js" href="{$link}">{$menutitle}</a>',
                ]}
            </div>
            <div class="au-footer__year">
                © Autentiments 2023
            </div>
            {if $_modx->resource.template == 18}
                <div class="au-footer__developer">
                    <div class="au-footer__developer-link">Разработка сайта STIK</div>
                </div>
            {/if}
        </div>

        <div class="fixed-buttons">
            <a href="https://t.me/autentiments_bot" target="_blank" class="fixed-buttons__telegram"><img
                        src="https://img.icons8.com/ios/50/ffffff/sent.png"/>&nbsp;Написать</a>
            <a href="whatsapp://send?phone=79215702113" target="_blank" class="fixed-buttons__whatsapp"><img
                        src="https://img.icons8.com/ios/50/ffffff/whatsapp--v1.png"/>&nbsp;Написать</a>
        </div>
    </footer>
{/block}
{block "scripts"}
    <script src="/assets/tpl/js/vendor/swiper-bundle.js"></script>
    <script src="/assets/tpl/js/vendor/intl-tel-input/intlTelInput-jquery.min.js"></script>
    <script src="/assets/tpl/js/vendor/jquery.cookie.js" async></script>
    <script src="/assets/tpl/js/vendor/jquery-ui.min.js" async></script>
    <script src="/assets/tpl/js/vendor/jquery.ui.touch-punch.min.js" async></script>
    <script src="/assets/tpl/js/vendor/scrollreveal.min.js" async></script>
    <script src="/assets/tpl/js/vendor/theia-sticky-sidebar.js" async></script>
    <script src="/assets/tpl/js/vendor/datepicker.js" async></script>
    <script src="/assets/tpl/js/my_scrollreveal.js" async></script>
    <script src="/assets/tpl/js/my_swiper.js" async></script>
    <script src="/assets/tpl/js/script.js" defer></script>
    <script src="/assets/tpl/js/modx.js" defer></script>
    <script src="/assets/js/script.js" async></script>
{/block}
{include 'modals'}
</body>
</html>