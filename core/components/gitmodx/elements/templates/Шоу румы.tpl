{extends 'template:1'}
{block 'main'}
    <main class="showrooms">
        <div class="showroom">
            <div class="showroom__address">
                <h4 class="showroom__city">Санкт-петербург</h4>
                Басков переулок, 26
            </div>
            <div class="showroom__work-time">
                График работы:<br>
                ежедневно с 11:00 до 22:00
            </div>
            <div class="showroom__contacts">
                Номер для связи:<br>
                <a href="tel:+79215702113">+7 921 570-21-13</a> (Телефон/Whats App)
            </div>
            <div class="showroom__photos swiper-container">
                <div class="swiper-wrapper">
                    <div class="showroom-photo swiper-slide">
                        <img src="/assets/tpl/img/showrooms/СанктПетербург/1.jpg" alt="">
                    </div>
                     <div class="showroom-photo swiper-slide">
                         <img src="/assets/tpl/img/showrooms/СанктПетербург/2.jpg" alt="">
                     </div>
                     <div class="showroom-photo swiper-slide">
                         <img src="/assets/tpl/img/showrooms/СанктПетербург/3.jpg" alt="">
                     </div>
                     <div class="showroom-photo swiper-slide">
                         <img src="/assets/tpl/img/showrooms/СанктПетербург/4.jpg" alt="">
                     </div>
                </div>
            </div>
            <div class="photos__pagination"></div>
            <div class="showroom__map">
                <iframe src="https://yandex.ru/map-widget/v1/?um=constructor%3A21969cf4edc2b619aedb5f5537520b384852429a03796ab09e122b3cde1fac1f&amp;source=constructor" width="100%" height="400" frameborder="0"></iframe>
            </div>
        </div>
        <div class="showroom">
            <div class="showroom__address">
                <h4 class="showroom__city">Москва</h4>
                ул. Пятницкая, 7, стр.5
            </div>
            <div class="showroom__work-time">
                График работы:<br>
                ежедневно с 11:00 до 22:00
            </div>
            <div class="showroom__contacts">
                Номер для связи:<br>
                <a href="tel:+79263022528">+7 926 302-25-28</a> (Телефон/Whats App)
            </div>
            <div class="showroom__photos swiper-container">
                <div class="swiper-wrapper">
                    <div class="showroom-photo swiper-slide">
                        <img src="/assets/tpl/img/showrooms/Москва/1.jpg" alt="">
                    </div>
                     <div class="showroom-photo swiper-slide">
                         <img src="/assets/tpl/img/showrooms/Москва/2.jpg" alt="">
                     </div>
                     <div class="showroom-photo swiper-slide">
                         <img src="/assets/tpl/img/showrooms/Москва/3.jpg" alt="">
                     </div>
                     <div class="showroom-photo swiper-slide">
                         <img src="/assets/tpl/img/showrooms/Москва/4.png" alt="">
                     </div>
                </div>
            </div>
            <div class="photos__pagination"></div>
            <div class="showroom__map">
                <iframe src="https://yandex.ru/map-widget/v1/?um=constructor%3Ad2d55147c88bea7bd8ece5725d31554712700aad3a202ea97da0a73404050c66&amp;source=constructor" width="100%" height="400" frameborder="0"></iframe>
            </div>
        </div>
        <div class="partners">
            <div class="partners__title">
                Адреса партнеров Autentiments, где можно приобрести наш товар:
            </div>
            <div class="partners__items">
                <div class="partner">
                    Онлайн-магазин Babochka <br>
                    babochka.ru
                </div>
                <div class="partner">
                    Petrovka 15 concept store <br>
                    Москва ул. Петровка, 15
                </div>
                <div class="partner">
                    Outfit <br>
                    Екатеринбург, ул. Радищева, 24
                </div>
                <div class="partner">
                    FrWL store <br>
                    Казань, ул. Кремлёвская, 2а
                </div>
                <div class="partner">
                    O DA BRANDS <br>
                    г. Казань ТЦ KazanMall Павлюхина, 91, 2 этаж
                </div>
            </div>
        </div>
    </main>
{/block}