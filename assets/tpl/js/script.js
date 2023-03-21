function resizeWindowHeight() {
    let vh = window.innerHeight * 0.01;
    document.documentElement.style.setProperty('--vh', `${vh}px`);

    window.addEventListener('resize', () => {
        let vh = window.innerHeight * 0.01;
        document.documentElement.style.setProperty('--vh', `${vh}px`);
    });
}


// data-val для input
function setDataValInput() {
    $(document).on('input', '.custom-form__input', function () {
        this.setAttribute('data-val', this.value);
    });
}


// textarea
function resizeTextarea() {
    $('textarea').on('input', function () {
        let height = $(this).outerHeight();
        if (this.scrollHeight > height) {
            $(this).removeAttr("rows");
            $(this).removeAttr("cols");

            this.style.height = `${this.scrollHeight}px`;
        }
    });
}


// скрыть/показать пароль
function toggleShowPassword() {

    $('.au-login__password-hide-btn').click(function () {
        const input = $(this).siblings('.custom-form__input');
        $(this).toggleClass('show');

        if ($(this).hasClass('show')) {
            input.attr('type', 'text');
        } else {
            input.attr('type', 'password');
        }
    });
}


// фиксация шапки при скролле вверх
function fixedHeaderTop() {
    let header = $('.au-header');
    let scrollPrev = 0;
    let height = header.outerHeight();

    $(window).scroll(function () {
        let scrolled = $(window).scrollTop();

        if (scrolled > height && scrolled > scrollPrev) {
            header.addClass('out');
        } else {
            header.removeClass('out');
        }
        scrollPrev = scrolled;
    });
}


// модалка с выбором языка/валюты на мобиле
function toggleLangBlock() {
    if ($(window).width() < 768) {
        $('.au-header__btn-lang').click(function () {
            $('.modal').removeClass('active');
            $('.au-filter__close').removeClass('active');
            $('.au-header__lang-box').toggleClass('active');
        });

        $(document).scroll(function () {
            $('.au-header__lang-box').removeClass('active');
        });
    }
}


// поиск
function toggleModalSearsh() {
    $('.btn_search_open').click(function () {
        if ($(window).width() >= 1024) {
            $('.modal').removeClass('active');
            openModalАdditionally($('.au-modal-overlay'));
            $('.au-header__wrapper').addClass('z-index');

        } else {
            $('.au-header__sub-box').toggleClass('top');
        }
        $('.au-search').toggleClass('active');
    });
}


// открытие/закрытие бургера
function toggleBurger() {
    $('.au-btn-burger_open').click(function () {
        $('.au-burger').addClass('active');
        $('.au-header__lang-box').removeClass('active');
        $('body').addClass('no-scroll');
        $('.modal').removeClass('active');
        $('.au-filter__close').removeClass('active');

        if ($('.au-category__category').length || $('.au-product').length) {
            $('.au-burger.active').addClass('visible');
            $('.sub-catalog').addClass('active');
        }
    });

    $('.au-btn-burger_close').click(function () {
        $('.au-burger').removeClass('active visible');
        $('.au-header__sub-box').removeClass('active top');
        $('body').removeClass('no-scroll');
    });
}


// открытие/закрытие sub-menu
function toggleSubMenu() {
    function addColumns(item) {
        if (item.find($('.au-header__sub-item')).length > 18) {
            item.find($('.au-header__sub-list')).addClass('columns-three');
        } else if (item.find($('.au-header__sub-item')).length > 9) {
            item.find($('.au-header__sub-list')).addClass('columns-two');
        }
    }

    $('.au-header__sub-open').click(function () {
        $('.au-burger.active').addClass('visible');
        $('.au-header__sub-box').removeClass('active');
        $(this).find($('.au-header__sub-box')).addClass('active');

        if ($(window).width() >= 1024) {
            $('.au-header__sub-open').removeClass('active');
            $(this).find($('.au-header__link')).addClass('active');
            closeForModal();
            addColumns($(this));
        }

        $('.au-header__sub-wrapper').scroll(function () {
            if (this.scrollHeight - this.scrollTop === this.clientHeight && $(window).width() < 1024) {
                $(this).removeClass('sub-wrapper__bottom-arrow');
            } else {
                $(this).addClass('sub-wrapper__bottom-arrow');
            }
        });
    });

    $('.au-header__sub-close').click(function () {
        setTimeout(function () {
            $('.au-header__sub-box').removeClass('active');
        }, 0);
        setTimeout(function () {
            $('.au-burger.active').removeClass('visible');
        }, 250);
        if ($(window).width() >= 1024) {
            $('.au-header__link').removeClass('active');
        }
    });

    if ($(window).width() >= 1024) {
        $('.au-header__sub-open').hover(function () {
            if (!$('.modal').hasClass('au-modal-text-page')) {
                closeForModal();
            }
            $(this).find($('.au-header__sub-box')).toggleClass('active');
            $(this).find($('.au-header__link')).toggleClass('active');
            addColumns($(this));
        });

        $(document).scroll(function () {
            $('.au-header__sub-box').removeClass('active');
            $('.au-header__link').removeClass('active');
        });
    }
}


// модалка фильтры
function toggleModalFilter() {
    if ($('.filter-btn-open').length) {
        $('.filter-btn-open').click(function () {
            let top = $(this).offset().top + $(this).outerHeight();

            $('.modal').removeClass('active');
            $('.au-modal-filter').addClass('active');
            openModalАdditionally();
            $('.au-header__lang-box').removeClass('active');

            if ($(window).width() < 1024) {
                $('.au-modal-filter').offset({top: top});
                $('.au-filter__close').addClass('active');
            }
        });

        if ($(window).width() < 1024) {
            $('.au-filter__close').click(function () {
                $(this).removeClass('active');
            });
        }
    }
}


// Стоимость input range
function startUpRangeUiCost() {
    $('#filter-range').slider({
        range: true,
        animate: "slow",
        classes: {
            "ui-slider": "au-filter__slider",
            "ui-slider-handle": "au-filter__handle",
            "ui-slider-range": "au-filter__range"
        },
        min: 500,
        max: 200000,
        values: [500, 200000],
        step: 500,
        slide: function (event, ui) {
            $('#min_cost').val(ui.values[0]);
            $('#max_cost').val(ui.values[1]);
        }
    });
    $('#min_cost').val(500);
    $('#max_cost').val(200000);
}


// модалка info-size
function toggleModalInfoSize() {
    if ($('.au-product__info-size').length) {
        $('.au-product__info-size').click(function () {

            $('.modal').removeClass('active');
            $('.au-modal-size').addClass('active');
            openModalАdditionally($('.au-modal-overlay'));
            $('.au-header__lang-box').removeClass('active');
        });
    }
}

// модалка info-remains
function toggleModalInfoRemains() {
    if ($('.au-product__info-remains').length) {
        $('.au-product__info-remains').click(function () {

            $('.modal').removeClass('active');
            $('.au-modal-remains').addClass('active');
            openModalАdditionally($('.au-modal-overlay'));
            $('.au-header__lang-box').removeClass('active');
        });
    }
}


// модалка авторизация/регистрация
function openModalLogin() {
    $('.btn_login_open').click(function () {
        $('.au-burger').removeClass('active');
        $('.modal').removeClass('active');
        $('.au-modal-login').addClass('active');
        openModalАdditionally();
        $('.au-header__lang-box').removeClass('active');
        $('.au-filter__close').removeClass('active');

        if ($(window).width() >= 768) {
            $('.au-modal-overlay').addClass('active');
        } else {
            $('.au-login__header-close').fadeIn();
            if ($('.au-ordering__link-back').length) {
                $('.au-ordering__link-back').addClass('au-hidden');
            } else {
                $('.au-header__burger').addClass('au-hidden');
            }
        }
    });

    $('.au-tab-title[href="register"]').click(function () {
        $('.au-login__img_register').fadeIn();
    });

    $('.au-tab-title[href="login"]').click(function () {
        $('.au-login__img_register').fadeOut();
    });
}


// модалка корзина
function showAjaxCart() {
    if ($('#ms2_cart_modal').length) {
        $.post("/assets/components/autentiments/getAjaxCart.php", {language: $('html').attr('lang')}, function (data) {
            if (data) {
                $('#ms2_cart_modal').html(data);
                $('.modal').removeClass('active');
                $('.au-modal-cart').addClass('active');
                openModalАdditionally($('.au-modal-overlay'));
                $('.au-header__lang-box').removeClass('active');
                $('.au-filter__close').removeClass('active');
            }
        })
    }
}

function openModalCart() {

    $('.au-cart-open').click(function () {
        showAjaxCart();
    });
}


// кнопки плюс/минус (колличество товара)
// function changeCountCards() {
//     $('.au-cart__minus').click(function (e) {
//         e.preventDefault();
//         const inputCount = $(this).parent().find('input');
//         let count = parseInt(inputCount.val()) - 1;
//         count = count < 1 ? 1 : count;
//         if (count === 1) {
//             $(this).prop("disabled", true);
//         }
//         inputCount.val(count);
//         inputCount.change();
//     });
// }


// ширина scrollbar
function widthScroll() {
    let div = document.createElement('div');
    let body = document.querySelector('body')
    div.style.overflowY = 'scroll';
    div.style.width = '50px';
    div.style.height = '50px';
    div.style.visibility = 'hidden';
    body.appendChild(div);
    let scrollWidth = div.offsetWidth - div.clientWidth;
    body.removeChild(div);
    return scrollWidth;
}


// ширина scrollbar, overlay, no-scroll при открытии модалок
function openModalАdditionally(overlay) {
    let paddingRightHeader = parseInt($('.au-header').css("padding-right"));
    $('body').addClass('no-scroll');
    $('body').css("padding-right", `${widthScroll()}px`);
    $('.au-header').css("padding-right", `${paddingRightHeader + widthScroll()}px`);
    $('.top-banner__wrapper').css("padding-right", `${parseInt($('.top-banner__wrapper').css("padding-right")) + widthScroll()}px`);
    if (overlay) {
        $('.au-modal-overlay').addClass('active');
    }
}


// функция для закрытия модалок
function closeForModal() {
    $('body').removeClass('no-scroll');
    $('body').css("padding-right", "");
    $('.au-header').css("padding-right", "");
    $('.top-banner__wrapper').css("padding-right", "");
    $('.au-modal-overlay').removeClass('active');
    $('.modal').removeClass('active');
    $('.au-header').removeClass('z-index');
    $('.au-header__wrapper').removeClass('z-index');
    $('.au-login__img_register').fadeOut();

    $('.au-login__tab').removeClass('active');
    $('.au-login__tab .au-tab-title').removeClass('active');
    $('.au-login__tab[data-tab="login"]').addClass('active');
    $('.au-login__tab .au-tab-title[href="login"]').addClass('active');

    if ($('.au-header__burger').hasClass('au-hidden')) {
        $('.au-login__header-close').fadeOut(200);
        setTimeout(function () {
            $('.au-header__burger').removeClass('au-hidden');
        }, 200);
    }

    if ($('.au-ordering__link-back').hasClass('au-hidden')) {
        $('.au-login__header-close').fadeOut(200);
        setTimeout(function () {
            $('.au-ordering__link-back').removeClass('au-hidden');
        }, 200);
    }
}


// закрытие модалок
function closeModal() {
    $(document).on('click', '.au-close, .au-modal-overlay.active, .au-modal.active', function (e) {
        if (!e.target.closest('.au-modal__content')) {
            closeForModal();
            $(window).scroll();
        }
        if ($('.au-lookbook__gallery').length) {
            $('.au-lookbooks__gallery-img').attr('src', '');
            $('.au-lookbook__gallery').removeClass('active');
            closeForModal();
        }
    });
}

document.addEventListener('click', function (e) {
    if (e.target.closest('.au-cart__resume')) {
        closeForModal();
    }
})

// theiaStickySidebar
function addStickySidebar() {
    $('.sidebar').theiaStickySidebar({
        additionalMarginTop: 140,
        additionalMarginBottom: -30,
        updateSidebarHeight: false
    });
}


// добавить в избранное
// function addFavoriteCard() {
//     $('.msfavorites').click(function(e) {
//         e.preventDefault();
//         $(this).toggleClass('voted');
//     });
// }


// выберите размер и добавить в корзину
function addProductCart() {
    const btnBasket = $('.au-product__add-basket');
    const btnSize = $('.au-product__add-size');
    const size = $('.au-product__size');
    const notSize = $('.not-size');
    if (btnSize.length > 0) {
        if ($('.au-product__size-input').is(':checked')) {
            btnBasket.css('visibility', 'visible');
            btnBasket.css('opacity', '1');
            $('.au-product__add-size').css('visibility', 'hidden');
            $('.au-product__add-size').css('opacity', '0');
        }

        btnBasket.mouseenter(function () {
            $(this).removeClass('active');
            btnSize.addClass('active');
        });
        btnSize.mouseleave(function () {
            $(this).removeClass('active');
            btnBasket.addClass('active');
        });
        $(document).on('click', '.au-product__size', function () {
            btnBasket.css('visibility', 'visible');
            btnBasket.css('opacity', '1');
            $('.au-product__add-size').css('visibility', 'hidden');
            $('.au-product__add-size').css('opacity', '0');
            $('.not-size').removeClass('active');
            $('.au-product__add-entrance').removeClass('active');
            $('.au-product__add-entrance').removeClass('end').prop('disabled', false);
        });

        $(document).on('click', '.not-size', function () {
            $(this).addClass('active');
            $('.au-product__add-entrance').addClass('active');
            $('.au-product__size-input').prop('checked', false);
            $('.au-product__add-entrance').removeClass('end').prop('disabled', false);
        });

        $(document).on('click', '.au-product__add-entrance', function () {
            let sizeLabel = $('.au-product__size.not-size.active');
            let size = sizeLabel.html();
            let sizeInput = $(`.au-product__size-input[id="${sizeLabel.attr('for')}"]`);
            $('#size_subscribe_form input[name="modification_id"]').val(sizeInput.val());
            $('#size_subscribe_form .selected-size_js').text(size);
            let colorLabel = $('.au-product__color.active');
            let color = colorLabel.attr('title');
            $('#size_subscribe_form .selected-color_js').text(color);

            $('.modal').removeClass('active');
            $('.au-modal-entrance').addClass('active');
            openModalАdditionally($('.au-modal-overlay'));
            $('.au-header__lang-box').removeClass('active');
        });
    }
}

document.querySelector('#size_subscribe_form')?.addEventListener('submit', e => {
    e.preventDefault();
    e.stopPropagation();

    $.ajax({
        url: '/assets/components/autentiments/subscribeModification.php',
        method: "POST",
        data: {
            phone: e.target.elements["phone"].value,
            modification_id: e.target.elements["modification_id"].value
        },
        dataType: "JSON",
        success:function (data){
            if (!data)
                return;
            if(data.success){
                $.jGrowl(data.message,{theme:"ms2-message-success"});
                $('.modal').removeClass('active');
                closeForModal();
            }
            else{
                $.jGrowl(data.message,{theme:"ms2-message-error"});
            }
        }
    });
})

// табы
function toggleTabs() {
    $('.au-tab-title').click(function (e) {
        e.preventDefault();
        const id = $(this).attr('href');
        const content = $('.au-tab-content[data-tab="' + id + '"]');

        $('.au-tab-title').removeClass('active');
        $('.au-tab-content').removeClass('active');
        $(this).addClass('active');
        content.addClass('active');
    });
}


// accordeons
function toggleAccordeons() {
    $('.au-accordeon-title').click(function () {
        $('.au-accordeon-title').not($(this)).removeClass('open');
        $('.au-accordeon-content').not($(this).next()).slideUp(300);
        $(this).toggleClass('open');
        $(this).next().slideToggle(300);
    });
}


// табы в модалке авторизации
function toggleLoginTabs() {
    $('.au-tab-login-title').click(function (e) {
        e.preventDefault();
        const id = $(this).attr('href');
        const content = $('.au-tab-login-content[data-tab="' + id + '"]');

        $('.au-tab-login-title').removeClass('active');
        $('.au-tab-login-content').removeClass('active');
        $(this).addClass('active');
        content.addClass('active');
    });
}


function showRegisterPhone() {
    $('.loyalty-check_js').on('change', function () {
        if ($(this).is(':checked')) {
            $('.custom-form__register_phone').fadeIn();
        } else {
            $('.custom-form__register_phone').hide();
        }
    });
}


function openBonusRules() {
    $('.bonus-rules-open').click(function () {
        openModalАdditionally($('.au-modal-overlay'));
        $('.au-modal-bonus-rules').addClass('active');
    });
}


function hideLoyaltyMobile() {
    $('.au-profile__tab').click(function () {
        let href = $(this).attr('href');

        if (href === 'purchases' && $(window).width() < 1024) {
            $('.au-profile__loyalty-box').hide();
        } else if ($(window).width() < 1024) {
            $('.au-profile__loyalty-box').show();
        }
    });
}


// модалка смены пароля
function toggleChangePassword() {
    $('.au-change-password__btn-open').click(function () {
        $('.modal').removeClass('active');
        openModalАdditionally($('.au-modal-overlay'));
        $('.au-modal-change-password').addClass('active');
    });

    $('.au-change-password__btn').click(function () {
        $('.au-close').trigger('click');
    });
}


// текстовые страницы-модалки
function toggleTextModal() {
    $('.au-text-tab_js').click(function (e) {
        e.preventDefault();
        $this = this;

        $.ajax("/info").done(function (ajaxData) {
            if (ajaxData) {
                $('.au-modal-text-page').html(ajaxData);

                const id = $($this).attr('href').replace("/", "");
                const content = $('.au-tab-text-content[data-tab="' + id + '"]');
                const tab = $('.au-text-tab_js[href="' + id + '"]');

                $('.au-login__header-close').fadeOut(200);
                setTimeout(function () {
                    $('.au-header__burger').removeClass('au-hidden');
                    $('.au-ordering__link-back').removeClass('au-hidden');
                }, 200);
                $('.modal').removeClass('active');
                $('.au-modal-overlay').removeClass('active');
                $('.au-header').removeClass('out');
                setTimeout(function () {
                    $('.au-header__sub-box').removeClass('active top');
                }, 0);
                $('.au-burger').removeClass('active visible');

                if (!$('.au-modal-text-page').hasClass('active')) {
                    openModalАdditionally();
                    $('.au-modal-text-page').addClass('active');
                }

                $('.au-text-tab_js').removeClass('active');
                $('.au-tab-text-content').removeClass('active');
                $($this).addClass('active');
                content.addClass('active');
                tab.addClass('active');

                $('#text_tab_active').val($($this).data('text'));
                $(window).off('scroll');
            }
        })
    });

    $(document).on('click', '#text_tab_active', function () {
        $(this).toggleClass('open');
        $('.au-text-page__nav').toggleClass('active');
    });

    // Табы в модалке
    $(document).on('click', '.au-modal-text-page .au-text-tab_js', function (e) {
        e.preventDefault();

        const id = $(this).attr('href');
        const content = $('.au-tab-text-content[data-tab="' + id + '"]');
        const tab = $('.au-text-tab_js[href="' + id + '"]');

        $('.au-text-tab_js').removeClass('active');
        $('.au-tab-text-content').removeClass('active');
        $(this).addClass('active');
        content.addClass('active');
        tab.addClass('active');

        $('#text_tab_active').val($(this).data('text')).removeClass('open');
        $('.au-text-page__nav').removeClass('active');
    });
}


// просмотр картинки лукбука на мобиле
function openLookbookImg() {
    if ($(window).width() < 1025) {
        $('.au-lookbook__card img').on('click', function () {
            let src = $(this).attr('src');
            $('.au-lookbook__gallery').addClass('active');
            $('.au-lookbooks__gallery-img').attr('src', src);
            openModalАdditionally($('.au-modal-overlay'));

        });
    }
}


// программа лояльности в ЛК
function countAmountForLevel() {
    if ($('.au-profile').length) {
        const AMOUNT_TOTAL = 100000;
        const LEVEL_ONE = 50000;
        const LEVEL_TWO = 70000;
        let amount = parseInt($('#amount').text());
        let x = amount / AMOUNT_TOTAL * 100;
        let amountLevel = $('#amount_level');
        let currentLevel = $('#current_level');

        // $('.au-profile__loyalty-slider').css('width', `${x}%`);

        // if (amount < LEVEL_ONE) {
        //     currentLevel.text('Базовай');
        //     amountLevel.text(LEVEL_ONE - amount);
        // } else if (amount >= LEVEL_ONE && amount < LEVEL_TWO) {
        //     currentLevel.text('Бронзовый');
        //     amountLevel.text(LEVEL_TWO - amount);
        // } else if (amount >= LEVEL_TWO && amount <= AMOUNT_TOTAL) {
        //     currentLevel.text('Серебряный');
        //     amountLevel.text(AMOUNT_TOTAL - amount + 1);
        // } else if (amount > AMOUNT_TOTAL) {
        //     currentLevel.text('Золотой');
        //     $('.au-profile__amount_level').hide();
        // }
    }
}


$(document).ready(function () {

    if ($('.au-header-cart').length) {
        $('body').addClass('au-ordering-home');
    }

    $('input[type=number]').on('input', function () {
        this.value = this.value.replace(/[^\d]/g, '');
    });

    $('input[type=tel]').on('input', function () {
        this.value = this.value.replace(/[^\d\+\-\(\)\s]/g, '');
    });

    $('#date').on('input', function () {
        this.value = this.value.replace(/[^\d\.]/g, '');
    });


    $('.au-profile__form input').on("input", function () {
        $('.au-profile__submit').prop('disabled', false);
    });


    $('.au-promo-code__cancel').click(function (e) {
        e.preventDefault();
        $('.mspc_btn').click();
    });

    $('.au-promo-code__input').on('input', function () {
        $('.au-promo-code__submit').addClass('active');
    });


    $('.au-bonuses__input').on('input', function () {
        $('.au-bonuses__submit').addClass('active');
    });

    // disabled contacts submit
    $('.au-contacts__col-form .custom-form__input').on('input', function () {
        if ($('#name').val().length > 0 && $('#email').val().length > 0 && $('#message').val().length > 0) {
            $('.au-contacts__submit').prop('disabled', false);
        }
    });

    // test
    $('.au-profile__loyalty-btn').click((e) => {
        if ($('input[name="mobilephone"]').val() == '') {
            e.preventDefault();
            addPhoneLoyalty();
        }
    });

});


// function validate() {
//     let input = $('.au-ordering__form input.custom-form__input');
//     let inputvalue = input.filter(function (n) {
//         return this.value.length > 0;
//     });

//     if (inputvalue.length == input.length) {
//         $('.au-ordering__submit').prop('disabled', false);
//         $('.au-ordering__politics').show();
//     } else {
//         $('.au-ordering__submit').prop('disabled', true);
//         $('.au-ordering__politics').hide();
//     }
// }

// disabled ordering submit
// $(document).ready(function() {
//     if ($(window).width() >= 1024) {
//         $('.au-ordering__submit').prop('disabled', true);
//         $('.au-ordering__form input.custom-form__input').on('input', validate);
//     }
// });


// cookie
$(function () {
    function showCookieModal() {
        $(".au-modal-cookie").fadeIn();
    }

    if (!$.cookie("hideCookieModal")) {
        setTimeout(showCookieModal, 3000);
    }

    $(".au-modal-cookie__close").click(function () {
        $(".au-modal-cookie").fadeOut();
        $.cookie("hideCookieModal", true, {
            expires: 365,
            path: "/"
        });
    });
});


// modal-welcome
// $(function() {
//     function showWelcomeModal() {
//         $(".au-modal-welcome").fadeIn();
//         openModalАdditionally();
//     }

//     if (!$.cookie("hideCookieModal")) {
//         setTimeout(showWelcomeModal, 1000);
//     }

//     $(".au-welcome-close").click(function() {
//         $(".au-modal-welcome").fadeOut();
//         closeForModal();
//         $.cookie("hideCookieModal", true, {
//             expires: 365,
//             path: "/"
//         });
//     });
// });


document.addEventListener("DOMContentLoaded", function () {
    if ('#purchases' === window.location.hash) {
        $('.au-profile__tab').removeClass('active');
        $('.au-profile__tab-content').removeClass('active');
        $('.au-profile__tab[href="purchases"]').addClass('active');
        $('.au-profile__tab-content[data-tab="purchases"]').addClass('active');

        if ($(window).width() < 1024) {
            $('.au-profile__loyalty-box').hide();
        }
    }
});


function addPhoneLoyalty() {
    $('.loyalty-text_no-tel').fadeIn();
    $('.loyalty-text_join').hide();
    $('.au-profile__loyalty-btn').hide();
    $('.custom-form__group_tel').addClass('group-no-phone');
    $('.custom-form__group_tel input').focus();
}


$(document).ready(function () {
    resizeWindowHeight();
    setDataValInput();
    resizeTextarea();
    toggleShowPassword();
    fixedHeaderTop();
    toggleLangBlock();
    toggleTextModal();
    toggleModalSearsh();
    toggleBurger();
    toggleSubMenu();
    toggleModalFilter();
    toggleModalInfoSize();
    toggleModalInfoRemains();
    // startUpRangeUiCost();
    openModalLogin();
    openModalCart();
    showRegisterPhone();
    // changeCountCards();
    openBonusRules();
    hideLoyaltyMobile();
    toggleChangePassword();
    closeModal();
    addStickySidebar();
    // addFavoriteCard();
    addProductCart();
    toggleTabs();
    toggleAccordeons();
    toggleLoginTabs();
    openLookbookImg();
    // countAmountForLevel();
});

/* International Telephone Input */

$("input[type=tel]").each(function (index) {
    let telInput = $(this),
        errorMsg = $(".int-tel-error"),
        errorMap = ["Неправильный номер", "Неверный код страны", "Слишком короткий", "Слишком длинный", "Неправильный номер"];

    // initialise plugin
    let iti = telInput.intlTelInput({
        nationalMode: false,
        formatOnDisplay: true,
        autoHideDialCode: false,
        initialCountry: "ru",
        preferredCountries: ["ru", "by", "kz", "az", "uz", "am", "ge", "kg"],
        geoIpLookup: function (callback) {
            $.get('//ipinfo.io', function () {
            }, "jsonp").always(function (resp) {
                let countryCode = (resp && resp.country) ? resp.country : "";
                callback(countryCode);
            });
        },
        utilsScript: "/assets/tpl/js/vendor/intl-tel-input/utils.js"
    });

    let reset = function () {
        telInput.removeClass("error");
        errorMsg.html('');
    };

    // on blur: validate
    telInput.on('blur keyup change', function () {
        if ($.trim(telInput.val())) {
            if (telInput.intlTelInput("isValidNumber")) {
                $('.js_sms_code_send').prop('disabled', false);
                reset();
            } else {
                $('.js_sms_code_send').prop('disabled', true);
                telInput.addClass("error");
                let errorCode = telInput.intlTelInput("getValidationError");
                errorMsg.html(errorMap[errorCode]);
            }
        } else {
            reset();
        }
    });

    // on keyup / change flag: reset
    // telInput.on("keyup change", reset);
});
if ($('.coupon-form').length) {
    $('.coupon-form button').click(function (e) {
        var values = $('.coupon-form').serializeArray();
        values['action'] = 'activateCoupon';
        $.ajax({
            url: "",
            method: "POST",
            data: {action: 'activateCoupon', values: values},
            dataType: "JSON",
            success: function (response) {
                if (response.success) {
                    if ($('.au-bonuses__count span').length) {
                        $.jGrowl(response.msg, {theme: 'af-message-success'});
                        $('.au-bonuses__count span').html(miniShop2.Utils.formatPrice(response.balance));
                    }
                    console.log(response);
                } else {
                    $.jGrowl(response.msg, {theme: 'af-message-error'});
                }
            }
        })
        return false;
    });
}

document.addEventListener('miniShop2Initialize',e=>{
    window.miniShop2.addCallback("Cart.add.response.success","events",(response)=>{
        setEvent("add_to_cart",PageInfo);
    })
    window.miniShop2.addCallback("Cart.remove.before","events",(response)=>{
        console.log(response);
    })
})


let countEvents = 0;
function setEvent(event, props = {}) {
    console.log(event, props);
    countEvents++;
    if(_tmr === undefined) {
        var _tmr = [];

    }
    _tmr = _tmr || [];
    switch (event) {

        case "add_to_cart":
            ym(86113805,'reachGoal','add_to_cart',props);
            gtag("event", "add_to_cart", {
                currency: "RUB",
                items: [{
                    item_name: props.pagetitle,
                    item_category: props?.category?.split("/")[0],
                    item_category2: props?.category?.split("/")[1],
                    price: props.price,
                    quantity: props.quantity ?? 1,
                }],
                value: props.price * (props.quantity ?? 1),
            });
            VK.Retargeting.ProductEvent(PRICE_LIST_ID, 'add_to_cart', {
                'products': [
                    {
                        id: props.id,
                        price: props.price,
                        price_old: props.old_price,
                    }
                ],
                'currency_code': 'RUR',
            });
            ClTrack("addToCart", {
                product: {
                    id: props.id,
                },
                price: props.price,
            });
            _tmr.push({
                type: 'AddToCart',
                productid: props.id,
                totalvalue: props.price,
            });

            break;
        case "remove_from_cart":
            gtag("event", "remove_from_cart", {
                currency: "RUB",
                items: [{
                    item_name: props.pagetitle,
                    item_category: props?.category?.split("/")[0],
                    item_category2: props?.category?.split("/")[1],
                    price: props.price,
                    quantity: props.quantity ?? 1,
                }],
                value: props.price * (props.quantity ?? 1),
            });
            ClTrack("removeFromCart", {
                product: {
                    id: props.id
                }
            });
            VK.Retargeting.ProductEvent(PRICE_LIST_ID, 'remove_from_cart', {
                'products': [
                    {
                        id: props.id,
                        price: props.price,
                        price_old: props.old_price,
                    }
                ],
                'currency_code': 'RUR',
            });
            break;
        case "purchase":
            let products = [];
            document.querySelectorAll(".auten-cart-item").forEach(product=>{
                products.push({
                    "pagetitle":product.querySelector(".auten-cart-item__title")?.innerText,
                    "key":product.getAttribute("id"),
                    "quantity":product.querySelector("input[name=count]")?.value,
                    "price":product.querySelector(".ms_price")?.innerText,
                    "old_price":product.querySelector(".ms_old_price")?.innerText,
                });
            })
            gtag("event", "purchase", {
                items: products.map((item) => {
                    return {
                        item_name: item.pagetitle,
                        price: item.price,
                        quantity: item.quantity ?? 1,
                    };
                }),
                currency: "RUB",
                shipping: document.querySelector(".ms2_order_delivery_cost")?.innerText.replace(/\D/g,""),
                value: document.querySelector(".ms2_order_cost")?.innerText.replace(/\D/g,""),
                transaction_id: props.transaction_id??0,
            });
            VK.Retargeting.ProductEvent(PRICE_LIST_ID, 'purchase', {
                products: products.map((item) => {
                    return {
                        id: '' + item.key,
                        price: item.price,
                        price_old: item.old_price,
                    };
                }),
                'currency_code': 'RUR',
            });
            break;
        case "view_item":
            VK.Retargeting.ProductEvent(PRICE_LIST_ID, 'view_product', {
                'products': [{
                    id: props.id,
                    price: props.price,
                    price_old: props.old_price,
                }],
                'currency_code': 'RUR',
            });
            gtag('event', 'view_item', {
                currency: "RUB",
                items: [{
                    item_name: props.pagetitle,
                    item_category: props?.category?.split("/")[0],
                    item_category2: props?.category?.split("/")[1],
                    price: props.price,
                    quantity: props.quantity ?? 1,
                }],
                value: props.price * (props.quantity ?? 1),
            });
            ClTrack("productView", {
                id: props.id,
            });
            _tmr?.push({
                type: 'itemView',
                productid: props.id,
                pagetype: 'product',
                totalvalue: props.price
            });
            break;
        case "add_to_wishlist":
            gtag("event", "add_to_wishlist", {
                currency: "RUB",
                items: [{
                    item_name: props.pagetitle,
                    item_category: props?.category?.split("/")[0],
                    item_category2: props?.category?.split("/")[1],
                    price: props.price,
                    quantity: props.quantity ?? 1,
                }],
                value: props.price * (props.quantity ?? 1),
            });
            VK.Retargeting.ProductEvent(PRICE_LIST_ID, 'add_to_wishlist', {
                'products': [
                    {
                        id: props.id,
                        price: props.price,
                        price_old: props.old_price,
                    }
                ],
                'currency_code': 'RUR',
            });
            ClTrack("addToFav", {
                product: {
                    id: props.id
                },
                price: props.price
            });
            break;
        case "remove_from_wishlist":
            ClTrack("removeFromFav", {
                product: {
                    id: props.id
                }
            });
            VK.Retargeting.ProductEvent(PRICE_LIST_ID, "remove_from_wishlist", {
                'products': [
                    {
                        id: props.id,
                        price: props.price,
                        price_old: props.old_price,
                    }
                ],
            });
            break;
        case "view_item_list":
            gtag("event", "view_item_list", {
                items: Object.keys(props.products).map((item) => {
                    item = props.products[item];
                    return {
                        item_name: item.pagetitle,
                        item_category: item.category.split("/")[0],
                        item_category2: item.category.split("/")[1],
                        price: item.price,
                        quantity: item.quantity ?? 1,
                    };
                }),
            });
            VK.Retargeting.ProductEvent(PRICE_LIST_ID, 'view_category', {
                products: Object.keys(props.products).map((item) => {
                    item = props.products[item];
                    return {
                        id: '' + item.id,
                        item: item.price,
                        price_old: item.old_price,
                    };
                }),
                'currency_code': 'RUR',
            });
            ClTrack('categoryView', {
                id: props.id,
            });
            break;
        case 'maxma-registration':
            gtag("event", "registration", {
                category: "maxma"
            });
            ym(86113805, 'reachGoal', 'maxma-reg')
            roistat.event.send('maxma-reg')
            break;
    }
    return true;
}

$(document).on('smsCodeCheck', (e, response, form, values) => {
    if (response.isRegister) {
        setEvent("maxma-registration");
    }
});
let bannerItemsParent = document.querySelector('.top-banner__items');
let bannerItems = bannerItemsParent.querySelectorAll('.top-banner-item');
let active = bannerItemsParent.querySelector('.top-banner-item.active');
if (!active)
    bannerItems[0].classList.add('active');
active.nextElementSibling?.classList.add('next');
active.previousElementSibling?.classList.add('prev');
setInterval(() => {
    if (bannerItems.length < 2)
        return;
    let prev = bannerItemsParent.querySelector('.top-banner-item.active');
    if (!prev) {
        bannerItems[0].classList.add('active');
        prev = bannerItems[0];
    }
    let newActive = prev.nextElementSibling;
    if (!newActive)
        newActive = bannerItems[0];
    let next = newActive.nextElementSibling;
    if (!next)
        next = bannerItems[0];
    bannerItems.forEach(item=>{
        item.classList.remove('active');
        item.classList.remove('prev');
        item.classList.remove('next');
    })
    prev.classList.add('prev');
    newActive.classList.add('active');
    next.classList.add('next');
}, 5000);