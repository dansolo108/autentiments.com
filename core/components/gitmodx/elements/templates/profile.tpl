{extends 'template:1'}

{block 'main'}
    <main class="au-profile">
        <div class="au-profile__nav">
            <div class="au-profile__nav-row">
                <h1 class="au-h1  au-profile__title">{$_modx->resource.pagetitle}</h1>
                <div class="au-profile__tabs">
                    <a class="au-profile__tab  au-tab-title  active" href="data">{'stik_profile_personal_data' | lexicon}</a>
                    <a class="au-profile__tab  au-tab-title" href="purchases">{'stik_profile_my_orders' | lexicon}</a>
                </div>
                <a class="au-profile__output" href="{10 | url : [] : ['service' => 'logout']}">
                    {'stik_profile_logout' | lexicon}
                    <svg width="16" height="16" viewBox="0 0 16 16" fill="none" xmlns="http://www.w3.org/2000/svg">
                        <path fill-rule="evenodd" clip-rule="evenodd" d="M1.99609 1.99609H8.99815V2.99609H2.99609V12.9974H8.99815V13.9974H1.99609V1.99609Z" fill="#1A1714"></path>
                        <path fill-rule="evenodd" clip-rule="evenodd" d="M11.2415 4.98528L14.1096 7.99677L11.2415 11.0083L10.5174 10.3186L12.2525 8.49677H6.53027V7.49677H12.2525L10.5174 5.67493L11.2415 4.98528Z" fill="#1A1714"></path>
                    </svg>  
                </a>
            </div>
        </div>
        <div class="au-profile__content  page-container">
            <div class="au-profile__loyalty-box">
                {if !$_modx->user.join_loyalty}
                    <div class="au-profile__loyalty  active">
                        <h2 class="au-h2  au-profile__loyalty-title">{'stik_profile_loyalty_title' | lexicon}</h2>
                        <p class="au-ordering__loyalty-text loyalty-text_join">
                            {'stik_loyalty_join_text' | lexicon}
                        </p>
                        <p class="au-ordering__loyalty-text loyalty-text_no-tel">
                            {'stik_loyalty_no_phone_text' | lexicon}
                        </p>
                        {'!AjaxForm' | snippet : [
                            'snippet' => 'joinLoyalty',
                            'form' => 'joinLoyalty.form',
                        ]}
                    </div>
                {/if}
                <div class="au-profile__loyalty  au-profile__loyalty_bonuses {if $_modx->user.join_loyalty}active{/if}">
                    <h2 class="au-h2  au-profile__loyalty-title">
                        {'stik_profile_loyalty_bonuses' | lexicon}
                        {set $maxmaClientBalance = '!maxmaClientBalance' | snippet : ['phone' => $_modx->user.mobilephone]}
                        <span class="au-bonuses__count">
                            <span>{'!msMultiCurrencyPriceFloor' | snippet : ['price' => $maxmaClientBalance]}</span>
                            {$maxmaClientBalance | declension : ('stik_declension_bonuses' | lexicon)}
                        </span>
                    </h2>
                    {'!getLoyaltyInfo' | snippet}
                    <div class="au-profile__loyalty-row">
                        <span class="au-profile__loyalty-col">{'stik_profile_loyalty_current_level' | lexicon} {*$_modx->getPlaceholder('loyalty.discount')*}</span>
                        <b class="au-profile__loyalty-value" id="current_level">{('stik_loyalty_level_name_' ~ $_modx->getPlaceholder('loyalty.id')) | lexicon}</b>
                    </div>
                    <div class="au-profile__loyalty-range">
                        <div class="au-profile__loyalty-slider" style="width: {$_modx->getPlaceholder('loyalty.next_slider_percent') ?: 100}%;"></div>
                    </div>
                    <div class="au-profile__loyalty-row">
                        <span class="au-profile__loyalty-col">{'stik_profile_loyalty_purchase_amount' | lexicon}</span>
                        <b class="au-profile__loyalty-value"><span id="amount">{'!msMultiCurrencyPriceFloor' | snippet : ['price' => $_modx->getPlaceholder('loyalty.amount')]}</span> {$_modx->getPlaceholder('msmc.symbol_right')}</b>
                    </div>
                    <div class="au-profile__loyalty-row  au-profile__amount_level">
                        <span class="au-profile__loyalty-col">{'stik_profile_loyalty_to_next_level' | lexicon}</span>
                        <b class="au-profile__loyalty-value"><span id="amount_level">{if $_modx->getPlaceholder('loyalty.next_amount')}{'!msMultiCurrencyPriceFloor' | snippet : ['price' => $_modx->getPlaceholder('loyalty.next_amount')]}</span> {$_modx->getPlaceholder('msmc.symbol_right')}{else}-{/if}</b>
                    </div>
                    <a class="au-profile__loyalty-tab  au-text-tab_js" href="loyalty">{'stik_profile_loyalty_link_about' | lexicon}</a>
                </div>
            </div>

            <div class="au-profile__tab-content  au-tab-content  active" data-tab="data">
                {'!UpdateProfile' | snippet : [
                    'preHooks' => 'dobHook',
                    'useExtended' => '0',
                    'allowedFields' => 'name,surname,email,dob,country,city,zip,address,building,corpus,entrance,room',
                    'validate' => 'email:email:required',
                    'submitVar' => 'login-updprof-btn',
                ]}
                {if $.get['updpsuccess']}
                    <div class="alert alert-success mb-28" role="alert">
                        {'stik_profile_updpsuccess' | lexicon}
                    </div>
                {/if}
                <form class="au-profile__form" action="{$_modx->resource.id | url}" method="post">
                    <h2 class="au-h2  au-profile__subtitle">{'stik_profile_title_personal_data' | lexicon}</h2>
                    <div class="au-ordering__row  au-ordering__row_buyer">
                        <div class="custom-form__group">
                            <input class="custom-form__input{if $_modx->getPlaceholder('error.name')} error{/if}" type="text" name="name" id="name"
                                value="{$_modx->getPlaceholder('name')}" data-val="{$_modx->getPlaceholder('name')}"> 
                            <label class="custom-form__label" for="name">{'stik_profile_form_name' | lexicon}</label>
                            <span class="error_field">{$_modx->getPlaceholder('error.name')}</span>
                        </div>
                        <div class="custom-form__group">
                            <input class="custom-form__input{if $_modx->getPlaceholder('error.surname')} error{/if}" type="text" name="surname" id="surname"
                                value="{$_modx->getPlaceholder('surname')}" data-val="{$_modx->getPlaceholder('surname')}"> 
                            <label class="custom-form__label" for="surname">{'stik_profile_form_surname' | lexicon}</label>
                            <span class="error_field">{$_modx->getPlaceholder('error.surname')}</span>
                        </div>
                        <div class="custom-form__group">
                            <input class="custom-form__input{if $_modx->getPlaceholder('error.email')} error{/if}" type="email" name="email" id="email"
                                value="{$_modx->getPlaceholder('email') | filterFakeEmail}" data-val="{$_modx->getPlaceholder('email') | filterFakeEmail}"> 
                            <label class="custom-form__label" for="email">Email</label>
                            <span class="error_field">{$_modx->getPlaceholder('error.email')}</span>
                        </div>
                        <div class="custom-form__group custom-form__group_tel">
                            <input class="custom-form__input" type="text" name="mobilephone" id="mobilephone" disabled
                                value="{$_modx->getPlaceholder('mobilephone')}" data-val="{$_modx->getPlaceholder('mobilephone')}"> 
                            <label class="custom-form__label" for="mobilephone">{'stik_profile_form_mobilephone' | lexicon}</label>
                            <span class="error_field">{$_modx->getPlaceholder('error.mobilephone')}</span>
                            <div class="group-no-phone__title">Введите номер телефона</div>
                        </div>
                        <div class="custom-form__group custom-form__group_date">
                            <input class="custom-form__input datepicker-here{if $_modx->getPlaceholder('error.dob')} error{/if}" type="text" name="dob" id="dob"
                                value="{if $_modx->getPlaceholder('dob')}{$_modx->getPlaceholder('dob') | date : 'd.m.Y'}{/if}" data-val="{$_modx->getPlaceholder('dob')}"
                                data-date-format="dd.mm.yyyy" placeholder="{'stik_profile_form_dob_placeholder' | lexicon}"> 
                            <label class="custom-form__label" for="dob">{'stik_profile_form_dob' | lexicon}</label>
                            <span class="error_field">{$_modx->getPlaceholder('error.dob')}</span>
                        </div>
                    </div>
                    <h2 class="au-h2  au-profile__subtitle">{'stik_profile_title_address' | lexicon}</h2>
                    <div class="au-ordering__row">
                        <div class="custom-form__group   custom-form__group_arrow">
                            <input class="custom-form__input{if $_modx->getPlaceholder('error.country')} error{/if}" type="text" name="country" id="country"
                            value="{$_modx->getPlaceholder('country')}" data-val="{$_modx->getPlaceholder('country')}">
                            <label class="custom-form__label" for="country">{'stik_profile_form_country' | lexicon}</label>
                            <span class="error_field">{$_modx->getPlaceholder('error.country')}</span>
                        </div>
                        {foreach ['city','zip','address','building','corpus','entrance','room'] as $k => $v}
                            <div class="custom-form__group  custom-form__group_{$v}">
                                <input class="custom-form__input{if $_modx->getPlaceholder('error.'~$v)} error{/if}"
                                    type="{$v == 'entrance' ? 'number' : 'text'}" name="{$v}" id="{$v}"
                                    value="{$_modx->getPlaceholder($v)}" data-val="{$_modx->getPlaceholder($v)}">
                                <label class="custom-form__label" for="city">{('stik_profile_form_'~$v) | lexicon}</label>
                                <span class="error_field">{$_modx->getPlaceholder('error.'~$v)}</span>
                            </div>
                        {/foreach}
                    </div>
                    {*<button class="au-btn  au-profile__submit" type="submit" disabled="">Сохранить изменения
                        <!-- <span class="au-btn  au-profile__submit-btn">Сохранить изменения</span> -->
                    </button>*}
                    <input type="submit" name="login-updprof-btn" class="au-btn  au-profile__submit" disabled="" value="{'stik_profile_form_submit' | lexicon}">
                </form>
                <h2 class="au-h2  au-profile__subtitle">{'stik_profile_form_password' | lexicon}</h2>
                <button class="au-btn-light  au-change-password__btn-open">{'stik_profile_form_password_change' | lexicon}</button>
            </div>
            <div class="au-profile__tab-content  au-tab-content" data-tab="purchases">
                <h2 class="au-h2  au-profile__order-title">{'stik_profile_orders_title' | lexicon}</h2>
                {set $orders = '!pdoArchiveYear' | snippet : [
                    'limit' => 0,
                    'tpl' => 'stik.tpl.msOrders.row',
                    'tplYear' => '@INLINE <span class="au-profile__order-year">{$year}</span><ul class="au-profile__orders">{$wrapper}</ul>',
                    'class' => 'msOrder',
                    'sortby' => 'createdon',
                    'sortdir' => 'DESC',
                    'tpl' => 'stik.tpl.msOrders.row',
                    'where' => [
                        'user_id' => $_modx->user.id
                    ],
                ]}
                {if $orders}
                    {$orders}
                {else}
                    <div class="au-profile__orders_empty">
                        <p class="au-profile__orders-text">{'stik_profile_orders_empty_text' | lexicon}</p>
                        <a class="au-btn  au-profile__orders-btn" href="{7|url}">{'stik_profile_orders_empty_btn' | lexicon}</a>
                    </div>
                {/if}
            </div>
        </div>
    </main>

    {'!ChangePassword' | snippet : [
        'submitVar' => 'change-password',
        'placeholderPrefix' => 'cp.',
        'validateOldPassword' => 0,
        'reloadOnSuccess' => 0,
    ]}
    {if $.get['change_pw'] || $_modx->getPlaceholder('cp.successMessage')}
        {$_modx->regClientScript("
            <script>
                $(document).ready(function() {
                    $('.au-change-password__btn-open').trigger('click');
                });
            </script>
        ", true)}
    {/if}
    <div class="au-modal au-modal-change-password  modal">
        <div class="au-modal__wrapper">
            <button class="au-close modal-close  au-change-password__close" aria-label="{'stik_modal_close' | lexicon}"></button>
            <div class="au-modal__content  au-change-password">
                <h3 class="au-h2  au-change-password__title">{'stik_change_pw_title' | lexicon}</h3>
                <div class="au-change-password__content">
                    {if !$_modx->getPlaceholder('cp.successMessage')}
                        <form class="au-change-password__form" action="{$_modx->resource.id | url : [] : ['change_pw' => '1']}" method="post">
                            <div class="custom-form__group">
                                <input class="custom-form__input{if $_modx->getPlaceholder('cp.error.password_new')} error{/if}"
                                    value="{$_modx->getPlaceholder('cp.password_new')}" data-val="{$_modx->getPlaceholder('cp.password_new')}" 
                                    type="password" name="password_new" id="password_new">
                                <label class="custom-form__label" for="new_password">{'stik_change_pw_form_password_new' | lexicon}</label>
                                <span class="error_field">{$_modx->getPlaceholder('cp.error.password_new')}</span>
                            </div>
                            <div class="custom-form__group">
                                <input class="custom-form__input{if $_modx->getPlaceholder('cp.error.password_new_confirm')} error{/if}"
                                    value="{$_modx->getPlaceholder('cp.password_new_confirm')}" data-val="{$_modx->getPlaceholder('cp.password_new_confirm')}" 
                                    type="password" name="password_new_confirm" id="password_new_confirm">
                                <label class="custom-form__label" for="new_password">{'stik_change_pw_form_password_new_confirm' | lexicon}</label>
                                <span class="error_field">{$_modx->getPlaceholder('cp.error.password_new_confirm')}</span>
                            </div>
                            <input class="au-btn  au-change-password__btn" type="submit" name="change-password" value="{'stik_change_pw_form_submit' | lexicon}"/>
                        </form>
                    {else}
                        <div class="au-change-password__successfully">
                            <p class="au-change-password__text">{'stik_change_pw_form_success' | lexicon}</p>
                            <button class="au-btn  au-change-password__btn">{'stik_change_pw_form_success_btn' | lexicon}</button>
                        </div>
                    {/if}
                </div>
            </div>
        </div>
    </div>
{/block}