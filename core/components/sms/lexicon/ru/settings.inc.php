<?php

$_lang['area_sms_main'] = 'Основное';

$_lang['setting_sms_login'] = 'Логин';
$_lang['setting_sms_login_desc'] = '';
$_lang['setting_sms_pass'] = 'Пароль';
$_lang['setting_sms_pass_desc'] = '';
$_lang['setting_sms_service'] = 'Сервис';
$_lang['setting_sms_service_desc'] = '';
$_lang['setting_sms_sender'] = 'Отправитель';
$_lang['setting_sms_sender_desc'] = '';
$_lang['setting_sms_charset'] = 'Кодировка отправляемого текста';
$_lang['setting_sms_charset_desc'] = '';
$_lang['setting_sms_code_length'] = 'Длина генерируемого кода подтверждения';
$_lang['setting_sms_code_length_desc'] = '';
$_lang['setting_sms_code_mode'] = 'Режим генерации кода';
$_lang['setting_sms_code_mode_desc'] = '0 - только цифры, 1 - только буквы, 2 - буквы и цифры';
$_lang['setting_sms_code_message_tpl'] = 'Чанк смс сообщения по умолчанию';
$_lang['setting_sms_code_message_tpl_desc'] = 'Название чанка с контентом для сообщения, которое используется по умолчанию при отправке кода подтверждения';
$_lang['setting_sms_code_life'] = 'Время жизни кода подтверждения (минут)';
$_lang['setting_sms_code_life_desc'] = '';
$_lang['setting_sms_code_attempt'] = 'Кол-во попыток повторного ввода кода';
$_lang['setting_sms_code_attempt_desc'] = '';
$_lang['setting_sms_extra'] = 'Доп. настройки для сервиса';
$_lang['setting_sms_extra_desc'] = 'передаются в виде JSON данных: {"ключ1":"значение1","ключ2":"значение2",...}';
$_lang['setting_sms_send_fields'] = 'Поля для вывода в таблице отправляемых смс';
$_lang['setting_sms_send_fields_desc'] = 'Поля для вывода в таблице отправляемых смс';
$_lang['setting_sms_sandbox'] = 'Режим тестирования';
$_lang['setting_sms_sandbox_desc'] = 'позволяет включить режим тестирования и не отправлять реальные сообщения в установленный сервис смс сообщений';