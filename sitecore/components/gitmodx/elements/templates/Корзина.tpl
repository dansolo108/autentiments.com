{extends 'template:1'}
{block 'body-padding'}
style="padding-top:{(!$_modx->user.join_loyalty || !$_modx->isAuthenticated('web'))? '118px':'74px'}"
{/block}
{block 'header'}
    <header class="au-header  au-header-cart  container">
        <div class="au-header__head">
            <a class="au-ordering__link-back" href="" onclick="javascript:history.back(); return false;">
                <svg viewBox="0 0 20 20" fill="none" xmlns="http://www.w3.org/2000/svg">
                    <path d="M14.0658 3.28239L13.3587 2.57529L5.93408 9.99992L13.3588 17.4246L14.0659 16.7175L7.34831 9.99991L14.0658 3.28239Z" fill="#1A1714"></path>
                </svg>
                <span class="au-desktop">{'stik_link_back' | lexicon}</span>
            </a>
            <button class="au-close  au-login__header-close" aria-label="{'stik_modal_close' | lexicon}">
                <svg width="20" height="20" viewBox="0 0 20 20" fill="none" xmlns="http://www.w3.org/2000/svg">
                    <path d="M14.0659 3.28239L13.3588 2.57529L5.93414 9.99992L13.3588 17.4246L14.0659 16.7175L7.34837 9.99991L14.0659 3.28239Z" fill="#1A1714"></path>
                </svg>
            </button>
            <a class="au-header__logo" href="{1|url}">
                <svg viewBox="0 0 150 14" fill="none" xmlns="http://www.w3.org/2000/svg">
                    <path d="M5.66708 8.90851C3.75303 8.90851 4.27451 8.88086 3.19798 11.0339L2.44737 12.6655C2.30318 13.4141 2.08985 13.3094 0.578757 13.4181C0.300243 13.4378 0 13.5326 0 13.0961C0 12.0492 0.888876 13.0013 1.45973 11.4428C1.80738 10.4907 6.19645 3.38366 5.68881 1.98516C5.51301 1.50121 5.08239 0.959986 6.38608 0.559004C6.94903 0.38518 7.32631 1.18517 7.53569 1.60985C8.68728 3.94266 11.5534 10.2932 13.181 11.9781C14.625 13.4754 13.0783 13.1909 12.1085 13.0092C10.9016 12.782 11.4606 12.5964 10.3268 10.4334C9.59196 9.03296 9.71443 9.09221 7.53569 8.9737L5.66708 8.90851ZM94.3512 8.47988L98.3768 1.88837C99.1176 0.596535 99.1511 0.582708 100.255 0.977764C101.073 1.27011 100.342 1.20295 100.846 4.23895L102.337 11.4428C102.717 13.0388 103.007 13.2245 101.64 13.5465C100.781 13.748 101.105 13.0309 100.64 10.3149L99.2677 2.70416C97.4998 5.43005 95.0465 9.07246 94.9734 10.1549C94.926 10.866 94.0589 11.1643 93.6204 10.068C93.2786 9.21271 90.0925 4.53919 89.3696 3.45674C89.091 4.64783 87.5187 12.3001 87.6965 13.0092C87.9098 13.8645 86.9321 13.6215 86.3968 13.4714C85.5296 13.2265 86.3019 12.1756 86.7839 10.1865C87.1237 8.7821 88.7513 2.07009 88.2436 1.55652C88.1883 1.50121 87.9572 1.01134 88.4688 0.890851C88.9449 0.780236 89.8041 0.634065 89.9068 1.20295C90.1261 2.40787 93.5453 7.27298 94.3512 8.47988ZM109.636 6.56978L112.824 6.62311C113.31 6.84237 112.145 7.67791 111.933 7.68582L108.67 7.81421C108.67 13.904 112.919 12.7465 113.533 13.0744C113.729 13.1791 114.098 13.9534 112.233 13.9534C110.373 13.9534 109.099 13.5386 108.413 12.709C107.179 11.2176 107.038 9.85665 107.038 8.09272C107.038 5.23054 107.518 3.25329 108.476 2.15701C110.695 -0.373328 114.039 0.444438 113.156 1.50319C112.514 2.27355 109.105 -0.369377 108.67 6.91348L109.636 6.56978ZM118.072 10.8403L118.373 3.92883C118.373 1.88047 116.312 2.58564 116.528 1.67504C116.528 1.67306 117.62 0.48987 118.181 0.622213L124.298 7.41718C125.543 8.75444 126.323 9.56036 126.639 9.83294C126.989 7.7352 127.139 4.49376 126.639 2.49083C126.406 1.56442 127.165 1.48146 128.121 1.48146C129.029 1.48146 128.472 2.91551 128.292 4.10068C127.812 7.25125 127.99 8.80382 127.99 11.6779C127.99 12.1934 126.635 13.2936 126.446 13.0092C125.853 12.1223 126.92 12.2053 123.976 9.03888L119.51 4.31599C119.392 6.82262 119.214 9.98109 119.35 12.4087C119.406 13.3963 119.202 13.264 118.244 13.503C116.449 13.9514 117.675 12.707 117.934 11.8398C118.011 11.5751 118.059 11.2413 118.072 10.8403ZM135.097 2.68243L131.834 2.72589C131.103 2.72589 131.204 1.26615 131.963 1.26615C133.594 1.26615 134.809 1.53282 137.126 1.4064C137.598 1.38072 140.741 1.15554 140.924 1.34122C141.27 1.68689 140.334 2.45922 140.033 2.4691L136.922 2.59749C136.61 4.4029 136.411 8.36334 136.492 10.7119C136.525 11.6858 137.258 12.5312 138.511 12.3771L138.726 12.3435C138.761 12.3435 139.55 12.1282 139.239 13.0527C138.989 13.8092 136.144 14.4077 135.097 12.0216C134.767 11.269 135.008 10.71 135.097 9.78949C135.307 7.60483 135.303 4.9461 135.097 2.68243ZM143.427 12.5588C143.427 11.3105 145.244 13.341 146.356 12.1618C147.86 10.5677 144.006 6.37818 144.006 4.10068C144.006 3.03403 145.736 0 147.506 0C147.913 0 149.223 0.179751 149.223 0.622213C149.223 1.98516 147.482 0.742705 146.26 1.98516C143.907 4.38117 149.384 8.28037 147.569 12.3653C146.805 14.0857 145.714 14.2951 144.146 13.6966C143.599 13.4872 143.427 13.2541 143.427 12.5588ZM5.15153 7.92087H9.03888L6.87002 3.59501C6.62311 3.41131 6.52632 4.13821 6.35447 4.48586L4.78808 7.77075L5.15153 7.92087ZM16.7662 1.52492C16.7662 0.217281 17.4792 0.481968 18.4294 1.11603C20.0392 2.18861 17.8032 1.30369 18.3978 8.36136C18.5657 10.3465 19.4032 11.4566 21.3607 11.9465C25.8031 13.0586 25.6826 3.99797 25.1809 1.99701C24.8826 0.803939 27.0278 1.09431 27.0278 1.95355C27.0278 3.19403 26.2771 8.58654 25.7399 10.0245C25.1611 11.5771 23.3083 13.268 21.5543 13.268C20.2802 13.268 18.3187 12.4956 17.7321 11.3342C16.857 9.60184 16.7662 4.01179 16.7662 1.52492ZM33.3388 2.68243L30.0756 2.72589C29.3448 2.72589 29.4455 1.26615 30.206 1.26615C31.8356 1.26615 33.0504 1.53282 35.3674 1.4064C35.8395 1.38072 38.9821 1.15554 39.1678 1.34122C39.5135 1.68689 38.5772 2.45922 38.277 2.4691L35.1639 2.59749C34.8538 4.4029 34.6543 8.36334 34.7353 10.7119C34.7689 11.6858 35.4997 12.5312 36.7521 12.3771L36.9674 12.3435C37.0029 12.3435 37.791 12.1282 37.4829 13.0527C37.2301 13.8092 34.3857 14.4077 33.3388 12.0216C33.0089 11.269 33.2519 10.71 33.3388 9.78949C33.5501 7.60483 33.5462 4.9461 33.3388 2.68243ZM44.8467 6.56978L48.0348 6.62311C48.5208 6.84237 47.3534 7.67791 47.144 7.68582L43.8808 7.81421C43.8808 13.904 48.1297 12.7465 48.742 13.0744C48.9395 13.1791 49.3089 13.9534 47.4442 13.9534C45.5835 13.9534 44.3095 13.5386 43.6221 12.709C42.3875 11.2176 42.2493 9.85665 42.2493 8.09272C42.2493 5.23054 42.7273 3.25329 43.6873 2.15701C45.9035 -0.373328 49.2496 0.444438 48.3667 1.50319C47.7247 2.27355 44.3134 -0.369377 43.8808 6.91348L44.8467 6.56978ZM53.2832 10.8403L53.5834 3.92883C53.5834 1.88047 51.5232 2.58564 51.7365 1.67504C51.7385 1.67306 52.8308 0.48987 53.3898 0.622213L59.5092 7.41718C60.7537 8.75444 61.5339 9.56036 61.848 9.83294C62.1976 7.7352 62.3497 4.49376 61.848 2.49083C61.6169 1.56442 62.3754 1.48146 63.3294 1.48146C64.24 1.48146 63.681 2.91551 63.5013 4.10068C63.0233 7.25125 63.201 8.80382 63.201 11.6779C63.201 12.1934 61.846 13.2936 61.6564 13.0092C61.0638 12.1223 62.1285 12.2053 59.1873 9.03888L54.7212 4.31599C54.6007 6.82262 54.4249 9.98109 54.5612 12.4087C54.6165 13.3963 54.413 13.264 53.455 13.503C51.6595 13.9514 52.8861 12.707 53.1429 11.8398C53.2219 11.5751 53.2693 11.2413 53.2832 10.8403ZM70.3061 2.68243L67.043 2.72589C66.3121 2.72589 66.4148 1.26615 67.1733 1.26615C68.8029 1.26615 70.0177 1.53282 72.3347 1.4064C72.8088 1.38072 75.9495 1.15554 76.1352 1.34122C76.4809 1.68689 75.5446 2.45922 75.2443 2.4691L72.1313 2.59749C71.8212 4.4029 71.6217 8.36334 71.7027 10.7119C71.7362 11.6858 72.4691 12.5312 73.7214 12.3771L73.9347 12.3435C73.9703 12.3435 74.7584 12.1282 74.4503 13.0527C74.1974 13.8092 71.353 14.4077 70.3061 12.0216C69.9763 11.269 70.2192 10.71 70.3061 9.78949C70.5175 7.60483 70.5135 4.9461 70.3061 2.68243ZM79.7736 6.80484C79.7736 6.26559 79.823 3.28489 79.5386 2.96292C78.8255 2.15503 79.2719 1.83108 80.0324 1.22467C81.1188 0.35555 81.5988 1.33134 81.5988 1.33134C81.5988 1.52887 81.3736 2.17281 81.342 3.26316L81.1919 11.743C81.1919 12.7011 81.1978 13.8684 80.5045 13.8684C80.1904 13.8684 79.9889 13.5386 79.904 12.8808C79.8171 12.223 79.7736 10.1964 79.7736 6.80484Z" fill="black"></path>
                </svg>
            </a>
        </div>
    </header>
{/block}

{block 'main'}
    {if $.get['msorder']}
        {$_modx->sendRedirect(26 | url : [] : ['msorder' => $.get['msorder']])}
    {/if}
    {set $msOrder = '!msOrder' | snippet : [
        'tpl' => 'stik.msOrder',
        'userFields' => '{"msloyalty":"","name":"name","surname":"surname","phone":"mobilephone","building":"building","corpus":"corpus","entrance":"entrance","room":"room"}'
    ]}
    {*"country":"","city":"","index":"",*}
    {if $msOrder}
        {$msOrder}
        {* Подключаем скрипты для СДЭК и DaData *}
        {'!MinifyX' | snippet : [
            'minifyCss' => 1,
            'minifyJs' => 1,
            'registerCss' => 'default',
            'registerJs' => 'default',
            'cssSources' => '
                /assets/components/stik_cdek/css/web/jquery.fancybox.min.css,
                /assets/components/stik_cdek/css/web/auto-complete.css,
                /assets/components/stik_cdek/css/web/cdek.css,
            ',
            'jsSources' => '
                /assets/components/stik_cdek/js/web/deliveryPoints.js,
                /assets/components/stik_cdek/js/web/cdek.default.js,
                /assets/components/stik_cdek/js/web/autocomplete/auto-complete.min.js,
                /assets/components/stik_cdek/js/web/autocomplete/init.js,
                /assets/components/stik_cdek/js/web/jquery.fancybox.min.js,
            ',
        ]}
    {else}
        <main class="au-ordering  page-container ajax-loader-block">
            <div class="au-cart__empty" style="display:block;">
                <h3 class="au-h1  au-cart__title">{'stik_empty_basket_title' | lexicon}</h3>
                <p class="au-cart__text">{'stik_empty_basket_text' | lexicon}</p>
                <a class="au-btn" href="{7|url}">{'stik_empty_basket_view_catalog' | lexicon}</a>
            </div>
        </main>
    {/if}
    <script>
        //gtag('event', 'begin_checkout', { value: 0, currency: 'RUB' } );
        
        document.addEventListener("DOMContentLoaded", () => {
            //г. Санкт-Петербург, Басков переулок, 26
            //г. Москва, ул. Пятницкая, 7, стр.5
            $('.au-ordering__delivery').change(function(event){ 
                if (event.target.closest('#delivery_7')) {
                    $('#street').val('ул. Пятницкая').attr('data-val','ул. Пятницкая');
                    $('#building').val('7').attr('data-val','7');
                    $('#corpus').val('5').attr('data-val','5');
                    $('#entrance').val('').attr('data-val','');
                    $('#room').val('').attr('data-val','');
                }
                else if(event.target.closest('#delivery_6')){
                    $('#street').val('Басков переулок').attr('data-val','Басков переулок');
                    $('#building').val('26').attr('data-val','26');
                    $('#corpus').val('').attr('data-val','');
                    $('#entrance').val('').attr('data-val','');
                    $('#room').val('').attr('data-val','');
                }
            })
        });

    </script>
{/block}

{block 'footer'}
{/block}