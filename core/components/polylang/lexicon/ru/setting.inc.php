<?php
/**
 * Setting Russian Lexicon Entries for Polylang
 *
 * @package polylang
 * @subpackage lexicon
 */
$_lang['area_polylang_main'] = 'Основные';
$_lang['area_polylang_mse2'] = 'mSearch2';
$_lang['area_polylang_translate'] = 'Перевод';
$_lang['area_polylang_editor'] = 'Редактор';
$_lang['area_polylang_msmulticurrency'] = 'msMultiCurrency';
$_lang['setting_polylang_working_templates'] = 'Активные шаблоны';
$_lang['setting_polylang_working_templates_desc'] = 'Список id шаблонов через запятую, для которых нужно активировать наборы.';
$_lang['setting_polylang_tools_handler_class'] = 'Класс Tools';
$_lang['setting_polylang_tools_handler_class_desc'] = '';
$_lang['setting_polylang_content_classes'] = 'Классы контента';
$_lang['setting_polylang_content_classes_desc'] = '';
$_lang['setting_polylang_input_types'] = 'Типы ввода';
$_lang['setting_polylang_input_types_desc'] = '';
$_lang['setting_polylang_skip_empty_value'] = 'Пропустить пустое значение перевода';
$_lang['setting_polylang_skip_empty_value_desc'] = 'Если выбрано "Да", то для полей, у которых не задан перевод, будет использовано оригинальное значение поля.';
$_lang['setting_polylang_default_site_url'] = 'URL сайта для языковой версии по умолчанию';
$_lang['setting_polylang_default_site_url_desc'] = 'Укажите URL сайта для языковой версии по умолчанию. Например http://mysite.ru/';
$_lang['setting_polylang_default_language'] = 'Ключ языка для языковой версии по умолчанию';
$_lang['setting_polylang_default_language_desc'] = '';
$_lang['setting_polylang_detect_visitor_language'] = 'Автоопределение языка посетителя';
$_lang['setting_polylang_detect_visitor_language_desc'] = 'При первом посещении сайта его языковая версия определяется на основании языка посетителя.';
$_lang['setting_polylang_force_language'] = 'Принудительно установить язык';
$_lang['setting_polylang_force_language_desc'] = 'Укажите код (ISO) языка на котором следует отобразить сайт при первом его посещении. Если включена опция "Автоопределение языка посетителя" то она  будет проигнорирована.';
$_lang['setting_polylang_visitor_default_language'] = 'Ключ языка посетителя по умолчанию';
$_lang['setting_polylang_visitor_default_language_desc'] = 'Если языка посетителя нет в списке локализаций, то будет использован указанный.';
$_lang['setting_polylang_mse2_index'] = 'Индексировать поля в mSearch2';
$_lang['setting_polylang_mse2_index_desc'] = 'Перевод для полей указанных опций mse2_index_fields будет добавлен в интекс mSearch2';
$_lang['setting_polylang_translate_yandex_key'] = 'API ключ для Яндекс переводчика';
$_lang['setting_polylang_translate_yandex_key_desc'] = '';
$_lang['setting_polylang_translate_google_key'] = 'API ключ для Google переводчика';
$_lang['setting_polylang_translate_google_key_desc'] = '';
$_lang['setting_polylang_translate_promt_config'] = 'Настройки API для PROMT переводчика';
$_lang['setting_polylang_translate_promt_config_desc'] = '';
$_lang['setting_polylang_translate_data_source_language'] = 'Язык источника данных';
$_lang['setting_polylang_translate_data_source_language_desc'] = 'При необходимости можно указать язык лексикона из которого следует брать текст для перевода. Например, что бы на немецкий текст брался из английского лексикона, а для польского из русского то следует указать: {"de":"en","pl":"ru"}';
$_lang['setting_polylang_disallow_translation_completed_field'] = 'Запретить перевод заполненного поля';
$_lang['setting_polylang_disallow_translation_completed_field_desc'] = 'Если выбрано "Да" и поле уже содержит текст то его перевод из админки будет проигнорирован.';
$_lang['setting_polylang_class_translator'] = 'Класс переводчика';
$_lang['setting_polylang_class_translator_desc'] = 'Доступные значения: PolylangTranslatorGoogle; PolylangTranslatorYandex; PolylangTranslatorPromt.';
$_lang['setting_polylang_show_translate_btn'] = 'Показывать кнопку "Перевести"';
$_lang['setting_polylang_show_translate_btn_desc'] = 'Если выбрано "Да", то у полей всегда будет отображаться кнопка "Перевести", а не только при наведении на поле.';
$_lang['setting_polylang_reload_lexicon'] = 'Перезагрузить лексиконы';
$_lang['setting_polylang_reload_lexicon_desc'] = 'Укажите через запятую лексиконы которые следует перезагружать при смене языка. Пример: minishop2:product,minishop2:cart';
$_lang['setting_polylang_debug'] = 'Отладка';
$_lang['setting_polylang_debug_desc'] = '';
$_lang['setting_polylang_editor_height'] = 'Высота редактора';
$_lang['setting_polylang_editor_height_desc'] = '';
$_lang['setting_polylang_use_code_editor'] = 'Редактор кода';
$_lang['setting_polylang_use_code_editor_desc'] = 'Нужно ли использовать редактор кода при  отключенном HTML-редакторе.';
$_lang['setting_polylang_use_resource_editor_status'] = 'Использовать статус HTML-редактора ресурса';
$_lang['setting_polylang_use_resource_editor_status_desc'] = 'Если выбрано "Да" то будет ли включен HTML-редактор у локализации будет зависеть от того включен ли он у самого ресурса.';
$_lang['setting_polylang_post_processing_translation'] = 'Пост обработка перевода';
$_lang['setting_polylang_post_processing_translation_desc'] = 'Включение опции позволяет удалить лишних пробелов в тегах modx которые появляются при переводе.';
$_lang['setting_polylang_default_language_group'] = 'Группа языков по умолчанию';
$_lang['setting_polylang_default_language_group_desc'] = 'Значение по умолчанию параметра "группа языков" для сниппетов. Этот значение будет используется если явно не указано значение при вызове сниппета.';
$_lang['setting_polylang_set_currency_for_language'] = 'Устанавливать валюту по умолчанию';
$_lang['setting_polylang_set_currency_for_language_desc'] = 'Позволяет установить валюту по умолчанию для msMultiCurrency на основании заданной в языке.';