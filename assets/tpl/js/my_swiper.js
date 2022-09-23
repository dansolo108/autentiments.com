const clientWindowWidth = document.documentElement.clientWidth;

if (document.querySelector('.au-home__slider')) {
    const homeSlider = new Swiper('.au-home__slider', {
        speed: 600,
        loop: true,
        autoplay: false,
        delay: 4000,
        spaceBetween: 0,
        slidesPerView: 1,
        pagination: {
            el: '.au-home__pagination',
            clickable: true
        },
        navigation: {
            nextEl: '.au-home__next',
            prevEl: '.au-home__prev',
        },
    });
}

if (document.querySelector('.au-liked__slider')) {
    const sliderCard = new Swiper('.au-liked__slider', {
        speed: 400,
        loop: false,
        spaceBetween: 0,
        slidesPerView: 2,
        slidesPerGroup: 2,
        pagination: {
            el: '.au-liked__pagination',
            clickable: true,
        },
        navigation: {
            nextEl: '.au-liked__next',
            prevEl: '.au-liked__prev',
        },
        breakpoints: {
            1024: {
                slidesPerView: 4,
                slidesPerGroup: 4
            }
        }
    });
}

if (document.querySelector('.au-lookbook__slider')) {
    const sliderCard = new Swiper('.au-lookbook__slider', {
        speed: 400,
        loop: true,
        spaceBetween: 0,
        slidesPerView: 1,
        pagination: {
            el: '.au-lookbook__pagination',
            clickable: true,
        },
        navigation: {
            nextEl: '.au-lookbook__next',
            prevEl: '.au-lookbook__prev',
        }
    });
}

function productGalleryInit() {
    if (document.querySelector('.au-product__slider') && clientWindowWidth < 1024) {
        const productSlider = new Swiper('.au-product__slider', {
            speed: 400,
            loop: true,
            spaceBetween: 0,
            slidesPerView: 1,
            pagination: {
                el: '.au-product__pagination',
                clickable: true,
            },
        });
    } else if (document.querySelector('.au-product__slider')) {
        document.querySelector('.au-product__slider').classList.remove('swiper-container');
    }
}
if (document.querySelector('.showroom__photos')) {
    const sliderPhotos = new Swiper('.showroom__photos', {
        speed: 400,
        loop: false,
        spaceBetween: 0,
        slidesPerView: 1,
        slidesPerGroup: 1,
        pagination: {
            el: '.photos__pagination',
            clickable: true,
        },
        navigation: {
            nextEl: '.au-liked__next',
            prevEl: '.au-liked__prev',
        },
        breakpoints: {
            1024: {
                slidesPerView: 4,
                slidesPerGroup: 4
            },
            600: {
                slidesPerView: 2,
                slidesPerGroup: 1
            },
        }
    });
}
productGalleryInit();