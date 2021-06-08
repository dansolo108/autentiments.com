if (typeof msPromoCode === 'undefined') {
    msPromoCode = {}
}

(function (window, document, $, undefined) {
    msPromoCode.setup = function () {
        if (typeof mspc === 'undefined') {
            mspc = {};
        }
        if (typeof mspc.msg === "undefined") {
            mspc.msg = {};
        }
        if (typeof mspc.param === 'undefined') {
            mspc.param = {};
        }
        if (typeof mspc.param.sticky === 'undefined') {
            mspc.param.sticky = false;
        }
        if (typeof mspc.param.form === 'undefined') {
            mspc.param.form = 'mspc_form';
        }
        if (typeof mspc.param.discount_amount === 'undefined') {
            mspc.param.discount_amount = 'mspc_discount_amount';
        }
        if (typeof mspc.param.coupon_description === 'undefined') {
            mspc.param.coupon_description = 'mspc_coupon_description';
        }
        if (typeof mspc.param.field === 'undefined') {
            mspc.param.field = 'mspc_field';
        }
        if (typeof mspc.param.disfield === 'undefined') {
            mspc.param.disfield = 'mspc_field-disabled';
        }
        if (typeof mspc.param.btn === 'undefined') {
            mspc.param.btn = 'mspc_btn';
        }
        if (typeof mspc.param.msg === 'undefined') {
            mspc.param.msg = 'mspc_msg';
        }
        if (typeof mspc.param.price === 'undefined') {
            mspc.param.price = 'span.price';
        }
        if (typeof mspc.param.old_price === 'undefined') {
            mspc.param.old_price = 'span.old_price';
        }
        if (typeof mspc.param.refresh_old_price === 'undefined') {
            mspc.param.refresh_old_price = true;
        }
    };

    msPromoCode.initialize = function () {
        msPromoCode.setup();

        $(document).ready(function () {
            var types = [];

            // Если была передана ошибка
            if ('error' in mspc.msg && mspc.msg.error != '' && mspc.msg.error != null) {
                types = ['danger', 'error', 'error'];
            }
            // Если было передано предупреждение
            else if ('warning' in mspc.msg && mspc.msg.warning != '' && mspc.msg.warning != null) {
                types = ['warning', 'warning', 'info'];
            }
            // Если был передан успех
            else if ('success' in mspc.msg && mspc.msg.success != '' && mspc.msg.success != null) {
                types = ['success', 'success', 'success'];
            }

            // console.log('initialize', mspc);

            if (types.length) {
                // Манипуляции с формой
                $('.' + mspc.param.form)
                    .addClass('has-' + types[1]);

                // Манипуляции с сообщением
                $('.' + mspc.param.msg)
                    .addClass('text-' + types[0])
                    .html(mspc.msg[types[1]]);

                // Показываем jGrowl уведомление
                if (types[0] !== 'warning') {
                    // miniShop2.Message.show(mspc.msg[types[1]], {
                    //     theme: "ms2-message-" + types[2],
                    //     sticky: mspc.param.sticky,
                    // });
                }
            }

            msPromoCode.Cart.freshen({
                mspc: {
                    discount_amount: mspc.discount_amount || 0,
                    coupon_description: msPromoCode.Tools.htmlspecialcharsDecode(mspc.coupon_description) || '',
                }
            }, false);

            // При нажатии Enter на поле
            $('.mspc_btn, .mspc_field').on('keypress', function (e) {
                var key = e.which;
                if (key == 13) {
                    msPromoCode.handlerForm(e);
                }
            });

            // При клике по кнопке
            $('.' + mspc.param.btn).on('click', function (e) {
                msPromoCode.handlerForm(e);
            });
        });
    };

    /**
     *
     * @param e
     */
    msPromoCode.handlerForm = function (e) {
        var $form = $(e.target).closest('.' + mspc.param.form);
        var $field = $form.find('.' + mspc.param.field);

        if ($field.hasClass(mspc.param.disfield)) {
            msPromoCode.Coupon.remove($form);
        }
        else {
            msPromoCode.Coupon.set($form);
        }

        e.preventDefault();
    };

    msPromoCode.Coupon = {
        /**
         * Применяет купон
         *
         * @param form
         */
        set: function (form) {
            msPromoCode.Send.setCoupon('coupon/set', form);
        },

        /**
         * Удаляет купон
         *
         * @param form
         */
        remove: function (form) {
            msPromoCode.Send.removeCoupon('coupon/remove', form);
        },

        /**
         * Чекает купон на доступность
         *
         * @param code
         */
        check: function (code) {
            if (typeof(code) === 'undefined') {
                code = '';
            }
            msPromoCode.Send.checkCoupon('coupon/get', code);
        },
    };

    msPromoCode.Form = {
        /**
         * Чистим элементы формы ввода купона от классов, текста и т.п.
         *
         * @param clearValue
         */
        clean: function (clearValue) {
            clearValue = clearValue || true;

            // Чистим тег формы
            $('.' + mspc.param.form)
                .removeClass(function (index, cls) {
                    return cls.replace(mspc.param.form, '')
                });

            // Чистим тег сообщения
            $('.' + mspc.param.msg)
                .removeClass(function (index, cls) {
                    return cls.replace(mspc.param.msg, '')
                });

            // Чистим поле сообщения от текста
            if (clearValue) {
                $('.' + mspc.param.msg)
                    .html('');
            }
        }
    };

    msPromoCode.Cart = {
        /**
         *
         * @param resp
         * @param touch
         */
        freshen: function (resp, touch) {
            touch = touch || true;

            if (typeof(resp) !== 'undefined') {
                // Ставим общую скидку
                var discount_amount = 0;
                if ('mspc' in resp && 'discount_amount' in resp.mspc) {
                    discount_amount = resp.mspc.discount_amount;
                }
                var coupon_description = '';
                if ('mspc' in resp && 'coupon_description' in resp.mspc) {
                    coupon_description = resp.mspc.coupon_description;
                }
                msPromoCode.Cart.setDiscountAmount(discount_amount, coupon_description);

                // Ставим цены для товаров в корзине
                if ('ms2' in resp && 'cart' in resp.ms2) {
                    msPromoCode.Cart.setPrices(resp.ms2.cart);
                }
                $(document).trigger('mspc_freshen', resp);
            }
            else {
                msPromoCode.Send.freshenCart('cart/get');
            }
        },

        /**
         * @param cart
         */
        setPrices: function (cart) {
            if (typeof cart !== 'undefined') {
                for (var key in cart) {
                    // манипуляции с old_price
                    if (mspc.param.refresh_old_price) {
                        if (typeof cart[key].old_price !== 'undefined' && cart[key].old_price != 0 && cart[key].old_price != cart[key].price) {
                            // подставляем новый old_price в товар в корзине
                            $('#' + key)
                                .find(mspc.param.old_price)
                                .show()
                                .find('span')
                                .html(miniShop2.Utils.formatPrice(cart[key].old_price));
                        }
                        else {
                            // прячем тег с old_price
                            $('#' + key)
                                .find(mspc.param.old_price)
                                .hide();
                        }
                    }

                    // манипуляции с price
                    $('#' + key)
                        .find(mspc.param.price + ' span')
                        .html(miniShop2.Utils.formatPrice(cart[key].price));

                }
            }
        },

        /**
         * Устанавливает общую скидку в специальное поле
         *
         * @param discount
         * @param description
         */
        setDiscountAmount: function (discount, description) {
            if (typeof discount !== 'undefined') {
                var $discount = $('.' + mspc.param.discount_amount);
                var $description = $('.' + mspc.param.coupon_description);

                if (discount > 0) {
                    $discount.show();
                } else {
                    $discount.hide();
                }
                if (!!description) {
                    $description.show();
                } else {
                    $description.hide();
                }

                $discount.find('span')
                    .html(miniShop2.Utils.formatPrice(discount));
                $description
                    .html(description);
            }
        },
    };

    msPromoCode.Send = {
        /**
         *
         * @param action
         * @param touch
         */
        freshenCart: function (action, touch) {
            touch = touch || true;

            $.post(
                mspcConfig['webconnector'], {
                    mspc_action: action,
                    ctx: mspcConfig.ctx,
                },
                function (resp) {
                    // console.log('freshenCart', resp)

                    if (resp.success) {
                        if (touch) {
                            var types = [];

                            // Если была передана ошибка
                            if ('error' in resp.mspc && resp.mspc.error != '' && resp.mspc.error != null) {
                                types = ['danger', 'error', 'error'];

                                // если купон отвязали от корзины (закончился, действие юзера, ещё что-то...)
                                if (resp.mspc.coupon == '') {
                                    // location.reload();
                                    $form = $('.' + mspc.param.form);
                                    msPromoCode.Send.removeCoupon('coupon/remove', $($form[0]));
                                }
                            }
                            // Если было передано предупреждение
                            else if ('warning' in resp.mspc && resp.mspc.warning != '' && resp.mspc.warning != null) {
                                types = ['warning', 'warning', 'info'];
                            }
                            // Если был передан успех
                            else if ('success' in resp.mspc && resp.mspc.success != '' && resp.mspc.success != null) {
                                types = ['success', 'success', 'success'];
                            }

                            msPromoCode.Form.clean(false);

                            if (types.length) {
                                // Манипуляции с формой
                                $('.' + mspc.param.form)
                                    .addClass('has-' + types[1]);

                                // Манипуляции с сообщением
                                $('.' + mspc.param.msg)
                                    .addClass('text-' + types[0])
                                    .html(resp.mspc[types[1]]);

                                // Показываем jGrowl уведомление
                                if (types[0] != 'warning') {
                                    miniShop2.Message.show(resp.mspc[types[1]], {
                                        theme: "ms2-message-" + types[2],
                                        sticky: mspc.param.sticky,
                                    });
                                }
                            }
                        }

                        msPromoCode.Cart.freshen(resp);
                    }
                },
                'json'
            );
        },

        /**
         * Устанавливает промо-код на корзину
         *
         * @param action
         * @param form
         */
        setCoupon: function (action, form) {
            var $form = form;
            var $field = $form.find('.' + mspc.param.field);

            if ($form.length < 1 || $field.length < 1 || $field.val() == '') {
                return;
            }

            $.post(
                mspcConfig['webconnector'], {
                    mspc_action: action,
                    mspc_coupon: $field.val(),
                    ctx: mspcConfig.ctx,
                },
                function (resp) {
                    // console.log('setCoupon', resp)

                    var types = [];

                    msPromoCode.Form.clean();

                    if (resp['success']) {
                        msPromoCode.Cart.freshen(resp);
                        miniShop2.Cart.status(resp.ms2.status);

                        // Манипуляции с полем ввода
                        $('.' + mspc.param.field)
                            .val(resp.mspc.coupon)
                            .addClass(mspc.param.disfield)
                            .attr('disabled', true);

                        // Если было передано предупреждение
                        if ('warning' in resp.mspc && resp.mspc.warning != '' && resp.mspc.warning != null) {
                            types = ['warning', 'warning', 'info'];
                        }
                        // Если был передан успех
                        else if ('success' in resp.mspc && resp.mspc.success != '' && resp.mspc.success != null) {
                            types = ['success', 'success', 'success'];
                        }
                    }
                    else {
                        types = ['danger', 'error', 'error'];
                    }

                    if (types.length) {
                        // Манипуляции с формой
                        $form.addClass('has-' + types[1]);

                        // Манипуляции с сообщением
                        $form.find('.' + mspc.param.msg)
                            .addClass('text-' + types[0])
                            .html(resp.mspc[types[1]]);

                        // Манипуляции с кнопкой
                        $('.' + mspc.param.btn)
                            .html(resp.mspc.btn);

                        // Показываем jGrowl уведомление
                        if (types[0] != 'warning') {
                            miniShop2.Message.show(resp.mspc[types[1]], {
                                theme: "ms2-message-" + types[2],
                                sticky: mspc.param.sticky,
                            });
                        }
                    }

                    $(document).trigger('mspc_set', resp);
                },
                'json'
            );
        },

        /**
         * Удаляет промо-код с корзины
         *
         * @param action
         * @param form
         */
        removeCoupon: function (action, form) {
            var $form = form;
            var $field = $form.find('.' + mspc.param.field);

            if ($form.length < 1 || $field.length < 1 || $field.val() == '') {
                return;
            }

            $.post(
                mspcConfig['webconnector'], {
                    mspc_action: action,
                    mspc_coupon: $field.val(),
                    ctx: mspcConfig.ctx,
                },
                function (resp) {
                    // console.log('removeCoupon', resp)

                    var types = ['danger', 'success', 'error'];

                    msPromoCode.Form.clean();
                    msPromoCode.Cart.freshen(resp);
                    miniShop2.Cart.status(resp.ms2.status);

                    // Манипуляции с полем ввода
                    $('.' + mspc.param.field)
                        .val('')
                        .removeClass(mspc.param.disfield)
                        .attr('disabled', false);

                    // Манипуляции с сообщением
                    $form.find('.' + mspc.param.msg)
                        .addClass('text-' + types[0])
                        .html(resp.mspc[types[1]]);

                    // Манипуляции с кнопкой
                    $('.' + mspc.param.btn)
                        .html(resp.mspc.btn);

                    // Показываем jGrowl уведомление
                    miniShop2.Message.show(resp.mspc[types[1]], {
                        theme: "ms2-message-" + types[2],
                        sticky: mspc.param.sticky,
                    });

                    $(document).trigger('mspc_remove', resp);
                },
                'json'
            );
        },

        /**
         * Проверяет, доступен ли купон для применения. Если нет - удаляет его с корзины.
         *
         * @param action
         * @param code
         */
        checkCoupon: function (action, code) {
            $.post(
                mspcConfig['webconnector'], {
                    mspc_action: action,
                    mspc_coupon: code,
                    ctx: mspcConfig.ctx,
                },
                function (resp) {
                    if (typeof resp != 'undefined' && 'mspc' in resp && 'coupon' in resp.mspc) {
                        if (resp['mspc']['coupon'] == null) {
                            $form = $('.' + mspc.param.form);
                            msPromoCode.Send.removeCoupon('coupon/remove', $($form[0]));
                        }
                    }
                },
                'json'
            );
        },
    };

    msPromoCode.Tools = {
        htmlspecialcharsDecode: function (string, quoteStyle) {
            var optTemp = 0
            var i = 0
            var noquotes = false

            if (typeof quoteStyle === 'undefined') {
                quoteStyle = 2
            }
            string = string.toString()
                .replace(/&lt;/g, '<')
                .replace(/&gt;/g, '>')
            var OPTS = {
                'ENT_NOQUOTES': 0,
                'ENT_HTML_QUOTE_SINGLE': 1,
                'ENT_HTML_QUOTE_DOUBLE': 2,
                'ENT_COMPAT': 2,
                'ENT_QUOTES': 3,
                'ENT_IGNORE': 4
            }
            if (quoteStyle === 0) {
                noquotes = true
            }
            if (typeof quoteStyle !== 'number') {
                // Allow for a single string or an array of string flags
                quoteStyle = [].concat(quoteStyle)
                for (i = 0; i < quoteStyle.length; i++) {
                    // Resolve string input to bitwise e.g. 'PATHINFO_EXTENSION' becomes 4
                    if (OPTS[quoteStyle[i]] === 0) {
                        noquotes = true
                    } else if (OPTS[quoteStyle[i]]) {
                        optTemp = optTemp | OPTS[quoteStyle[i]]
                    }
                }
                quoteStyle = optTemp
            }
            if (quoteStyle & OPTS.ENT_HTML_QUOTE_SINGLE) {
                // PHP doesn't currently escape if more than one 0, but it should:
                string = string.replace(/&#0*39;/g, "'")
                // This would also be useful here, but not a part of PHP:
                // string = string.replace(/&apos;|&#x0*27;/g, "'");
            }
            if (!noquotes) {
                string = string.replace(/&quot;/g, '"')
            }
            // Put this in last place to avoid escape being double-decoded
            string = string.replace(/&amp;/g, '&')

            return string
        },
    };

    msPromoCode.initialize();
})
(this, document, jQuery);