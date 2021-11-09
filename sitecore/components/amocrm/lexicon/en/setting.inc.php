<?php

$_lang['area_amocrm_main'] = 'Основные';
$_lang['area_amocrm_fields'] = 'Поля';
$_lang['area_amocrm_pipelines'] = 'Воронки и статусы';
$_lang['area_amocrm_token'] = 'Токен авторизации';

$_lang['setting_amocrm_domain'] = 'Домен для подключения';
$_lang['setting_amocrm_domain_desc'] = '';
$_lang['setting_amocrm_protocol'] = 'Протокол для подключения';
$_lang['setting_amocrm_protocol_desc'] = '';
$_lang['setting_amocrm_account'] = 'Аккаунт';
$_lang['setting_amocrm_account_desc'] = '';
$_lang['setting_amocrm_client_id'] = 'ID интеграции';
$_lang['setting_amocrm_client_id_desc'] = 'Получаем в личном кабинете';

$_lang['setting_amocrm_client_secret'] = 'Секрет интеграции';
$_lang['setting_amocrm_client_secret_desc'] = 'Получаем в личном кабинете';

$_lang['setting_amocrm_token_field'] = 'Хранилище токена';
$_lang['setting_amocrm_token_field_desc'] = 'Служебный параметр. Заполняется автоматически';

$_lang['setting_amocrm_client_code'] = 'Код авторизации';
$_lang['setting_amocrm_client_code_desc'] = 'Получаем в личном кабинете.  Действует только 20 минут';
$_lang['setting_amocrm_use_simple_queue'] = 'Использовать очереди SimpleQueue';
$_lang['setting_amocrm_use_simple_queue_desc'] = 'Если включено, а компонент simpleQueue установлен, вместо прямой отправки данных в amoCRM будут создаваться задания на отправку данных';
$_lang['setting_amocrm_new_order_status_id'] = 'ID статуса нового заказа';
$_lang['setting_amocrm_new_order_status_id_desc'] = 'По умолчанию 1';
$_lang['setting_amocrm_pipeline_id'] = 'ID воронки для нового заказа';
$_lang['setting_amocrm_pipeline_id_desc'] = '';
$_lang['setting_amocrm_form_pipeline_id'] = 'ID воронки для новой заяки';
$_lang['setting_amocrm_form_pipeline_id_desc'] = '';
$_lang['setting_amocrm_form_as_lead'] = 'Заявки из форм как сделки (лиды)';
$_lang['setting_amocrm_form_as_lead_desc'] = '';
$_lang['setting_amocrm_form_status_new'] = 'ID статуса amoCRM для новой заявки как сделки';
$_lang['setting_amocrm_form_status_new_desc'] = '';
$_lang['setting_amocrm_secret_key'] = 'Секретный ключ виджета';
$_lang['setting_amocrm_secret_key_desc'] = 'Получается при добавлении нового виджета';
$_lang['setting_amocrm_order_fields'] = 'Список полей заказа';
$_lang['setting_amocrm_order_fields_desc'] = 'Список полей заказа через запятую, которые будут передаваться при создании сделки';
$_lang['setting_amocrm_order_address_fields'] = 'Список полей адреса заказа';
$_lang['setting_amocrm_order_address_fields_desc'] = 'Список полей адреса заказа через запятую, которые будут передаваться при создании сделки';
$_lang['setting_amocrm_order_address_fields_prefix'] = 'Префикс для полей адреса заказа';
$_lang['setting_amocrm_order_address_fields_prefix_desc'] = 'Префикс, добавляемый в начало полей адреса заказа при создании сделки.
<br>По умолчанию: "address."';
$_lang['setting_amocrm_user_fields'] = 'Список полей пользователя';
$_lang['setting_amocrm_user_fields_desc'] = 'Список полей пользователя через запятую, которые будут передаваться при создании контакта';
$_lang['setting_amocrm_spam_modx_log'] = 'Отправлять логи в системный лог MDOX';
$_lang['setting_amocrm_spam_modx_log_desc'] = 'Если включено, логи работы компонента будут отправляться в системный лог MODX на уровне ERROR. Может быть полезно при отладке';
$_lang['setting_amocrm_auto_create_orders_fields'] = 'Создавать автоматически поля сделок';
$_lang['setting_amocrm_auto_create_orders_fields_desc'] = 'Если включено, отсутствующие в amoCRM поля сделок будут созданы автоматически. По умолчаниб выключено.';
$_lang['setting_amocrm_auto_create_users_fields'] = 'Создавать автоматически поля контактов';
$_lang['setting_amocrm_auto_create_users_fields_desc'] = 'Если включено, отсутствующие в amoCRM поля контактов будут созданы автоматически. По умолчаниб выключено.';
$_lang['setting_amocrm_skip_empty_fields'] = 'Пропускать поля без значения';
$_lang['setting_amocrm_skip_empty_users_fields_desc'] = 'Если включено, поля с пустыми значениями не будут передаваться в amoCRM. По умолчаниб включено.';
$_lang['setting_amocrm_default_responsible_user_id'] = 'ID ответственного по умолчанию';
$_lang['setting_amocrm_default_responsible_user_id_desc'] = 'Если указано, при создании контактов и сделок в поле ответственного по умолчанию будет подставляться данный ID.';
$_lang['setting_amocrm_update_order_on_change_status'] = 'Обновлять все поля заказа в amoCRM при изменении статуса на сайте';
$_lang['setting_amocrm_update_order_on_change_status_desc'] = 'Если включено, при изменении статуса заказа на сайте в amoCRM будут отправлены все поля заказа заново. Используется, если на сайте параметры заказа могут быть изменены. Если выключено, в amoCRM отправится запрос только на смену статуса сделки. По умолчанию выключено.';
$_lang['setting_amocrm_categories_pipelines'] = 'Настройки воронки и ответственного для категорий товаров';
$_lang['setting_amocrm_categories_pipelines_desc'] = 'Массив в JSON формате для указания отдельных воронки, статуса и ответственного для категорий товаров.<br>
Указание ответственного (<b>responsible_user_id</b>) необязательно.<br>
Пример: {"23":{"pipeline_id":1480825,"status_id":22834243,"responsible_user_id":2956360}}
По умолчанию пустой массив: {}
<br><br>
<b>ВАЖНО: </b> поиск идет до первого совпадения. 
';
$_lang['setting_amocrm_order_properties_element'] = 'Название элемента в свойствах заказа';
$_lang['setting_amocrm_order_properties_element_desc'] = 'Название массива в properties заказа, поля которого будут приравнены к полям заказа.<br>
Позволяет с помощью внешнего плагина в properties заказа добавить неограниченное количество полей, которые будут переданы в amoCRM.<br>
Поля должны быть перечислены в настройке <b>amocrm_order_fields</b>';
$_lang['setting_amocrm_responsible_id_priority_category'] = 'Заменять ответственного в заказе указанным для категории';
$_lang['setting_amocrm_responsible_id_priority_category_desc'] = 'Если включено, сохраненный в заказе ответственный будет заменен ответственным, найденным для категории одного из товаров в заказе';
$_lang['setting_amocrm_save_user_in_mgr'] = 'Отправлять пользователя при редактировании в админке';
$_lang['setting_amocrm_save_user_in_mgr_desc'] = 'Если включено, при сохранении пользователя в админке данные будут переданы в amoCRM. По умолчанию выключено';
$_lang['setting_amocrm_save_user_by_profile'] = 'Отправлять пользователя при сохранении отдельно профиля';
$_lang['setting_amocrm_save_user_by_profile_desc'] = 'Если включено, при сохранении профиля пользователя через <b>$profile->save()</b> данные будут переданы в amoCRM. По умолчанию выключено';
$_lang['setting_amocrm_form_filled_fields'] = 'Список обязательных полей формы';
$_lang['setting_amocrm_form_filled_fields_desc'] = 'Список полей через запятую, обязательных для заполнения при передаче формы в amoCRM<br>
По умолчанию: <i>пустая строка</i>';
$_lang['setting_amocrm_auto_update_pipelines'] = 'Автоматически обновлять воронки и статусы';
$_lang['setting_amocrm_auto_update_pipelines_desc'] = 'Если включено, при проверке ID статусов в amoCRM будут обновляться воронки.<br>Если не используется несколько воронок, достаточно только на непродолжительное время включить настройку.';
$_lang['setting_amocrm_user_enum_fields'] = 'Список ENUM полей контактов и их типы';
$_lang['setting_amocrm_user_enum_fields_desc'] = 'JSON массив со списком полей, которым необходимо добавлять тип ENUM.
<br>Ключи массива - названия полей в нижнем регистре или их ID, значения - типы ENUM
<br>По умолчанию: {"phone":"WORK","email":"WORK","телефон":"WORK"}';
$_lang['setting_amocrm_user_readonly_fields'] = 'Список полей контактов, значения которых в amoCRM не изменяются';
$_lang['setting_amocrm_user_readonly_fields_desc'] = 'Список полей через запятую, значения которых в amoCRM не изменяются при обновлении контакта.
<br>По умолчанию: name';
$_lang['setting_amocrm_user_fields_glue_amo_values'] = 'Строка для соединения множественных значений полей контактов при сохранении пользователя';
$_lang['setting_amocrm_user_fields_glue_amo_values_desc'] = 'Если значение - пустая строка, при получении контакта из amoCRM webhook\'ом, сохранено будет только первое значение. Если в настройке указана какая-либо строк, она будет "соединителем" при сохранении всех значений из amoCRM
<br>По умолчанию: [пустая строка]';
