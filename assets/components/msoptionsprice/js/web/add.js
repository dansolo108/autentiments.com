$(document).on('msoptionsprice_product_action', function (e, action, form, r) {
    if (action == 'modification/get' && r.success && r.data) {
        var m = r.data.modification || {};

        var thumbs = m.thumbs || {main: ['default.png']};
        var fotorama = $(form).closest(msOptionsPrice.Product.parent).find('.fotorama').data('fotorama');

        if (fotorama) {
            var images = [];
            (thumbs.main || []).filter(function (href) {
                images.push({img: href, caption: ''})
            });
            fotorama.load(images);
        }
    }
});


$(document).on('msoptionsprice_product_action', function (e, action, form, r) {
    if (action == 'modification/get' && r.success && r.data) {
        var m = r.data.modification || {};
        var thumbs = m.thumbs || {main: ['default.png']};

        if (thumbs.main[0]) {
            var img = $(form).find('.true img');
            img.attr('src', thumbs.main[0]);
        }
    }
});


$(document).on('msoptionsprice_product_action', function (e, action, form, r) {
    if (action == 'modification/get' && r.success && r.data) {
        var m = r.data.modification || {};

        if (!m.cost) {
            setTimeout((function () {
                $(form).find('.msoptionsprice-cost').html('<p>Цена по согласованию</p>');
            }.bind(this)), msOptionsPrice.timeout);
        }

    }
});

$(document).on('msoptionsprice_product_action', function (e, action, form, r) {
    if (action === 'modification/get' && r.success && r.data) {
        var m = r.data.modification || {};
        var o = r.data.options || {};

        var cartButton = $(form).find('button[value="cart/add"]');

        if (!m['count']) {
            cartButton.attr('disabled', true).prop('disabled', true);
            miniShop2.Message.error('нет в наличии');
        }
        else {
            cartButton.attr('disabled', false).prop('disabled', false);
        }
    }
});


$(document).on('msoptionsprice_product_action', function (e, action, form, r) {
    if (action == 'modification/get' && r.success && r.data) {
        var m = r.data.modification || {};
        var thumbs = m.thumbs || {main: []};

        if (thumbs.main[0]) {
            var img = $(form).find('.img-wrapper img');
            img.attr('src', thumbs.main[0]);
        }
    }
});


$(document).on('msoptionsprice_product_action', function (e, action, form, r) {
    if (action == 'modification/get' && r.success && r.data) {
        var m = r.data.modification || {};
        if (m) {
            $('.id').val(m.id || '');
            $('.name').val(m.name || '');
            $('.article').val(m.article || '');
        }
    }
});

$(document).on('msoptionsprice_product_action', function (e, action, form, r) {
    if (action == 'modification/get' && r.success && r.data) {
        var m = r.data.modification || {};

        console.log(m);
    }
});



$(document).on('msoptionsprice_product_action', function (e, action, form, r) {
    if (action == 'modification/get' && r.success && r.data) {
        var m = r.data.modifications || {};

        for (i = 0; i < m.length; i += 1) {
            if (m[i].type == 2 && m[i].options) {
                var tid= 'sel_option_' + Object.keys(m[i].options)[0];
                document.getElementById(tid).innerHTML='<span class="price_view">' + m[i]['price'] + ' ₽</span>';
            }
        }
    }
});



$(document).on('msoptionsprice_product_action', function (e, action, form, r) {
    if (action === 'modification/get' && r.success && r.data) {
        var m = r.data.modification || {};
        var thumbs = m.thumbs || {main: []};

        if (thumbs.main[0]) {
            var index = $('.gallery-top').find('[data-fancybox="gallery"][href="'+thumbs.main[0]+'"]').index('a[data-fancybox="gallery"]');
            if (index >= 0) {
                $('.gallery-top')[0].swiper.slideTo(index);
            }
        }
    }
});


$(document).on('msoptionsprice_product_action', function (e, action, form, r) {
    if (action == 'modification/get' && r.success && r.data) {
        var m = r.data.modification || {};

        if (m.id) {
            $(document).find('.msoptionsprice-modification').removeClass('active');
            $(document).find('.msoptionsprice-modification.modification-'+m.id).addClass('active');
        }

    }
});

$(document).on('change', '.tm-modification-input', function (e) {
    $(this.form).trigger('change');
});


// процент скидки
$(document).on('msoptionsprice_product_action', function (e, action, form, r) {
    if (action == 'modification/get' && r.success && r.data) {
        var m = r.data.modification || {};
        if (m) {
            //console.log(m); // тут данные модификации

            var d = 0;
            if (m.old_cost > 0) {
                d =  parseInt(100 - (m.cost / m.old_cost * 100));
            }

            var $discount = $(msOptionsPrice.Product.prefix + m.rid + msOptionsPrice.Product.prefix + 'discount');
            if ($discount.length) {
                $discount.html(d);

                if (!d) {
                    $discount.parent().hide();
                }else {
                    $discount.parent().show();
                }
            }


        }
    }
});

/**
 * <div class="product-price-percent">- <span class="msoptionsprice-discount msoptionsprice-36">17</span> %</div>
 */