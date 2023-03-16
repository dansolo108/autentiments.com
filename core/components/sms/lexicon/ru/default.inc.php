<?php

include 'settings.inc.php';

$_lang['sms_menu'] = 'SMS сообщения';
$_lang['sms_menu_desc'] = '';
$_lang['sms_menu_view'] = 'Просмотреть';
$_lang['sms_btn_close'] = 'Закрыть';
$_lang['sms_title'] = 'SMS сообщения';
$_lang['sms_pagetitle'] = 'SMS сообщения';
$_lang['sms_tab_send'] = 'SMS';
$_lang['sms_tab_send_intro'] = '';
$_lang['sms_send_field_id'] = 'ID';
$_lang['sms_send_field_sms_id'] = 'SMS ID';
$_lang['sms_send_field_user_id'] = 'Пользователь';
$_lang['sms_send_field_phone'] = 'Телефон';
$_lang['sms_send_field_message'] = 'Сообщение';
$_lang['sms_send_field_send'] = 'Отправлено';
$_lang['sms_send_field_status'] = 'Статус';
$_lang['sms_send_field_status_1'] = 'Не отправлено';
$_lang['sms_send_field_status_2'] = 'Отправлено';
$_lang['sms_send_field_status_3'] = 'Виртуальная отправка';
$_lang['sms_send_field_count'] = 'Кол-во дублей';
$_lang['sms_send_field_service'] = 'Сервис';
$_lang['sms_send_field_info'] = 'Информация';
$_lang['sms_send_field_properties'] = 'Полученные данные';
$_lang['sms_send_field_ip'] = 'IP';
$_lang['sms_send_field_client'] = 'Браузер';
$_lang['sms_send_field_createdon'] = 'Создано';

$_lang['sms_window_title_view'] = 'Просмотр';

$_lang['sms_filter_user'] = 'Пользователь';
$_lang['sms_filter_service'] = 'Сервис';
$_lang['sms_filter_query'] = 'IP, сообщение, телефон';

$_lang['sms_send_status_empty_message'] = 'Текcт сообщения не сгенерирован';
$_lang['sms_send_status_empty_phones'] = 'Телефон не указан';
$_lang['sms_send_status_empty_response'] = 'Ответ от сервиса не получен';
$_lang['sms_send_status_send_sms'] = 'Сообщение успешно отправлено';
$_lang['sms_send_status_send_sms_virtual'] = 'Виртуальная отправка';
$_lang['sms_send_status_code_dubl'] = 'Повторная отправка кода возможна только через [[+time]] минут(ы)';
$_lang['sms_code_invalid'] = 'Код подтверждения введен неправильно';
$_lang['sms_code_attempt'] = 'Лимит попыток повторного ввода кода исчерпан, необходимо получить новый код';
$_lang['sms_code_no_send'] = 'Код подтверждения не отправлен, либо истек его срок годности, запросите новый код подтверждения';

$_lang['sms_web_code_text'] = 'Ваш код:';
$_lang['sms_web_phone'] = 'Телефон';
$_lang['sms_web_code'] = 'Код';
$_lang['sms_web_btn_code_send'] = 'Получить код';
$_lang['sms_web_btn_code_check'] = 'Подтвердить код';