{extends 'template:1'}

{block 'js-params'}
    <script>
        let history = JSON.parse(sessionStorage.history);
        let last = 'catalog';
        history.forEach(item=>{
            if(item.indexOf('/catalog/') !== -1)
                return;
            else if(item.indexOf('/sale') !== -1)
                last = 'sale';
            else if(item.indexOf('/new') !== -1)
                last = 'new';
            else if(item.indexOf('/catalog') !== -1)
                last = 'catalog';
        })
        if(last === 'sale'){
            PIXEL_ID = SALE_PIXEL_ID;
            PRICE_LIST_ID = SALE_PRICE_LIST_ID;
        }
    </script>
{/block}

{block 'main'}
    <script type="text/javascript">
        document.addEventListener('DOMContentLoaded',e=>{
            let thisPage = {$_modx->resource.id | getJSONPageInfo};
            {ignore}
            PageInfo = { ...PageInfo, ...thisPage };
            setTimeout(()=>{
                setEvent('view_item',PageInfo);
            },500);
            {/ignore}
        });
        var _tmr = _tmr || [];
    </script>
    {set $colors = 'getProductDetails' | snippet : [
        'id'=>$_modx->resource.id,
        'details'=>['color'],
    ]}
    {set $activeColor = $.get['color']?$.get['color']:$colors[0]['value']}
    {set $sizes = 'getModifications' | snippet : [
        'where'=>[
            'Modification.product_id' => $_modx->resource.id,
            'color'=>$activeColor,
            'Modification.hide'=>0,
        ],
        'details'=>[
            'color',
            'size'
        ],
        'sortby'=>['size'=>'ASC'],
        'groupby'=>['Modification.product_id','size'],
    ]}
    {set $activeSize = $.get['size']?:false}
    {set $sizesOutput = ''}
    {set $remains = 0}
    {set $hide_remains = $_modx->resource.soon}
    {foreach $sizes as $size}
        {if $activeSize === false && $size['remains'] > 0 && !$size['soon'] && !$size['hide_remains']}
            {set $activeSize = $size['size']}
        {/if}
        {if $activeSize == $size['size']}
            {set $remains = $size['remains']}
            {set $hide_remains = $_modx->resource.soon?:$size['hide_remains']}
        {/if}
    {/foreach}
    {if $activeSize === false}
        {set $activeSize = $sizes[0]['size']}
    {/if}
    {foreach $sizes as $size}
        {set $size['activeSize'] = $activeSize}
        {set $sizesOutput = $sizesOutput ~ $_modx->getChunk('product.size',$size)}
    {/foreach}
    <main id="msProduct" class="au-product" itemtype="http://schema.org/Product" itemscope>
        <meta itemprop="name" content="{$_modx->resource.pagetitle}">
        <meta itemprop="description" content="{$_modx->resource.description ? $_modx->resource.description : $_modx->resource.pagetitle}">
        {* При изменении параметров вызова, также поправить в assets/components/stik/getAjaxMsGallery.php *}
        {'!msGallery' | snippet : [
            'tpl' => 'stik.msGallery',
            'where' => [
                'description' => $activeColor,
            ],
        ]}
        <div class="au-product__description" itemtype="http://schema.org/AggregateOffer" itemprop="offers" itemscope>
            <meta itemprop="category" content="{$_modx->resource.parent | resource: "pagetitle"}">
            <meta itemprop="offerCount" content="1">
            <meta itemprop="price" content="{$price | replace:" ":""}">
            <meta itemprop="lowPrice" content="{$price | replace:" ":""}">
            <meta itemprop="priceCurrency" content="RUR">
            <div class="au-product__marks">
                {if $_modx->resource.new?}
                    <span class="au-product__mark">New</span>
                {/if}
                {if $_modx->resource.soon?}
                    <span class="au-product__mark">Soon</span>
                {/if}
                {if $_modx->resource.sale?}
                    {set $discount = $old_price | discount : $price}
                    {if $discount > 0}
                        <span class="au-product__mark">Sale -{$discount}%</span>
                    {/if}
                {/if}
            </div>
            <h1 class="au-h1  au-product__title">{$_modx->resource.pagetitle}</h1>
            <div class="au-card__row">
                <div class="au-card__price-box">
                    {'!getModifications' | snippet : [
                        'where'=>[
                            'Modification.product_id' => $_modx->resource.id,
                            'color'=>$activeColor,
                            'size'=>$activeSize
                        ],
                        'details'=>[
                            'color',
                            'size',
                        ],
                        'groupby'=>['Modification.product_id','color','size'],
                        'tpl'=>'product.row.price'
                    ]}
                </div>
                <a class="au-product__like msfavorites" href="" aria-label="Добавить в избранное" data-click="" data-data-list="default" data-data-type="resource" data-data-key="{$_modx->resource.id}">
                    <svg width="24" height="23" viewBox="0 0 20 19" fill="none" xmlns="http://www.w3.org/2000/svg">
                        <path d="M9.6631 0.0736498C9.54545 0.110837 9.39572 0.201154 9.32086 0.270219C9.24599 0.339286 8.60963 1.55059 7.8877 3.00627C6.88235 5.02511 6.55615 5.63608 6.45989 5.67858C6.39572 5.70514 5.14439 5.8964 3.68984 6.10359C2.23529 6.31079 0.935829 6.50205 0.802139 6.53393C0.358289 6.63487 0.00534759 7.04395 0 7.45303C0 7.89398 0.0267379 7.92586 2.25668 10.0988C3.90374 11.7085 4.38503 12.2026 4.38503 12.3035C4.38503 12.3726 4.16578 13.6955 3.8984 15.2362C3.35294 18.4026 3.35829 18.3122 3.74332 18.6948C3.97861 18.9285 4.30481 19.0401 4.59358 18.987C4.70053 18.9657 5.94652 18.3441 7.36898 17.6003C8.7861 16.8566 9.97326 16.2509 10.0053 16.2509C10.0321 16.2509 11.2193 16.8566 12.6364 17.6003C14.0535 18.3441 15.2995 18.9657 15.4064 18.987C15.6952 19.0401 16.0214 18.9285 16.2567 18.6948C16.6417 18.3122 16.6471 18.4026 16.1016 15.2415C15.8342 13.7008 15.615 12.3779 15.615 12.3089C15.615 12.2026 16.0535 11.7457 17.7112 10.136C19.8663 8.03743 20 7.88336 20 7.4849C20 7.1077 19.6952 6.68799 19.3369 6.56049C19.246 6.52861 15.6578 5.99734 14.0909 5.78483C13.8556 5.75295 13.6043 5.69983 13.5401 5.67326C13.4385 5.63608 13.123 5.03574 12.1123 3.00627C10.738 0.238343 10.6952 0.163964 10.2781 0.0470861C10.0214 -0.0219809 9.91444 -0.016667 9.6631 0.0736498Z" fill="#ccc"/>
                    </svg>
                </a>
            </div>
            <form class="ms2_form" method="post">
                <input type="hidden" name="count" value="1"/>
                <input type="hidden" name="product_id" value="{$_modx->resource.id}"/>
                <div class="au-product__color-box">
                    <span class="au-product__subtitle">{('ms2_product_color') | lexicon}:</span>
                    <div class="au-product__colors">
                        {foreach $colors as $color}
                            {include 'product.color' idx=$color['idx'] value=$color['value'] activeColor=$activeColor}
                        {/foreach}
                    </div>
                </div>
                <div class="au-product__size-box">
                    <span class="au-product__subtitle">{'stik_product_size' | lexicon}:</span>
                    <div id="ajax_sizes" class="au-product__sizes">
                        {$sizesOutput}
                    </div>
                </div>
                <button type="button" class="au-product__info-size">{'stik_modal_size_info_link' | lexicon}</button>
                <div class="au-product__add-box">
                    <button class="au-btn  au-product__add-basket {if !$hide_remains}active{/if}" type="submit" name="ms2_action" value="cart/add">{'ms2_frontend_add_to_cart' | lexicon}</button>
                    <button class="au-btn-light  au-product__add-entrance {if $hide_remains || $remains == 0}active{/if}" type="button">
                        <span class="entrance">{'stik_product_subscribe_button' | lexicon}</span>
                        <span class="entrance_end">{'stik_product_subscribe_success' | lexicon}</span>
                    </button>
                    <div class="au-btn-light  au-product__add-size">{'stik_product_select_size' | lexicon}</div>
                </div>
                <button type="button" class="au-product__info-remains">{'stik_modal_remains_info_link' | lexicon}</button>
            </form>
            <div class="au-product__description-text">
                {$_modx->resource.content}
                {if $_modx->resource.article?}
                    <span class="au-product__article">{'stik_product_article' | lexicon}: {$_modx->resource.article}</span>
                {/if}
            </div>
            
            {set $measurements = $_modx->resource.measurements | strip}
            {set $model_params = $_modx->resource.model_params | strip}
            {set $care = $_modx->resource.care | strip}

            <div class="au-product__info">
                <div class="au-product__accordeon">
                    {if $measurements}
                        <button class="au-product__info-title  au-accordeon-title">{'stik_product_measurements_title' | lexicon}</button>
                        <div class="au-product__info-content  au-accordeon-content">
                            <div class="au-product__info-group">
                                {$_modx->resource.measurements | striptags | nl2br}
                            </div>
                        </div>
                    {/if}
                </div>
                <div class="au-product__accordeon">
                    {if $care}
                        <button class="au-product__info-title  au-accordeon-title">{'stik_product_care_title' | lexicon}</button>
                        <div class="au-product__info-content  au-product__info-content_care  au-accordeon-content">
                            {$_modx->resource.care | striptags | nl2br}
                        </div>
                    {/if}
                </div>
                <div class="au-product__accordeon">
                    {if $model_params}
                        <button class="au-product__info-title  au-accordeon-title">{'stik_product_model_params' | lexicon}</button>
                        <div class="au-product__info-content  au-accordeon-content">
                            <div class="au-product__info-group">
                                {$_modx->resource.model_params | striptags | nl2br}
                            </div>
                        </div>
                    {/if}
                </div>
                <div class="au-product__accordeon">
                    <button class="au-product__info-title  au-accordeon-title">{'stik_product_delivery_title' | lexicon}</button>
                    <div class="au-product__info-content  au-product__info-content_delivery  au-accordeon-content">
                        {'pdoField' | snippet : [
                            'id' => 544,
                            'field' => 'content',
                        ]}
                    </div>
                </div>
            </div>
        </div>
    </main>

    {set $complete_look = 'getModifications' | snippet : [
        'parents' => 7,
        'link' => 1,
        'limit' => 4,
        'details'=>['color'],
        'groupby'=>['Modification.product_id,color'],
        'master' => $_modx->resource.id,
        'tpl' => 'swiper.row',
        'includeThumbs' => 'category',
    ]}

    {if $complete_look}
        <section class="au-сomplete">
            <h2 class="au-h2  au-сomplete__title">{'stik_product_complete_look_title' | lexicon}</h2>
            <div class="au-сomplete__row">
                {$complete_look}
            </div>
        </section>
    {/if}

    {set $categories = $_modx->resource.parent | resource : 'categories'}
    {set $also_like = 'getModifications' | snippet : [
        'parents' => $categories ?: $_modx->resource.parent,
        'resources' => -$_modx->resource.id,
        'tpl' => 'swiper.row',
        'limit' => 8,
        'details'=>['color'],
        'groupby'=>['Modification.product_id'],
        'includeThumbs' => 'category',
        'sortby' => 'Rand()',
    ]}

    {if $also_like?}
        <section class="au-liked  au-liked_container">
            <h2 class="au-h2  au-liked__title">{'stik_product_also_like_title' | lexicon}</h2>
            <div class="au-liked__slider  swiper-container">
                <div class="swiper-wrapper">
                    {$also_like}
                </div>
            </div>
            <div class="au-swiper-buttons  au-desktop_xl">
                <div class="au-liked__prev  au-swiper-button-prev  swiper-button-prev"></div>
                <div class="au-liked__next  au-swiper-button-next  swiper-button-next"></div>
            </div>
            <div class="au-liked__pagination  au-swiper-pagination  swiper-pagination"></div>
        </section>
    {/if}
    {set $looked = '!looked' | snippet : [
        'tpl' => 'looked.row',
        'tplOuter'=>'@INLINE [[+output]]',
        'limit'=>12,
    ]}

    {if $looked?}
        <section class="au-liked  au-liked_container">
            <h2 class="au-h2  au-liked__title">{'stik_product_looked' | lexicon}</h2>
            <div class="au-liked__slider  swiper-container">
                <div class="swiper-wrapper">
                    {$looked}
                </div>
            </div>
            <div class="au-swiper-buttons  au-desktop_xl">
                <div class="au-liked__prev  au-swiper-button-prev  swiper-button-prev"></div>
                <div class="au-liked__next  au-swiper-button-next  swiper-button-next"></div>
            </div>
            <div class="au-liked__pagination  au-swiper-pagination  swiper-pagination"></div>
        </section>
    {/if}
    {'!addLooked' | snippet : [
    	'templates'=>'3',
    	'limit'=>'12',
    ]}
    <div class="au-modal au-modal-entrance  modal">
        <div class="au-modal__wrapper  au-entrance">
            <button class="au-close au-entrance__close" aria-label="{'stik_modal_close' | lexicon}"></button>
            <div class="au-modal__content  au-entrance__content">
                <h3 class="au-h1  au-entrance__title">{'stik_ss_form_title' | lexicon}</h3>
                <form class="au-entrance__form" id="size_subscribe_form">
                    <input type="hidden" name="modification_id">
                    <div class="custom-form__group">
                        <p class="au-entrance__text">{'stik_ss_form_caption' | lexicon}</p>
                        <span class="au-entrance__size">{'stik_ss_size' | lexicon} <span class="selected-size_js"></span></span>
                        <span class="au-entrance__size">{'ms2_product_color' | lexicon} <span class="selected-color_js"></span></span>
                        <input class="custom-form__input  au-subscribe__input" type="tel" name="phone" placeholder="Введите телефон" value="{$_modx->user.mobilephone | filterFakeEmail}" data-val="{$_modx->user.mobilephone | filterFakeEmail}">
                        <span class="error_phone"></span>
                        <button class="au-btn  au-entrance__submit" type="submit">{'stik_ss_form_submit' | lexicon}</button>
                        <p class="au-subscribe__politics-text">{'stik_ss_form_agree1' | lexicon} <a class="au-subscribe__politics-link au-text-tab_js" href="policy">{'stik_ss_form_policy_link' | lexicon}</a></p>
                    </div>
                </form>
            </div>
        </div>
    </div>
    
    <div class="au-modal  au-modal-size  modal  au-info-size">
        <div class="au-info-size__wrapper">
            <button class="au-close au-modal-size__close" aria-label="{'stik_modal_close' | lexicon}"></button>
            <div class="au-modal__content  au-info-size__content">
                <h3 class="au-info-size__title">{'stik_modal_size_title' | lexicon}</h3>
                <div class="au-info-size__group">
                    <ul class="au-info-size__list">
                        <li class="au-info-size__item">SIZE</li>
                        <li class="au-info-size__item">RUS</li>
                        <li class="au-info-size__item">EUR</li>
                        <li class="au-info-size__item">UK</li>
                        <li class="au-info-size__item">US</li>
                        <li class="au-info-size__item">IT</li>
                    </ul>
                    <div class="au-info-size__table-box">
                        <table class="au-info-size__table">
                            <tr class="au-info-size__tr">
                                <td class="au-info-size__td">XS</td>
                                <td class="au-info-size__td">S</td>
                                <td class="au-info-size__td">M</td>
                                <td class="au-info-size__td">L</td>
                                <td class="au-info-size__td">XL</td>
                                <td class="au-info-size__td">FREE SIZE</td>
                            </tr>
                            <tr class="au-info-size__tr">
                                <td class="au-info-size__td">42</td>
                                <td class="au-info-size__td">44</td>
                                <td class="au-info-size__td">46</td>
                                <td class="au-info-size__td">48</td>
                                <td class="au-info-size__td">50</td>
                                <td class="au-info-size__td">42-46</td>
                            </tr>
                            <tr class="au-info-size__tr">
                                <td class="au-info-size__td">36</td>
                                <td class="au-info-size__td">38</td>
                                <td class="au-info-size__td">40</td>
                                <td class="au-info-size__td">42</td>
                                <td class="au-info-size__td">44</td>
                                <td class="au-info-size__td">36-42</td>
                            </tr>
                            <tr class="au-info-size__tr">
                                <td class="au-info-size__td">10</td>
                                <td class="au-info-size__td">12</td>
                                <td class="au-info-size__td">14</td>
                                <td class="au-info-size__td">16</td>
                                <td class="au-info-size__td">18</td>
                                <td class="au-info-size__td">10-16</td>
                            </tr>
                            <tr class="au-info-size__tr">
                                <td class="au-info-size__td">8</td>
                                <td class="au-info-size__td">10</td>
                                <td class="au-info-size__td">12</td>
                                <td class="au-info-size__td">14</td>
                                <td class="au-info-size__td">16</td>
                                <td class="au-info-size__td">8-14</td>
                            </tr>
                            <tr class="au-info-size__tr">
                                <td class="au-info-size__td">40</td>
                                <td class="au-info-size__td">42</td>
                                <td class="au-info-size__td">44</td>
                                <td class="au-info-size__td">46</td>
                                <td class="au-info-size__td">48</td>
                                <td class="au-info-size__td">40-46</td>
                            </tr>
                        </table>
                    </div>
                </div>
                <div class="au-info-size__group">
                    <ul class="au-info-size__list">
                        <li class="au-info-size__item">RUS</li>
                        <li class="au-info-size__item">{'stik_modal_size_chest_girth' | lexicon}</li>
                        <li class="au-info-size__item">{'stik_modal_size_hip_girth' | lexicon}</li>
                        <li class="au-info-size__item">{'stik_modal_size_waist_girth' | lexicon}</li>
                    </ul>
                    <div class="au-info-size__table-box">
                        <table class="au-info-size__table">
                            <tr class="au-info-size__tr">
                                <td class="au-info-size__td">XS</td>
                                <td class="au-info-size__td">S</td>
                                <td class="au-info-size__td">M</td>
                                <td class="au-info-size__td">L</td>
                                <td class="au-info-size__td">XL</td>
                                <td class="au-info-size__td">FREE SIZE</td>
                            </tr>
                            <tr class="au-info-size__tr">
                                <td class="au-info-size__td">82-85</td>
                                <td class="au-info-size__td">86-89</td>
                                <td class="au-info-size__td">90-93</td>
                                <td class="au-info-size__td">94-97</td>
                                <td class="au-info-size__td">98-102</td>
                                <td class="au-info-size__td">82-95</td>
                            </tr>
                            <tr class="au-info-size__tr">
                                <td class="au-info-size__td">90-93</td>
                                <td class="au-info-size__td">94-97</td>
                                <td class="au-info-size__td">96-101</td>
                                <td class="au-info-size__td">102-105</td>
                                <td class="au-info-size__td">105-108</td>
                                <td class="au-info-size__td">90-105</td>
                            </tr>
                            <tr class="au-info-size__tr">
                                <td class="au-info-size__td">66-69</td>
                                <td class="au-info-size__td">70-73</td>
                                <td class="au-info-size__td">74-77</td>
                                <td class="au-info-size__td">78-81</td>
                                <td class="au-info-size__td">82-85</td>
                                <td class="au-info-size__td">60-76</td>
                            </tr>
                        </table>
                    </div>
                </div>
                <div class="au-info-size__info">
                    {'pdoField' | snippet : [
                        'id' => 547,
                        'field' => 'content',
                    ]}
                </div>
            </div>
        </div>
    </div>
    
    <div class="au-modal  au-modal-remains  modal">
        <div class="au-info-size__wrapper">
            <button class="au-close au-modal-size__close" aria-label="{'stik_modal_close' | lexicon}"></button>
            <div class="au-modal__content  au-info-size__content">
                <h3 class="au-info-size__title">{'stik_modal_remains_title' | lexicon}</h3>
                <div class="au-info-size__group">
                    <div class="au-info-size__table-box">
                        {'!pdoResources' | snippet: [
                            'class' => 'stikStore',
                            'sortby' => 'id',
                            'sortdir' => 'ASC',
                            'tpl' => 'stik.stikStore.availability.city',
                        ]}
                    </div>
                </div>
            </div>
        </div>
    </div>
{/block}