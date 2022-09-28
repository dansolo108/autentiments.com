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
    <script>
        document.addEventListener('DOMContentLoaded',e=>{
            let thisPage = {$_modx->resource.id | getJSONPageInfo};
            {ignore}
            PageInfo = { ...PageInfo, ...thisPage };
            setTimeout(()=>{
                setEvent('view_item',PageInfo);
            },500);
            {/ignore}
        });
        
    </script>
    {set $colors = 'getProductDetails' | snippet : [
        'id'=>$_modx->resource.id,
        'details'=>['color'],
    ]}
    {set $activeColor = $.get['color']?$.get['color']:$colors[0]['value']}
    {set $sizes = 'getProductDetails' | snippet : [
        'id'=>$_modx->resource.id,
        'details'=>['size'],
    ]}
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
        {if !$activeSize}
            {set $activeSize = $size['size']}
        {/if}
        {if $activeSize == $size['size']}
            {set $remains = $size['remains']}
            {set $hide_remains = $_modx->resource.soon?:$size['hide_remains']}
        {/if}
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
                    <svg width="24" height="21" viewBox="0 0 24 21" fill="none" xmlns="http://www.w3.org/2000/svg">
                        <path d="M22.0734 1.89911C23.2303 3.06919 23.9114 4.60633 23.9912 6.22747C24.1984 10.4375 20.738 13.252 15.5001 17.5124L15.4964 17.5154C14.5143 18.3142 13.4987 19.1404 12.4162 20.048L11.9532 20.4363L11.4902 20.048C10.4683 19.1911 9.50748 18.4064 8.57835 17.6477L8.57795 17.6473C3.29185 13.3305 -0.200334 10.4787 0.00891556 6.22747C0.0888843 4.60256 0.751369 3.06744 1.87431 1.90486C3.04852 0.689098 4.61284 0.012664 6.27901 0.000175732C7.87224 -0.0111895 9.27648 0.546021 10.4506 1.65802C11.1189 2.29101 11.6083 3.01062 11.9532 3.63925C12.2981 3.01062 12.7876 2.29101 13.4559 1.65802C14.6298 0.545976 16.0332 -0.0113692 17.6274 0.000175732C19.296 0.0127089 20.8749 0.687076 22.0734 1.89911Z" fill="#CCCCCC"></path>
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
                    <button class="au-btn  au-product__add-basket {if !$hide_remains}active{/if}" onclick="{*fbq('track', 'AddToCart',{ currency: 'RUB', content_type:'product', content_ids: {$_modx->resource.id}, content_name:'{$_modx->resource.pagetitle}',   value: {$_modx->resource.price|replace:',':'.'} });*}gtag('event', 'add_to_cart', { items: [ { id: {$_modx->resource.id}, name: '{$_modx->resource.pagetitle}', category: '{$_modx->resource.parent | resource : 'pagetitle'}', quantity: 1, price: {$_modx->resource.price|replace:',':'.'|replace:' ':''} } ] });" type="submit" name="ms2_action" value="cart/add">{'ms2_frontend_add_to_cart' | lexicon}</button>
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

    {set $complete_look = 'msProducts' | snippet : [
        'parents' => 7,
        'link' => 1,
        'limit' => 4,
        'master' => $_modx->resource.id,
        'tpl' => 'stik.msProducts.row',
        'includeThumbs' => 'category',
        'where' => [
            'Data.image:!=' => null,
        ]
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
    {* modal *}
    {'!AjaxForm' | snippet : [
        'snippet' => 'sizeSubscribe',
        'form' => 'sizeSubscribe.form',
        'tpl' => 'sizeSubscribeEmailTplConfirm',
        'subject' => 'stik_size_subscribe_subject' | lexicon,
    ]}
    
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