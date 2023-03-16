{extends 'template:1'}

{block 'main'}
    <main class="au-contacts">
        <h1 class="visually-hidden">{$_modx->resource.pagetitle}</h1>
        <div class="au-contacts__cover">
            {if $_modx->resource.video}
                <video class="au-contacts__img" webkit-playsinline playsinline autoplay loop muted poster="">
                    <source src="/assets/uploads/{$_modx->resource.video}" type="video/mp4">
                </video>
            {else}
                {include 'picture' img=$_modx->resource.img width=1440 height=498 class='au-contacts__img'}
            {/if}
        </div>
        <div class="au-contacts__content">
            <div class="au-contacts__container">
                <div class="au-contacts__row">
                    <div class="au-contacts__col">
                        <h2 class="au-h2  au-contacts__title">{'stik_contacts_title_contacts' | lexicon}</h2>
                        <ul class="au-contacts__list">
                            {if $_modx->config.phone}
                                <li class="au-contacts__item">
                                    <a class="au-contacts__link  au-contacts__link_tel" href="tel:{$_modx->config.phone | preg_replace : '/[^0-9+]/' : ''}">
                                        <svg width="20" height="20" viewBox="0 0 20 20" fill="none" xmlns="http://www.w3.org/2000/svg">
                                            <path d="M5.36484 2.34056C4.60376 2.29146 3.09355 2.7168 2.45542 4.33491C2.26033 4.8296 2.32154 5.46292 2.47799 6.08532C2.64018 6.73057 2.93048 7.46128 3.30101 8.20947C4.04203 9.70579 5.13435 11.3328 6.27674 12.5754C7.56589 13.9777 9.59338 15.3702 11.4571 16.3356C12.3917 16.8198 13.3039 17.2064 14.0809 17.4341C14.469 17.5479 14.8362 17.6258 15.1633 17.6528C15.4809 17.6789 15.8106 17.6616 16.0904 17.5377C17.6877 16.8304 18.0409 15.2965 17.968 14.5431C17.9449 14.3045 17.7983 14.1087 17.7004 13.9924C17.584 13.8541 17.4329 13.7116 17.269 13.5724C16.9392 13.2923 16.5073 12.9837 16.0651 12.6909C15.1852 12.1084 14.1994 11.5464 13.7775 11.3566C13.1096 11.056 12.4769 11.4272 12.088 11.7378C11.73 12.0238 11.409 12.3948 11.2028 12.6332C11.1976 12.6391 11.1926 12.645 11.1876 12.6507C11.1686 12.6462 11.1442 12.6394 11.1138 12.6292C10.9441 12.5723 10.7111 12.4466 10.4378 12.2632C9.90555 11.9062 9.32908 11.4051 8.975 11.0618L8.96694 11.0526C8.95377 11.0377 8.93418 11.0153 8.90921 10.9864C8.85924 10.9286 8.78788 10.8451 8.70344 10.7432C8.53387 10.5387 8.31472 10.2641 8.11071 9.97831C7.90293 9.68718 7.727 9.40656 7.62939 9.18601C7.60211 9.12436 7.58507 9.07642 7.57479 9.04113C7.59801 9.01798 7.62307 8.99319 7.64962 8.96692C7.86625 8.75255 8.18255 8.43957 8.42003 8.11684C8.71223 7.71974 9.06704 7.07014 8.72996 6.41283C8.52106 6.00548 7.92157 5.04218 7.30154 4.18048C6.99043 3.74811 6.66343 3.32621 6.36808 3.00534C6.22126 2.84583 6.07144 2.69935 5.9268 2.58752C5.80373 2.49238 5.6027 2.35578 5.36484 2.34056ZM5.36484 2.34056L5.33265 2.83953L5.36384 2.3405C5.36417 2.34052 5.36451 2.34054 5.36484 2.34056ZM11.2339 12.6588C11.2405 12.658 11.2444 12.6583 11.2446 12.6587C11.2449 12.659 11.2416 12.6594 11.2339 12.6588Z" stroke="#1A1714"></path>
                                        </svg>
                                        {$_modx->config.phone}
                                    </a>
                                </li>
                            {/if}
                            {if $_modx->config.whatsapp}
                                <li class="au-contacts__item">
                                    <a class="au-contacts__link" href="https://wa.me/{$_modx->config.whatsapp | preg_replace : '/[^0-9]/' : ''}" target="_blank">
                                        <svg width="20" height="20" viewBox="0 0 20 20" fill="none" xmlns="http://www.w3.org/2000/svg">
                                            <path fill-rule="evenodd" clip-rule="evenodd" d="M9.72662 7.7356V7.7306C9.72085 7.71672 9.71411 7.70325 9.70646 7.69031L9.70142 7.68031L9.69638 7.67532L8.26013 5.31784C8.23227 5.27151 8.19321 5.23292 8.14654 5.20564C8.09987 5.17835 8.04708 5.16323 7.99304 5.16167C7.99095 5.16157 7.98887 5.16146 7.98678 5.16135C7.97879 5.16092 7.97079 5.16049 7.9628 5.16086C7.93879 5.16326 7.91512 5.16834 7.89225 5.17601C7.17393 5.39653 6.19654 6.2551 5.9218 6.7629C5.64707 7.2707 5.61236 7.86589 5.7303 8.47055C5.96619 9.67988 6.80325 10.9797 7.86201 12.0622C8.92077 13.1447 10.1974 14.0112 11.4149 14.2635C12.0236 14.3897 12.6297 14.3563 13.1535 14.0822C13.6773 13.808 14.5627 12.8282 14.8215 12.0973C14.8454 12.0288 14.8455 11.9542 14.8217 11.8857C14.7979 11.8171 14.7517 11.7586 14.6905 11.7195L12.3925 10.2789L12.3875 10.2739C12.3761 10.2664 12.3644 10.2597 12.3522 10.2537C12.3184 10.2318 12.2808 10.2164 12.2413 10.2084H12.2312C12.2229 10.2064 12.2145 10.2047 12.2061 10.2034H12.201C12.1472 10.2003 12.0935 10.2106 12.0448 10.2336C12.0271 10.2421 12.0102 10.2522 11.9944 10.2638C11.9839 10.27 11.9738 10.2767 11.9642 10.284C11.9607 10.2889 11.9573 10.294 11.9541 10.2991C11.9414 10.31 11.9296 10.3217 11.9188 10.3344L11.4149 10.8432C10.5011 10.3834 9.79856 9.61268 9.16724 8.57132L9.63087 8.11291C9.63973 8.10499 9.64814 8.09659 9.65607 8.08774C9.66308 8.08132 9.66981 8.0746 9.67623 8.06759V8.06259C9.68324 8.05617 9.68997 8.04945 9.69638 8.04244V8.03744C9.69989 8.03249 9.70325 8.02743 9.70646 8.02229C9.7099 8.01903 9.71326 8.0157 9.71654 8.01229C9.72025 8.00404 9.72362 7.99564 9.72662 7.98711V7.98211L9.73166 7.97211C9.73525 7.96552 9.73862 7.95879 9.74174 7.95196C9.74375 7.94364 9.74543 7.93524 9.74678 7.92679V7.92179C9.74894 7.9118 9.75062 7.90171 9.75182 7.89156V7.88657C9.75215 7.87817 9.75215 7.86976 9.75182 7.86137V7.85637C9.75215 7.84798 9.75215 7.83958 9.75182 7.83119V7.8212C9.75047 7.81274 9.74879 7.80433 9.74678 7.796V7.791C9.74177 7.77196 9.73502 7.75342 9.72662 7.7356ZM8.57269 7.75291L7.72994 6.36961C7.61969 6.44267 7.50089 6.53038 7.37916 6.63136C7.04791 6.90616 6.84896 7.15072 6.80133 7.23874C6.67039 7.48077 6.62259 7.82172 6.71181 8.27911C6.8955 9.22087 7.58811 10.352 8.57692 11.363C9.56727 12.3756 10.6748 13.0889 11.6178 13.2843C12.0705 13.3782 12.4264 13.334 12.6897 13.1962C12.7857 13.146 13.0399 12.9442 13.3292 12.6084C13.4411 12.4785 13.5396 12.35 13.6221 12.23L12.2813 11.3894L12.1253 11.5469C11.8205 11.8546 11.3523 11.9311 10.9654 11.7365C9.82251 11.1614 8.9976 10.2204 8.31211 9.08974C8.07224 8.69409 8.13513 8.18554 8.46415 7.86022L8.57269 7.75291Z" fill="#1A1714"></path>
                                            <path fill-rule="evenodd" clip-rule="evenodd" d="M3.0684 14.5559C3.22901 14.8024 3.27294 15.1071 3.18849 15.3889L2.47222 17.7791L4.80318 16.9747C5.08678 16.8768 5.39933 16.9114 5.65463 17.069C6.91485 17.8468 8.40579 18.2968 10.0006 18.2968C14.5784 18.2968 18.2971 14.5781 18.2971 10.0003C18.2971 5.42244 14.5784 1.70375 10.0006 1.70375C5.42466 1.70375 1.7041 5.42246 1.7041 10.0003C1.7041 11.6622 2.19548 13.2162 3.0684 14.5559ZM2.23058 15.1018C1.25654 13.607 0.704102 11.8645 0.704102 10.0003C0.704102 4.87003 4.87253 0.703751 10.0006 0.703751C15.1307 0.703751 19.2971 4.87015 19.2971 10.0003C19.2971 15.1303 15.1307 19.2968 10.0006 19.2968C8.21557 19.2968 6.54344 18.7927 5.12942 17.92L1.73353 19.0919C1.66003 19.1173 1.58097 19.1219 1.50503 19.1052C1.4291 19.0885 1.35924 19.0512 1.30315 18.9974C1.24705 18.9435 1.2069 18.8753 1.1871 18.8001C1.16729 18.7249 1.16862 18.6457 1.19092 18.5713L2.23058 15.1018Z" fill="#1A1714"></path>
                                        </svg>
                                        Whatsapp {$_modx->config.whatsapp}
                                    </a>
                                </li>
                            {/if}
                            {if $_modx->config.vk}
                                <li class="au-contacts__item">
                                    <a class="au-contacts__link" href="{$_modx->config.vk}" target="_blank">
                                        <svg xmlns="http://www.w3.org/2000/svg" width="20" height="12" viewBox="0 0 40 24" fill="none">
                                        <path d="M23.55 23.19V16.55C28.01 17.23 29.42 20.74 32.26 23.19H39.5C37.6899 19.149 34.9892 15.5697 31.6 12.72C34.2 9.14003 36.96 5.77003 38.31 0.660034H31.73C29.15 4.57003 27.79 9.15003 23.55 12.17V0.660034H14L16.28 3.48003V13.53C12.58 13.1 10.08 6.33003 7.37 0.660034H0.5C3 8.32003 8.26 25.13 23.55 23.19V23.19Z" stroke="black" stroke-linecap="round" stroke-linejoin="round"/>
                                        </svg>
                                        {'stik_link_vk' | lexicon}
                                    </a>
                                </li>
                            {/if}
                            <li class="au-contacts__item">
                                <a class="au-contacts__link" href="https://t.me/autentiments_official" target="_blank">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 48 48" fill="none">
                                    <path d="M40.8296 8.47998C41.9696 8.47998 42.8296 9.47998 42.3696 11.34L36.7896 37.64C36.3996 39.51 35.2696 39.96 33.7096 39.09L20.3996 29.26C20.348 29.2229 20.3059 29.1742 20.2769 29.1176C20.2479 29.0611 20.2328 28.9985 20.2328 28.935C20.2328 28.8715 20.2479 28.8088 20.2769 28.7523C20.3059 28.6958 20.348 28.647 20.3996 28.61L35.7696 14.73C36.4696 14.11 35.6196 13.81 34.6996 14.37L15.4096 26.54C15.3511 26.5781 15.2847 26.6025 15.2154 26.6111C15.1461 26.6198 15.0757 26.6126 15.0096 26.59L6.8196 24C4.9996 23.47 4.9996 22.22 7.2296 21.33L39.9996 8.68998C40.2595 8.5653 40.5417 8.4939 40.8296 8.47998V8.47998Z" stroke="black" stroke-linecap="round" stroke-linejoin="round"/>
                                    </svg>
                                    {'stik_link_telegram' | lexicon}
                                </a>
                            </li>
                            {*if $_modx->config.instagram}
                                <li class="au-contacts__item">
                                    <a class="au-contacts__link" href="{$_modx->config.instagram}" target="_blank">
                                        <svg width="20" height="20" viewBox="0 0 20 20" fill="none" xmlns="http://www.w3.org/2000/svg">
                                            <path fill-rule="evenodd" clip-rule="evenodd" d="M5.75 1.99609C3.67897 1.99609 2 3.67516 2 5.74648V14.2472C2 16.3186 3.67897 17.9976 5.75 17.9976H14.25C16.321 17.9976 18 16.3186 18 14.2472V5.74648C18 3.67516 16.321 1.99609 14.25 1.99609H5.75ZM1 5.74648C1 3.12296 3.1266 0.996094 5.75 0.996094H14.25C16.8734 0.996094 19 3.12296 19 5.74648V14.2472C19 16.8708 16.8734 18.9976 14.25 18.9976H5.75C3.1266 18.9976 1 16.8708 1 14.2472V5.74648Z" fill="#1A1714"></path>
                                            <path fill-rule="evenodd" clip-rule="evenodd" d="M10.4156 7.19738C9.8261 7.10996 9.22403 7.21066 8.69505 7.48513C8.16607 7.75961 7.7371 8.1939 7.46917 8.72623C7.20123 9.25856 7.10797 9.86181 7.20265 10.4502C7.29733 11.0386 7.57513 11.5821 7.99653 12.0035C8.41793 12.4249 8.96148 12.7027 9.54987 12.7974C10.1383 12.8921 10.7415 12.7988 11.2738 12.5309C11.8062 12.263 12.2405 11.834 12.5149 11.305C12.7894 10.776 12.8901 10.174 12.8027 9.58447C12.7135 8.98314 12.4333 8.42645 12.0035 7.9966C11.5736 7.56675 11.0169 7.28655 10.4156 7.19738ZM8.23448 6.59751C8.95016 6.22616 9.76472 6.08993 10.5623 6.2082C11.3758 6.32884 12.129 6.70793 12.7106 7.28949C13.2921 7.87105 13.6712 8.62423 13.7919 9.43778C13.9101 10.2354 13.7739 11.0499 13.4026 11.7656C13.0312 12.4813 12.4436 13.0616 11.7234 13.4241C11.0032 13.7866 10.187 13.9128 9.391 13.7847C8.59495 13.6566 7.85956 13.2808 7.28942 12.7106C6.71929 12.1405 6.34345 11.4051 6.21535 10.6091C6.08726 9.81302 6.21343 8.99685 6.57593 8.27664C6.93843 7.55643 7.5188 6.96887 8.23448 6.59751Z" fill="#1A1714"></path>
                                            <path fill-rule="evenodd" clip-rule="evenodd" d="M14 4.50073C14 4.22459 14.2239 4.00073 14.5 4.00073H14.5086C14.7847 4.00073 15.0086 4.22459 15.0086 4.50073C15.0086 4.77687 14.7847 5.00073 14.5086 5.00073H14.5C14.2239 5.00073 14 4.77687 14 4.50073Z" fill="#1A1714"></path>
                                        </svg>
                                        {'stik_link_instagram' | lexicon}
                                    </a>
                                </li>
                            {/if*}
                            {if $_modx->config.pinterest}
                                <li class="au-contacts__item">
                                    <a class="au-contacts__link" href="{$_modx->config.pinterest}" target="_blank">
                                        <svg width="20" height="20" viewBox="0 0 20 20" fill="none" xmlns="http://www.w3.org/2000/svg">
                                            <path fill-rule="evenodd" clip-rule="evenodd" d="M3.31738 7.71795C3.31738 4.54991 6.03883 1.41991 10.2934 1.71465C13.6331 1.94601 16.7316 4.68313 16.6812 8.13732C16.6601 9.57729 16.164 11.0061 15.2983 12.0756C14.4277 13.1512 13.1685 13.8766 11.652 13.8394C10.9181 13.8214 10.2692 13.6986 9.67735 13.4175C9.43702 13.3034 9.21126 13.1656 8.9967 13.0036C8.31201 14.9284 7.40804 16.9001 6.45656 18.3047L5.62865 17.7438C6.58953 16.3254 7.52369 14.2416 8.2033 12.2406C8.17296 12.2047 8.14278 12.168 8.11276 12.1308L8.07361 12.0822L8.0476 12.0255C7.60901 11.0694 7.45882 9.57635 7.58721 8.36583C7.65155 7.75926 7.79128 7.16482 8.04169 6.72133C8.29601 6.27091 8.74194 5.86839 9.38715 5.97592C9.84421 6.0521 10.0585 6.43046 10.1495 6.74512C10.2422 7.06595 10.2582 7.46643 10.2331 7.89231C10.1822 8.75526 9.95027 9.89174 9.60404 11.1097C9.52242 11.3969 9.43395 11.6903 9.33929 11.9875C9.58655 12.2161 9.83897 12.3873 10.1064 12.5142C10.5381 12.7193 11.0402 12.8241 11.6765 12.8397C12.8308 12.868 13.8105 12.3243 14.521 11.4465C15.2364 10.5627 15.6633 9.35406 15.6813 8.12271C15.7225 5.3031 13.144 2.91452 10.2243 2.71226C6.5566 2.45817 4.31738 5.12057 4.31738 7.71795C4.31739 8.89356 4.68979 9.73551 5.05323 10.2807C5.23598 10.5548 5.41745 10.7552 5.5501 10.8846C5.61633 10.9493 5.67003 10.9959 5.70511 11.0249C5.72264 11.0394 5.73546 11.0495 5.74282 11.0551L5.74964 11.0603L5.74839 11.0594C5.7482 11.0592 5.74764 11.0588 5.45702 11.4657C5.1664 11.8726 5.16619 11.8724 5.16597 11.8722L5.16444 11.8712L5.16188 11.8693L5.15499 11.8642L5.13439 11.8487C5.11773 11.8359 5.09533 11.8183 5.06802 11.7957C5.01344 11.7506 4.93905 11.6856 4.85156 11.6002C4.67678 11.4296 4.44835 11.1761 4.22118 10.8354C3.76481 10.1508 3.31739 9.11891 3.31738 7.71795ZM5.74964 11.0603C5.75006 11.0606 5.74997 11.0605 5.74964 11.0603V11.0603ZM8.68042 10.7003C8.99564 9.56804 9.19182 8.56266 9.23484 7.83342C9.25754 7.44867 9.2347 7.18156 9.18882 7.02281C9.181 6.99578 9.17382 6.97551 9.16774 6.96059C9.10764 6.96896 9.02268 7.01782 8.91247 7.213C8.75889 7.48499 8.6397 7.92384 8.58164 8.4713C8.50437 9.19974 8.54493 10.0165 8.68042 10.7003ZM9.14821 6.92382C9.14834 6.92344 9.15018 6.92533 9.15336 6.93061C9.14968 6.92683 9.14809 6.92419 9.14821 6.92382Z" fill="#1A1714"></path>
                                        </svg>
                                        Pinterest
                                    </a>
                                </li>
                            {/if}
                            {if $_modx->config.email}
                                <li class="au-contacts__item">
                                    <a class="au-contacts__link" href="mailto:{$_modx->config.email}">
                                        <svg width="20" height="20" viewBox="0 0 20 20" fill="none" xmlns="http://www.w3.org/2000/svg">
                                            <path fill-rule="evenodd" clip-rule="evenodd" d="M2 4C1.44772 4 1 4.44772 1 5V15C1 15.5523 1.44772 16 2 16H18C18.5523 16 19 15.5523 19 15V5C19 4.44772 18.5523 4 18 4H2ZM2 5H18V5.72096L10.0075 10.42L2 5.71217V5ZM2 15H18V6.88098L10.0075 11.58L2 6.8722V15Z" fill="#1A1714"></path>
                                        </svg>
                                        {$_modx->config.email}
                                    </a> 
                                </li>
                            {/if}
                        </ul>   
                    </div>
                    <div class="au-contacts__col">
                        <h2 class="au-h2  au-contacts__title">{'stik_contacts_title_bank_details' | lexicon}</h2>
                        {$_modx->config['cultureKey'] == 'ru' ? $_modx->config.bank_details : $_modx->config.bank_details_en}
                    </div>
                </div>
                <div class="au-contacts__map-box">
                    <h2 class="au-h2  au-contacts__title">{'stik_contacts_title_address' | lexicon}</h2>
                    {if $_modx->config.address}
                        <p class="au-contacts__map-text">{$_modx->config['cultureKey'] == 'ru' ? $_modx->config.address : $_modx->config.address_en}</p>
                    {/if}
                    <div class="au-contacts__map" id="mymap">
                        {$_modx->config.map}
                    </div>
                </div>
                {if $_modx->config.address_msk}
                    <div class="au-contacts__map-box">
                        {if $_modx->config.address_msk}
                            <p class="au-contacts__map-text">{$_modx->config['cultureKey'] == 'ru' ? $_modx->config.address_msk : $_modx->config.address_msk_en}</p>
                        {/if}
                        <div class="au-contacts__map" id="mymap">
                            {$_modx->config.map_msk}
                        </div>
                    </div>
                {/if}
                <div class="au-contacts__row">
                    <h2 class="au-h2  au-contacts__title">{'stik_contacts_title_feedback' | lexicon}</h2>
                    <div class="au-contacts__message">
                        <div class="au-contacts__form-box">
                            <p class="au-contacts__col-text">{'stik_contacts_feedback_text' | lexicon}</p>
                            {'!AjaxForm' | snippet : [
                                'hooks' => 'csrf,spam,email,stikAmoCRM,FormItSaveForm',
                                'amoFields' => 'name==425633||email==425635||message==302807',
                                'formFields' => 'name,email,message',
                                'fieldNames' => 'name==Имя,email==Email,message==Сообщение',
                                'formName' => 'Обратная связь',
                                'validate' => '
                                    link:blank,
                                    name:required:stripTags:maxLength=^100^,
                                    email:required,
                                    message:required:stripTags:maxLength=^300^',
                                'emailTpl' => 'contactEmailTpl',
                                'form' => 'contacts.form',
                                'emailFrom' => $_modx->config.emailsender,
                                'emailFromName' => $_modx->config.site_name,
                                'emailSubject' => 'Обратная связь',
                                'emailTo' => $_modx->config.ms2_email_manager,
                                'validationErrorMessage' => 'stik_form_validation_error_message' | lexicon,
                                'successMessage' => 'stik_form_success_message' | lexicon,
                            ]}
                        </div>
                        <p class="au-contacts__message-info  au-contacts__text">{'stik_contacts_feedback_success' | lexicon}</p>
                    </div>
                </div>
            </div>
        </div>
    </main>
{/block}