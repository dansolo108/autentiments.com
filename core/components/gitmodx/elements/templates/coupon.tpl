{extends 'template:1'}

{block 'main'}
    <script>
        fbq('track', 'ViewContent',{ currency:'RUB',content_type:'product', content_ids: {$_modx->resource.id}, content_name:'{$_modx->resource.pagetitle}', value: {$_modx->resource.price|replace:',':'.'|replace:' ':''} });
        gtag('event', 'view_item', {
          "items": [
            {
              "id": {$_modx->resource.id},
              "name": '{$_modx->resource.pagetitle}',
              "category": '{$_modx->resource.parent | resource : 'pagetitle'}',
              "quantity": 1,
              "price": {$_modx->resource.price|replace:',':'.'|replace:' ':''}
            }
          ]
        });
    </script>
    <main id="msProduct" class="au-product" itemtype="http://schema.org/Product" itemscope>
        <meta itemprop="name" content="{$_modx->resource.pagetitle}">
        <meta itemprop="description" content="{$_modx->resource.description ?: $_modx->resource.pagetitle}">
        
        {* При изменении параметров вызова, также поправить в assets/components/stik/getAjaxMsGallery.php *}
        {'!msGallery' | snippet : [
            'tpl' => 'stik.msGallery',
            'where' => [
                'description' => $_modx->getPlaceholder('first_color_' ~ $_modx->resource.id) ?: $_modx->resource.color[0],
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
                    {'!getColorPrice' | snippet : [
                        'id' => $id
                        'tpl' => 'stik.productPrices.tpl'
                    ]}
                </div>
                <a class="au-product__like msfavorites" href="" aria-label="Добавить в избранное" data-click="" data-data-list="default" data-data-type="resource" data-data-key="{$_modx->resource.id}">
                    <svg width="24" height="21" viewBox="0 0 24 21" fill="none" xmlns="http://www.w3.org/2000/svg">
                        <path d="M22.0734 1.89911C23.2303 3.06919 23.9114 4.60633 23.9912 6.22747C24.1984 10.4375 20.738 13.252 15.5001 17.5124L15.4964 17.5154C14.5143 18.3142 13.4987 19.1404 12.4162 20.048L11.9532 20.4363L11.4902 20.048C10.4683 19.1911 9.50748 18.4064 8.57835 17.6477L8.57795 17.6473C3.29185 13.3305 -0.200334 10.4787 0.00891556 6.22747C0.0888843 4.60256 0.751369 3.06744 1.87431 1.90486C3.04852 0.689098 4.61284 0.012664 6.27901 0.000175732C7.87224 -0.0111895 9.27648 0.546021 10.4506 1.65802C11.1189 2.29101 11.6083 3.01062 11.9532 3.63925C12.2981 3.01062 12.7876 2.29101 13.4559 1.65802C14.6298 0.545976 16.0332 -0.0113692 17.6274 0.000175732C19.296 0.0127089 20.8749 0.687076 22.0734 1.89911Z" fill="#CCCCCC"></path>
                    </svg>
                </a>
            </div>
            <form class="ms2_form" method="post">
                <input type="hidden" name="id" value="{$_modx->resource.id}"/>
                <input type="hidden" name="count" value="1"/>
                <div class="au-product__add-box">
                    <button class="au-btn  au-product__add-basket active" onclick="fbq('track', 'AddToCart',{ currency: 'RUB', content_type:'product', content_ids: {$_modx->resource.id}, content_name:'{$_modx->resource.pagetitle}',   value: {$_modx->resource.price|replace:',':'.'} });gtag('event', 'add_to_cart', { items: [ { id: {$_modx->resource.id}, name: '{$_modx->resource.pagetitle}', category: '{$_modx->resource.parent | resource : 'pagetitle'}', quantity: 1, price: {$_modx->resource.price|replace:',':'.'|replace:' ':''} } ] });" type="submit" name="ms2_action" value="cart/add">{'ms2_frontend_add_to_cart' | lexicon}</button>
                </div>
            </form>
            <div class="au-product__description-text">
                {$_modx->resource.content}
                {if $_modx->resource.article?}
                    <span class="au-product__article">{'stik_product_article' | lexicon}: {$_modx->resource.article}</span>
                {/if}
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
    {set $also_like = 'msProducts' | snippet : [
        'parents' => $categories ?: $_modx->resource.parent,
        'resources' => -$_modx->resource.id,
        'tpl' => 'stik.msProducts.swiper.row',
        'limit' => 8,
        'includeThumbs' => 'category',
        'sortby' => 'Rand()',
        'where' => [
            'Data.image:!=' => null,
        ]
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