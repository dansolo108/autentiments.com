function calcRealTotalCost() {
    var real_total_cost = 0;
    $(miniShop2.Cart.cart + ' .basket__card').each(function(){
        var price = $(this).find('input[name=price]').val(),
            count = $(this).find('input[name=count]').val();
        real_total_cost = real_total_cost + (price * count);
    });
    $('.real_total_cost').text(miniShop2.Utils.formatPrice(real_total_cost));
}

function localDeliveryFields(delivery) {
    if (delivery == 1) {
        $('.hide_on_local_pickup').hide();
    } else {
        $('.hide_on_local_pickup').show();
    }
}

function pickupDeliveryPayment(delivery) {
    if (delivery == 1) {
        $('#pickup_payment_caption').show();
    } else {
        $('#pickup_payment_caption').hide();
    }
}

function chooseVisibleDelivery(delivery) {
    if ($('input[name="delivery"]:checked').parent().parent().is(':visible') === false) {
        $('.order__radio-pickup.delivery:visible').first().find('input[name="delivery"]').click();
    }
}

function managerCallingVisability(status) {
    if (status === true) {
        $('.without_manager_calling_wrapper').css('display', 'none');
    } else {
        $('.without_manager_calling_wrapper').show();
    }
}

function declension(n, titles) {
    return titles[(n % 10 === 1 && n % 100 !== 11) ? 0 : n % 10 >= 2 && n % 10 <= 4 && (n % 100 < 10 || n % 100 >= 20) ? 1 : 2];
}

function msmcGetPrice(price) {
    price = price.toString().replace(/\s+/g, '');
    
    let cource = parseFloat(msMultiCurrencyConfig.course),
        currencyId = msMultiCurrencyConfig.userCurrencyId,
        priceFloat = parseFloat(price);

    if (currencyId != 1) {
        price = priceFloat/cource;
        return Math.ceil(price);
    } else {
        return price;
    }
}

function setRates() {
    // $('input[name="order_rates"]').val($('input[name="delivery"]:checked').siblings('label').find('.delivery_rate').text());
    miniShop2.Order.add('order_rates', $('input[name="delivery"]:checked').siblings('label').find('.delivery_rate').text());
}

$(document).ready(function() {
    if (window.miniShop2) {
        $(document).on('mspc_set mspc_freshen', function(e, response) { // событие mspc_freshen добавлено в кастомном js-файле
            if(response.mspc.discount_amount > 0) {
                $('.mspc_discount_amount span').text(miniShop2.Utils.formatPrice(msmcGetPrice(response.mspc.discount_amount)));
            } else {
                $('.mspc_discount_amount span').text("-");
            }
        });
    
        $(document).on('mspc_remove', function(e, response) {
            if(response.mspc.discount_amount == 0) {
                $('.mspc_discount_amount span').text("-")
            }
        });
    
        miniShop2.Callbacks.add('Cart.change.response.success', 'fd_cart_change_ok', function(response) {
            $('.ms2_total_cost').text(miniShop2.Utils.formatPrice(msmcGetPrice(response.data.total_cost)));
            $('.mse2_total_declension').text(declension(response.data.total_count, stik_declension_product_js));
            calcRealTotalCost();
        });
    
        miniShop2.Callbacks.add('Cart.remove.response.success', 'fd_cart_remove_ok', function() {
            calcRealTotalCost();
        });
        
        miniShop2.Callbacks.add('Cart.add.response.success', 'fd_cart_add_ok', function() {
            showAjaxCart();
            // dataLayer.push({'event': 'add_cart'});
        });

        miniShop2.Callbacks.add('Order.add.response.success', 'stik', function(response) {
            // если было изменено поле страна/город/индекс и все эти поля не пустые
            if(
                !miniShop2.Utils.empty(response.data.country) ||
                !miniShop2.Utils.empty(response.data.city) ||
                !miniShop2.Utils.empty(response.data.index)
            ) {
                if ($('#country').val() && $('#city').val() && $('#index').val()) {
                    miniShop2.Order.getcost();
                }
            }
            
            if(!miniShop2.Utils.empty(response.data.delivery)) {
                // скрываем/показываем поля при самовывозе
                localDeliveryFields(response.data.delivery);
                // скрываем/показываем подпись у способа оплаты при самовывозе
                pickupDeliveryPayment(response.data.delivery);
            }
            
            chooseVisibleDelivery();
            console.log($('#country').val());
            console.log($('#city').val());
            console.log($('#index').val());
            if ($('#country').val() && $('#city').val() && $('#index').val()) {
                console.log('PASSED');
                $('.au-ordering').addClass('next-step');
            } else {
                console.log('NOT PASSED');
                $('.au-ordering').addClass('next-step');
            }
        });
        
        miniShop2.Callbacks.add('Order.getcost.before', 'fd_order_getcost_before', function() {
            // Перед расчетом стоимости, делаем блок с ценами, доставками, способами оплаты неактивным и показываем прелоадер
            $('.ajax-loader').addClass('enabled');
            $('.ajax-loader-block').addClass('loading');
        });
        
        miniShop2.Callbacks.add('Order.getcost.response.error', 'fd_orders_getcost_err', function(response) {
            // убираем прелоадер
            $('.ajax-loader').removeClass('enabled');
            $('.ajax-loader-block').removeClass('loading');
            // showAjaxCart('full');
        });
        
        miniShop2.Callbacks.add('Order.getcost.response.success', 'fd_orders_getcost_ok', function(response) {
            // убираем прелоадер
            $('.ajax-loader').removeClass('enabled');
            $('.ajax-loader-block').removeClass('loading');
            
            var countryLower = $('#country').val().toLowerCase();
            
            if (countryLower == 'россия') {
                $('.delivery-ru').show();
            } else {
                $('.delivery-ru').hide();
            }
            
            if ($.inArray(countryLower, ['беларусь', 'казахстан', 'украина']) !== -1) {
                $('.delivery.cdek_courier,.delivery.cdek_pvz').show();
                if (countryLower == 'украина') {
                    $('.delivery.cdek_pvz').hide();
                }
            }
            
            // Стоимость заказа, поскольку она находится за пределами #msOrder
            $('.ms2_order_cost').text(miniShop2.Utils.formatPrice(response.data.cost))
            
            // Общая стоимость доставки
            if(response.data.delivery_cost > 0) {
                $('.ms2_delivery_cost').text(miniShop2.Utils.formatPrice(response.data.delivery_cost) + " " + ms2_frontend_currency);
                // $('#city, #index').removeClass('error');
            } else {
                $('.ms2_delivery_cost').text(stik_order_delivery_not_calculated);
                // $('#city, #index').addClass('error');
            }
            
            // showAjaxCart('full');
            
            // скидка на доставку
            if(response.data.delivery_discount > 0) {
                $('.ms2_delivery_discount').text(miniShop2.Utils.formatPrice(response.data.delivery_discount) + " " + ms2_frontend_currency);
                $('.delivery_discount_wrapper').css('display', 'flex');
            } else {
                $('.ms2_delivery_discount').text("");
                $('.delivery_discount_wrapper').hide();
            }
            
            // показываем стоимость и сроки у каждого способа доставки
            if($(".delivery.cdek_courier").length) {
                if(response.data.courier > 0) {
                    $('.delivery.cdek_courier .delivery_cost').text(miniShop2.Utils.formatPrice(response.data.courier) + " " + ms2_frontend_currency);
                } else {
                    $('.delivery.cdek_courier .delivery_cost').text(stik_order_delivery_impossible_calculate);
                }
                $('.delivery.cdek_courier .delivery_rate').text(response.data.courier_rates);
            }
            
            if($(".delivery.cdek_pvz").length) {
                if(response.data.pvz > 0) {
                    $('.delivery.cdek_pvz .delivery_cost').text(miniShop2.Utils.formatPrice(response.data.pvz) + " " + ms2_frontend_currency);
                } else {
                    $('.delivery.cdek_pvz .delivery_cost').text(stik_order_delivery_impossible_calculate);
                }
                $('.delivery.cdek_pvz .delivery_rate').text(response.data.pvz_rates);
            }
            
            if($(".delivery.ems").length) {
                if(response.data.ems > 0) {
                    $('.delivery.ems .delivery_cost').text(miniShop2.Utils.formatPrice(response.data.ems) + " " + ms2_frontend_currency);
                } else {
                    $('.delivery.ems .delivery_cost').text(stik_order_delivery_impossible_calculate);
                }
                $('.delivery.ems .delivery_rate').text(response.data.ems_rates);
            }
            
            if($(".delivery.dhl").length) {
                if(response.data.dhl > 0) {
                    $('.delivery.dhl .delivery_cost').text(miniShop2.Utils.formatPrice(response.data.dhl) + " " + ms2_frontend_currency);
                } else {
                    $('.delivery.dhl .delivery_cost').text(stik_order_delivery_impossible_calculate);
                }
                $('.delivery.dhl .delivery_rate').text(response.data.dhl_rates);
            }
            
            if($(".delivery.post").length) {
                if(response.data.post > 0) {
                    $('.delivery.post .delivery_cost').text(miniShop2.Utils.formatPrice(response.data.post) + " " + ms2_frontend_currency);
                } else {
                    $('.delivery.post .delivery_cost').text(stik_order_delivery_impossible_calculate);
                }
                $('.delivery.post .delivery_rate').text(response.data.post_rates);
            }
            
            if($(".delivery.local_courier").length) {
                if(response.data.local_courier > 0) {
                    $('.delivery.local_courier .delivery_cost').text(miniShop2.Utils.formatPrice(response.data.local_courier) + " " + ms2_frontend_currency);
                } else {
                    $('.delivery.local_courier .delivery_cost').text(stik_order_delivery_impossible_calculate);
                }
            }
            
            if(response.data.hide_local_courier === true) {
                $('.delivery.local_courier').hide();
            }
            if(response.data.hide_local_pickup === true) {
                $('.delivery.local_pickup').hide();
            }
            
            chooseVisibleDelivery();
            // setRates();
        });
        
        miniShop2.Callbacks.add('Order.submit.response.success', 'fd_order_submit_ok', function(response) {
            const orderCost = parseFloat($('.ms2_order_cost').first().text().replace(" ", ""));
            // dataLayer.push({'event': 'order'});
            fbq('track', 'Purchase', { currency: "RUB", value: orderCost.toFixed(2) }, {eventID: response.data.msorder});
        });
        
        if ($('#msOrder').length) {
            // скрываем/показываем поля при самовывозе
            localDeliveryFields($('input[name="delivery"]:checked').val());
            // скрываем/показываем подпись у способа оплаты при самовывозе
            pickupDeliveryPayment($('input[name="delivery"]:checked').val());
        }
    }
    
    // изменение кол-ва в ajax-корзине
    $(document).on('change', '#ms2_cart_modal input[name=count]', function () {
        if (!!$(this).val()) {
            $(this).closest('.ms2_form').submit();
        }
    });
    
    $(document).on('click', '.au-cart__minus', function () {
        var $input = $(this).siblings('span').find('input[type="number"]');
        var count = parseInt($input.val()) - 1;
        count = count < 1 ? 1 : count;
        $input.val(count);
        $input.change();
        return false;
    });
    
    $(document).on('click', '.au-cart__plus', function () {
        var $input = $(this).siblings('span').find('input[type="number"]');
        if (parseInt($input.val()) < parseInt($input.attr('max'))) {
            $input.val(parseInt($input.val()) + 1);
            $input.change();
        } else {
            // miniShop2.Message.error(stik_basket_not_enough);
        }
        return false;
    });
    
    $('.header__link-basket').click(function(){
        showAjaxCart();
    });
    
    $('#order_submit').click(function(){
        $('button#submitbtn').click();
    });
    
    $('#have_discount_card').on('change', function () {
        managerCallingVisability(this.checked);
    });
    
    managerCallingVisability($('#have_discount_card').is(':checked'));
    
    $('.product-info__choise-size .popup-modal').click(function(){
        let size = $(this).text();
        $('#sub-modal input[name="size"]').val(size);
        $('#sub-modal .modal-size_subscribe__size').text(size);
    });
    
    // Предотвращаем показ предыдущей стоимости доставки СДЭК при неверно указанных данных
    $("#city").bind("paste keyup", function() {
        if ($('input[name="cdek_id"]').val() !== '') {
            miniShop2.Order.add('cdek_id', '');
        }
    });

    if (typeof msFavorites != 'undefined') {
        msFavorites.addMethodAction('success', 'name_action', function (r) {
            var self = this;
            if (self.data && self.data.method == 'add') {
                // dataLayer.push({'event': 'favorite'});
                // Facebook Conversions API
                // $.ajax({
                //     method: "POST",
                //     url: document.location.href,
                //     data: { fb_conversions: "favorites" }
                // });
            }
        });
    }
});

$('#msProduct input.au-product__color-input').on('change', function () {
    let id = $('#msProduct .ms2_form input[name=id]').val();
    $.post(window.location.href, {
        stikpr_action: 'sizes/get',
        language: $('html').attr('lang'),
        product_id: id,
        selected_color: $(this).val()
        
    }, function(data) {
        if (data) {
            $('#ajax_sizes').html(data);
        }
    })
});

$(document).on('af_complete', function(event, response) {
    if (response.success) {
        var form = response.form;
    
        switch (form.attr('id')) {
            case 'contact_form':
                // dataLayer.push({'event': 'message'});
                break;
            case 'newsletter_subscribe_form':
            case 'greeting_subscribe_form':
                $('.au-subscribe').addClass('subscribe_submit-end');
                // dataLayer.push({'event': 'email'});
                break;
            case 'size_subscribe_form':
                $('.au-close').trigger('click');
                $('.au-product__add-entrance').addClass('end').prop('disabled', true);
                break;
        }
    }
});

function validateEmail(email) {
    const re = /^(([^<>()[\]\.,;:\s@\"]+(\.[^<>()[\]\.,;:\s@\"]+)*)|(\".+\"))@(([^<>()[\]\.,;:\s@\"]+\.)+[^<>()[\]\.,;:\s@\"]{2,})$/i;
    return re.test(String(email).toLowerCase());
}
