<?php
//
    $_lang = [
        'message_PageSpeed' => '[[+message]]',
        'message_PageSpeed_HTTP' => 'Ошибка "[[+code]]" протокола HTTP "[[+url]]"',
        'message_PageSpeed_cache' => 'Ошибка кеширования "[[+filename]]"',
        'message_PageSpeed_class' => 'Невозможно создать экземпляр "[[+name]]"',
        'message_PageSpeed_critical' => 'Критические стили уже установлены для ключа конфигурации "[[+key]]"',
        'message_PageSpeed_curl_error' => 'Ошибка "[[+message]]" cURL "[[+url]]"',
        'message_PageSpeed_fonts' => 'Шрифты для предварительной загрузки уже установлены для ключа конфигурации "[[+key]]"',
        'message_PageSpeed_fopen' => 'Невозможно открыть файл "[[+filename]]"',
        'message_PageSpeed_integrity' => 'Ни один из хэшей в атрибуте integrity не соответствует содержимому подресурса "[[+url]]"',
        'message_PageSpeed_is_dir' => 'Указанный путь "[[+filename]]" не является директорией',
        'message_PageSpeed_json_last_error_msg' => 'Невозможно декодировать данные JSON "[[+message]]"',
        'message_PageSpeed_key' => 'Ключ конфигурации "[[+key]]" не существует',
        'message_PageSpeed_libxml_get_errors' => 'Ошибка "[[+message]]" libXML на строке "[[+line]]" столбец "[[+column]]"',
        'message_PageSpeed_method' => 'Вызов "[[+name]]" вызвал ошибку',
        'message_PageSpeed_mkdir' => 'Невозможно создать директорию "[[+directory]]"',
        'message_PageSpeed_modContentType' => 'Невозможно получить экземпляр modContentType с именем "[[+name]]"',
        'message_PageSpeed_require_once' => 'Невозможно открыть необходимый файл "[[+filename]]"',
        'message_PageSpeed_tag' => 'Невозможно найти тег "[[+name]]" в HTML документе',
        'message_PageSpeed_upload_files' => 'Расширение "[[+extension]]" ограничено для загрузки',
        'message_PageSpeed_url' => 'Обработка страницы "[[+url]]" в режиме "[[+mode]]"',
        'refresh_PageSpeed' => 'Очистка кэша PageSpeed: ',
        'setting_PageSpeed_bundle' => 'bundle',
        'setting_PageSpeed_bundle_desc' => 'Не обязательный. По-умолчанию <strong>link script</strong>. Определяет типы контента, которые будут связаны в один файл. Не чувствителен к регистру. Возможные значения: <strong>link</strong>, <strong>script</strong>, любая их комбинация или пустое значение.
            <ul>
                <li>
                    <strong>link</strong> - CSS файлы.
                </li>
                <li>
                    <strong>script</strong> - JS файлы.
                </li>
            </ul>
        ',
        'setting_PageSpeed_convert' => 'convert',
        'setting_PageSpeed_convert_desc' => 'Не обязательный. По-умолчанию <strong>static</strong>. Отвечает за конвертирование <strong>gif</strong>, <strong>jpg</strong> и <strong>png</strong> изображений в формат <strong>webp</strong> с указанным качеством. Не чувствителен к регистру. Возможные значения: <strong>disable</strong>, <strong>dynamic</strong>, <strong>static</strong>.
            <ul>
                <li>
                    <strong>disable</strong> - изображения не конвертируются.
                </li>
                <li>
                    <strong>dynamic</strong> - изображения не сохраняются после конвертации. Потребляет больше ресурсов CPU.
                </li>
                <li>
                    <strong>persistent</strong> - изображения сохраняются рядом с оригинальными файлами и не удаляются во время очистки кеша. Потребляет больше свободного места.
                </li>
                <li>
                    <strong>static</strong> - изображения сохраняются после конвертации. Потребляет больше свободного места.
                </li>
            </ul>
        ',
        'setting_PageSpeed_critical' => 'critical',
        'setting_PageSpeed_critical_desc' => 'Не обязательный. По-умолчанию <strong>true</strong>. Отвечает за генератор критических стилей. Интерпретируется как <strong>boolean</strong>.',
        'setting_PageSpeed_crossorigin' => 'crossorigin',
        'setting_PageSpeed_crossorigin_desc' => 'Не обязательный. По-умолчанию <strong>anonymous</strong>. Значения аттрибута <strong>crossorigin</strong> для всех ресурсов. Не чувствителен к регистру. Возможные значения: <strong>anonymous</strong>, <strong>use-credentials</strong>, или пустое значение.',
        'setting_PageSpeed_display' => 'display',
        'setting_PageSpeed_display_desc' => 'Не обязательный. По-умолчанию  <strong>swap</strong>. Значение CSS свойства <strong>font-display</strong>. Не чувствителен к регистру. Возможные значения: <strong>auto</strong>, <strong>block</strong>, <strong>fallback</strong>, <strong>optional</strong>, <strong>swap</strong>.',
        'setting_PageSpeed_enable' => 'enable',
        'setting_PageSpeed_enable_desc' => 'Не обязательный. По-умолчанию <strong>true</strong>. Отвечает за работу дополнения. Интерпретируется как <strong>boolean</strong>.',
        'setting_PageSpeed_integrity' => 'integrity',
        'setting_PageSpeed_integrity_desc' => 'Не обязательный. По-умолчанию <strong>sha256</strong>. Алгоритм, который будет использоваться для вычисления хеша контроля целостности ресурсов. Не чувствителен к регистру. Возможные значения: <strong>sha256</strong>, <strong>sha384</strong>, <strong>sha512</strong>, любая их комбинация или пустое значение.',
        'setting_PageSpeed_lifetime' => 'lifetime',
        'setting_PageSpeed_lifetime_desc' => 'Не обязательный. По-умолчанию <strong>604800</strong>. Срок действия кэша ресурсов.',
        'setting_PageSpeed_loading' => 'loading',
        'setting_PageSpeed_loading_desc' => 'Не обязательный. По-умолчанию <strong>lazy</strong>. Значения аттрибута <strong>loading</strong> для тегов <strong>img</strong> и <strong>iframe</strong>. Не чувствителен к регистру. Возможные значения: <strong>auto</strong>, <strong>eager</strong>, <strong>lazy</strong>.',
        'setting_PageSpeed_minify' => 'minify',
        'setting_PageSpeed_minify_desc' => 'Не обязательный. По-умолчанию <strong>html link script</strong>. Определяет типы контента, которые будут минифицированы. Не чувствителен к регистру. Возможные значения: <strong>css</strong>, <strong>html</strong>, <strong>js</strong>, <strong>json</strong>, <strong>link</strong>, <strong>script</strong>, любая их комбинация или пустое значение.
            <ul>
                <li>
                    <strong>css</strong> - inline CSS.
                </li>
                <li>
                    <strong>html</strong> - HTML контент.
                </li>
                <li>
                    <strong>js</strong> - inline JS.
                </li>
                <li>
                    <strong>json</strong> - inline JSON и JSON+LD микроданные.
                </li>
                <li>
                    <strong>link</strong> - CSS файлы.
                </li>
                <li>
                    <strong>script</strong> - JS файлы.
                </li>
            </ul>
        ',
        'setting_PageSpeed_path' => 'path',
        'setting_PageSpeed_path_desc' => 'Не обязательный. По-умолчанию <strong>{assets_path}PageSpeed/</strong>. Путь директории кеша. Может быть очищен, если указан в <strong>Настройках системы</strong>, <strong>Настройках контекста</strong>, <strong>Настройках группы пользователей</strong> или в <strong>Настройках пользователя</strong>.',
        'setting_PageSpeed_quality' => 'quality',
        'setting_PageSpeed_quality_desc' => 'Не обязательный. По-умолчанию <strong>80</strong>. Качество отконвертированных <strong>webp</strong> изображений. Возможные значения: целые числа от <strong>0</strong> до <strong>100</strong>.',
        'setting_PageSpeed_resize' => 'resize',
        'setting_PageSpeed_resize_desc' => 'Не обязательный. По-умолчанию <strong>true</strong>. Отвечает за изменение размера изображений в тегах <strong>img</strong>. Интерпретируется как <strong>boolean</strong>.',
        'setting_PageSpeed_script' => 'script',
        'setting_PageSpeed_script_desc' => 'Не обязательный. По-умолчанию <strong>defer</strong>. Аттрибут, который будет использован для тегов <strong>script</strong>. Не чувствителен к регистру. Возможные значения: <strong>async</strong>, <strong>defer</strong>, или пустое значение.',
        'setting_PageSpeed_subresources' => 'subresources',
        'setting_PageSpeed_subresources_desc' => 'Не обязательный. По-умолчанию создаётся автоматически. JSON-объект, который содержит информацию про ресурсы, их версии и файлы. Либо <strong>URL</strong> либо свойство <strong>name</strong> для <strong>cdnjs.com</strong> API является обязательным, в то время как остальные свойства заменяются соответствующими по-умолчанию из API, если не указаны.',
        'setting_PageSpeed_url' => 'url',
        'setting_PageSpeed_url_desc' => 'Не обязательный. По-умолчанию <strong>{url_scheme}{http_host}{assets_url}PageSpeed/</strong>. URL директории кеша.',
        'topmenu_PageSpeed' => 'PageSpeed',
        'topmenu_PageSpeed_desc' => 'Очистить кэш PageSpeed'
    ];
