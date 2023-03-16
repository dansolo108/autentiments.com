<?php

include_once 'setting.inc.php';

$_lang['mspromocode'] = 'Промо-коды';
$_lang['mspromocode_menu_desc'] = 'Акционные купоны со скидками';
$_lang['mspromocode_coupons_intro_msg'] = '<b>Важно знать</b>. Если у промо-кода не указан ни один товар или раздел - он применится ко всему магазину.';
$_lang['mspromocode_actions_intro_msg'] = '<b>Важно знать</b>. Если у акции не указан ни один товар или раздел - она применится ко всему магазину.';
$_lang['mspromocode_action_coupons_intro_msg'] = '<b class="mspromocode-grid-row-freeze">Синим цветом</b> выделены купоны, замороженные от обновления при изменении данных в акции.<br /><b class="mspromocode-grid-row-activated">Зелёным цветом</b> выделены купоны, активированные при заказе.';
$_lang['mspromocode_msg_begin_save_object'] = '<span class="icon icon-lock" style="font-size:200%;"></span><br />Для начала сохраните объект';
// $_lang['mspromocode_msg_coupons_create'] = '<span class="icon icon-lock" style="font-size:200%;"></span><br />Сохраните акцию, чтобы добавить к ней список промо-кодов.';
// $_lang['mspromocode_msg_coupon_create_products'] = '<span class="icon icon-lock" style="font-size:200%;"></span><br />Сохраните промо-код, чтобы привязать его к ресурсам.';
// $_lang['mspromocode_msg_coupon_create_categories'] = '<span class="icon icon-lock" style="font-size:200%;"></span><br />Сохраните промо-код, чтобы привязать его к разделам.';
// $_lang['mspromocode_msg_tab_save_resources'] = '<b>Обратите внимание</b>. При любых изменениях в данной вкладке - результат <b>сохраняется автоматически</b>, а не по нажатию на кнопку "Сохранить".';
$_lang['mspromocode_msg_tab_save_resources'] = '<!--b>Обратите внимание</b>.<br /-->При любых изменениях на данной вкладке - результат <b>сохраняется автоматически</b>.';
$_lang['mspromocode_msg_tab_conditions'] = 'Условия, при которых пользователь сможет воспользоваться купоном.';
$_lang['mspromocode_msg_tab_orders'] = 'Заказы, к которым применялся промо-код.';

$_lang['mspromocode_tab_main'] = '<i class="icon icon-cog"></i>&nbsp; Основное';
$_lang['mspromocode_tab_links'] = '<i class="icon icon-link"></i>&nbsp; Привязки';
$_lang['mspromocode_tab_products'] = '<i class="icon icon-tag"></i>&nbsp; Товары';
$_lang['mspromocode_tab_categories'] = '<i class="icon icon-barcode"></i>&nbsp; Разделы';
$_lang['mspromocode_tab_action_coupons'] = '<i class="icon icon-tags"></i>&nbsp; Купоны';
$_lang['mspromocode_tab_conditions'] = '<i class="icon icon-list-ol"></i>&nbsp; Условия';
$_lang['mspromocode_tab_orders'] = '<i class="icon icon-list-alt"></i>&nbsp; Заказы';
$_lang['mspromocode_tab_coupons'] = 'Промо-коды';
$_lang['mspromocode_tab_actions'] = 'Акции';
$_lang['mspromocode_tab_actions_list'] = 'Список акций';
$_lang['mspromocode_tab_actions_coupons'] = 'Акционные промо-коды';
$_lang['mspromocode_tab_action'] = 'Акция';
$_lang['mspromocode_tab_resource_coupons'] = 'Купоны товаров';

// Выпадающие списки
$_lang['mspromocode_combo_filter_all'] = 'Все';
$_lang['mspromocode_combo_condition_from_total_cost'] = 'Сумма корзины (от)';
$_lang['mspromocode_combo_condition_to_total_cost'] = 'Сумма корзины (до)';
$_lang['mspromocode_combo_condition_from_total_count'] = 'Кол-во товаров в корзине (от)';
$_lang['mspromocode_combo_condition_to_total_count'] = 'Кол-во товаров в корзине (до)';
$_lang['mspromocode_combo_condition_user_group'] = 'Группа пользователя';

$_lang['mspromocode_grid_search'] = 'Поиск';
$_lang['mspromocode_grid_actions'] = 'Действия';

$_lang['mspromocode_id'] = 'ID';
$_lang['mspromocode_discount'] = 'Скидка';
$_lang['mspromocode_type'] = 'Тип';
$_lang['mspromocode_resource'] = 'Ресурс';
$_lang['mspromocode_resource_type'] = 'Тип ресурса';
$_lang['mspromocode_product'] = 'Товар';
$_lang['mspromocode_products'] = 'Товары';
$_lang['mspromocode_value'] = 'Значение';

$_lang['mspromocode_field_conditions'] = 'Условия';
$_lang['mspromocode_field_conditions_select'] = 'Список условий';
$_lang['mspromocode_field_conditions_select_empty'] = 'Условий для выбора больше нет...';
$_lang['mspromocode_field_conditions_list'] = 'Список применённых условий';

$_lang['mspromocode_field_products'] = 'Товары';
$_lang['mspromocode_field_products_select'] = 'Список доступных товаров';
$_lang['mspromocode_field_products_select_empty'] = 'Список доступных товаров пуст...';
$_lang['mspromocode_field_products_list'] = 'Список применённых товаров';

$_lang['mspromocode_field_categories'] = 'Разделы';
$_lang['mspromocode_field_categories_select'] = 'Список доступных разделов';
$_lang['mspromocode_field_categories_select_empty'] = 'Список доступных разделов пуст...';
$_lang['mspromocode_field_categories_list'] = 'Список применённых разделов';

$_lang['mspromocode_field_ms2_promocode'] = 'Промо-код (msPromoCode)';

$_lang['mspromocode_coupon_generate'] = 'Генерировать купоны';
$_lang['mspromocode_coupon_create'] = 'Создать промо-код';
$_lang['mspromocode_coupon_update'] = 'Изменить промо-код';
$_lang['mspromocode_coupon_enable'] = 'Включить промо-код';
$_lang['mspromocode_coupons_enable'] = 'Включить промо-коды';
$_lang['mspromocode_coupon_disable'] = 'Отключить промо-код';
$_lang['mspromocode_coupons_disable'] = 'Отключить промо-коды';
$_lang['mspromocode_coupon_remove'] = 'Удалить промо-код';
$_lang['mspromocode_coupons_remove'] = 'Удалить промо-коды';
$_lang['mspromocode_coupon_remove_confirm'] = 'Вы уверены, что хотите удалить этот промо-код?';
$_lang['mspromocode_coupons_remove_confirm'] = 'Вы уверены, что хотите удалить эти промо-коды?';

$_lang['mspromocode_coupon_id'] = 'ID';
$_lang['mspromocode_coupon_action'] = 'Акция';
$_lang['mspromocode_coupon_action_id'] = 'ID акции';
$_lang['mspromocode_coupon_discount'] = 'Скидка';
$_lang['mspromocode_coupon_power'] = 'Сила';
$_lang['mspromocode_coupon_mask'] = 'Маска';
$_lang['mspromocode_coupon_count'] = 'Кол-во';
$_lang['mspromocode_coupon_count_unlimit'] = 'Безгранично, если пусто';
$_lang['mspromocode_coupon_unlimited'] = '&infin;';
$_lang['mspromocode_coupon_code'] = 'Код купона';
$_lang['mspromocode_coupon_code_placeholder'] = 'Можно сгенерировать инструментом выше';
$_lang['mspromocode_coupon_code_gen'] = 'Маска для генерации';
$_lang['mspromocode_coupon_code_gen_btn'] = '<i class="icon icon-magic"></i> Генерировать';
// $_lang['mspromocode_coupon_code_gen_desc_before'] = 'Можно сгенерировать по маске, указанной ниже. Можно указать свою маску.<p><b>Было</b>: prefix-/([a-zA-Z0-9]{4-10})-([a-zA-Z0-9]{4})/<br/><b>Стало</b>: prefix-xvwJmvGwa-7jMC</p>';
$_lang['mspromocode_coupon_code_gen_desc_before'] = 'Можно сгенерировать по маске, указанной ниже. Можно указать свою маску.';
$_lang['mspromocode_coupon_code_gen_desc_after'] = '';
$_lang['mspromocode_coupon_description'] = 'Описание';
$_lang['mspromocode_coupon_begins'] = 'Начало';
$_lang['mspromocode_coupon_ends'] = 'Окончание';
$_lang['mspromocode_coupon_active'] = 'Включён';
$_lang['mspromocode_coupon_active_desc'] = 'Доступен ли данный купон для пользователей';
$_lang['mspromocode_coupon_oldprice'] = 'Только без старой цены';
$_lang['mspromocode_coupon_oldprice_desc'] = 'Применять только к товарам без старой цены';
$_lang['mspromocode_coupon_freeze'] = 'Заморожен';
$_lang['mspromocode_coupon_freeze_desc'] = 'При обновлении акции данные купона (скидку, время действия) не обновлять';
$_lang['mspromocode_coupon_allcart'] = 'Скидка на всю корзину';
$_lang['mspromocode_coupon_allcart_desc'] = 'Фиксированная скидка на всю корзину (нельзя выбрать товары, разделы)';
$_lang['mspromocode_coupon_orders'] = 'Заказов';

$_lang['mspromocode_condition_coupon_id'] = 'Купон';
$_lang['mspromocode_condition_action_id'] = 'Акция';
$_lang['mspromocode_condition_type'] = 'Условие';
$_lang['mspromocode_consition_remove'] = 'Удалить условие';
$_lang['mspromocode_conditions_remove'] = 'Удалить условия';
$_lang['mspromocode_consition_remove_confirm'] = 'Вы уверены, что хотите удалить это условие?';
$_lang['mspromocode_conditions_remove_confirm'] = 'Вы уверены, что хотите удалить эти условия?';

$_lang['mspromocode_resource_detach'] = 'Отвязать ресурс';
$_lang['mspromocode_resources_detach'] = 'Отвязать ресурсы';
$_lang['mspromocode_resource_detach_confirm'] = 'Вы уверены, что хотите отвязать этот ресурс?';
$_lang['mspromocode_resources_detach_confirm'] = 'Вы уверены, что хотите отвязать эти ресурсы?';

$_lang['mspromocode_order_id'] = 'ID заказа';
$_lang['mspromocode_order_num'] = 'Номер заказа';
$_lang['mspromocode_order_coupon_id'] = 'Купон';

$_lang['mspromocode_action'] = 'Акция';
$_lang['mspromocode_actions'] = 'Акции';
$_lang['mspromocode_action_id'] = 'ID';
$_lang['mspromocode_action_discount'] = 'Скидка';
$_lang['mspromocode_action_coupons'] = 'Купоны';
$_lang['mspromocode_action_activated'] = 'Активировано';
$_lang['mspromocode_action_name'] = 'Название';
$_lang['mspromocode_action_description'] = 'Описание';
$_lang['mspromocode_action_begins'] = 'Начало';
$_lang['mspromocode_action_ends'] = 'Окончание';
$_lang['mspromocode_action_ref'] = 'Реферальная';
$_lang['mspromocode_action_ref_desc'] = 'В данной акции реферальные промо-коды';
$_lang['mspromocode_action_active'] = 'Включена';
$_lang['mspromocode_action_active_desc'] = 'Доступна ли данная акция для пользователей';
$_lang['mspromocode_coupon_referrer_id'] = 'Владелец';
$_lang['mspromocode_coupon_referrer_username'] = 'Владелец';
$_lang['mspromocode_coupon_order_date'] = 'Активирован';
$_lang['mspromocode_coupon_order_num'] = 'Заказ';

$_lang['mspromocode_action_create'] = 'Добавить акцию';
$_lang['mspromocode_action_update'] = 'Изменить акцию';
$_lang['mspromocode_action_download'] = 'Скачать купоны в CSV';
$_lang['mspromocode_action_enable'] = 'Включить акцию';
$_lang['mspromocode_actions_enable'] = 'Включить акции';
$_lang['mspromocode_action_disable'] = 'Отключить акцию';
$_lang['mspromocode_actions_disable'] = 'Отключить акции';
$_lang['mspromocode_action_remove'] = 'Удалить акцию';
$_lang['mspromocode_actions_remove'] = 'Удалить акции';
$_lang['mspromocode_action_remove_confirm'] = 'Вы уверены, что хотите удалить эту акцию?';
$_lang['mspromocode_actions_remove_confirm'] = 'Вы уверены, что хотите удалить эти акции?';

// Для файла CSV заголовки
$_lang['mspromocode_action_download_id'] = 'ID купона';
$_lang['mspromocode_action_download_action_id'] = 'ID акции';
$_lang['mspromocode_action_download_action'] = 'Акция';
$_lang['mspromocode_action_download_code'] = 'Код купона';
$_lang['mspromocode_action_download_discount'] = 'Скидка';
$_lang['mspromocode_action_download_order_id'] = 'ID заказа';
$_lang['mspromocode_action_download_order_num'] = 'Заказ';
$_lang['mspromocode_action_download_activatedon'] = 'Активирован';
$_lang['mspromocode_action_download_begins'] = 'Начало';
$_lang['mspromocode_action_download_ends'] = 'Окончание';

// Ошибки
$_lang['mspromocode_err_field'] = 'Это поле должно быть заполнено';
$_lang['mspromocode_err_all_fields'] = 'Все поля должны быть заполнены';
$_lang['mspromocode_err_field_count'] = 'Поле "Количество" заполнено некорректно';
$_lang['mspromocode_err_ref_field_count'] = 'Не найдено пользователей без промо-кодов';
$_lang['mspromocode_err_ae'] = 'Это поле должно быть уникально';
$_lang['mspromocode_err_nf'] = 'Объект не найден';
$_lang['mspromocode_err_ns'] = 'Объект не указан';
$_lang['mspromocode_err_remove'] = 'Ошибка при удалении объекта.';
$_lang['mspromocode_err_save'] = 'Ошибка при сохранении объекта.';
$_lang['mspromocode_err_generate_only_to_action'] = 'Сгенерировать группу купонов можно исключительно для акции';
$_lang['mspromocode_err_coupon_conditions_are_not_met'] = 'Условия промо-кода не соблюдены';
$_lang['mspromocode_err_action_conditions_are_not_met'] = 'Условия акции не соблюдены';
$_lang['mspromocode_err_action_ref'] = 'Реферальная акция уже существует! Нельзя создавать несколько';

// Фронтенд
$_lang['mspromocode_promocode'] = 'Промо-код';
$_lang['mspromocode_discount_amount'] = 'Скидка';
$_lang['mspromocode_enter_promocode'] = 'Укажите промо-код';
$_lang['mspromocode_ok_code_apply'] = 'Промо-код применён';
$_lang['mspromocode_ok_code_remove'] = 'Промо-код отменён';
$_lang['mspromocode_err_code_invalid'] = 'Промо-код недействителен';
$_lang['mspromocode_err_code_notbound'] = 'Промо-код не был привязан';
$_lang['mspromocode_err_coupon_applied_before'] = 'У вас уже применён купон "[[+coupon]]"!';
$_lang['mspromocode_btn_apply'] = '+';
$_lang['mspromocode_btn_remove'] = '-';
