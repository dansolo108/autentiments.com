<?php
//
    $_lang = [
        'message_PageSpeed' => '[[+message]]',
        'message_PageSpeed_HTTP' => 'Помилка "[[+code]]" протоколу HTTP "[[+url]]"',
        'message_PageSpeed_cache' => 'Полмилка кешу "[[+filename]]"',
        'message_PageSpeed_class' => 'Неможливо створити екземпляр "[[+name]]"',
        'message_PageSpeed_critical' => 'Критичні стилі вже встановлені для ключа конфігурації "[[+key]]"',
        'message_PageSpeed_curl_error' => 'Помилка "[[+message]]" cURL "[[+url]]"',
        'message_PageSpeed_fonts' => 'Шрифти для попереднього завантаження вже встановлені для ключа конфігурації "[[+key]]"',
        'message_PageSpeed_fopen' => 'Неможливо відкрити файл "[[+filename]]"',
        'message_PageSpeed_integrity' => 'Жоден з хешів в атрибуті integrity не відповідає вмісту підресурса "[[+url]]"',
        'message_PageSpeed_is_dir' => 'Вказаний шлях "[[+filename]]" не є директорією',
        'message_PageSpeed_json_last_error_msg' => 'Неможливо декодувати дані JSON "[[+message]]"',
        'message_PageSpeed_key' => 'Ключ конфігурації "[[+key]]" не існує',
        'message_PageSpeed_libxml_get_errors' => 'Помилка "[[+message]]" libXML у рядку "[[+line]]" стовпець "[[+column]]"',
        'message_PageSpeed_method' => 'Виклик "[[+name]]" викликав помилку',
        'message_PageSpeed_mkdir' => 'Неможливо створити директорію "[[+directory]]"',
        'message_PageSpeed_modContentType' => 'Неможливо отримати екземпляр modContentType з іменем "[[+name]]"',
        'message_PageSpeed_require_once' => 'Неможливо відкрити необхідний файл "[[+filename]]"',
        'message_PageSpeed_tag' => 'Неможливо знайти тег "[[+name]]" у HTML документі',
        'message_PageSpeed_upload_files' => 'Розширення "[[+extension]]" обмежено для завантаження',
        'message_PageSpeed_url' => 'Обробка сторінки "[[+url]]" в режимі "[[+mode]]"',
        'refresh_PageSpeed' => 'Очищення кешу PageSpeed: ',
        'setting_PageSpeed_bundle' => 'bundle',
        'setting_PageSpeed_bundle_desc' => 'Не обов\'язковий. За замовчуванням <strong>link script</strong>. Визначає типи контенту, що будуть зв\'язані в один файл. Не чутливий до регістру. Можливі значення: <strong>link</strong>, <strong>script</strong>, будь-яка їх комбінація або порожнє значення. 
            <ul>
                <li>
                    <strong>link</strong> - CSS файли.
                </li>
                <li>
                    <strong>script</strong> - JS файли.
                </li>
            </ul>
        ',
        'setting_PageSpeed_convert' => 'convert',
        'setting_PageSpeed_convert_desc' => 'Не обов\'язковий. За замовчуванням <strong>static</strong>. Відповідає за конвертування <strong>gif</strong>, <strong>jpg</strong> та <strong>png</strong> зображень у формат <strong>webp</strong> з вказаною якістю. Не чутливий до регістру. Можливі значення: <strong>disable</strong>, <strong>dynamic</strong>, <strong>static</strong>.
            <ul>
                <li>
                    <strong>disable</strong> - зображення не конвертуються.
                </li>
                <li>
                    <strong>dynamic</strong> - зображення не зберігаються у кеш після конвертації. Потребує більше ресурсів CPU.
                </li>
                <li>
                    <strong>persistent</strong> - зображення зберігаються поряд оригінальними файлами та не видаляються під час очищення кешу. Потребує більше вільного місця.
                </li>
                <li>
                    <strong>static</strong> - зображення зберігаються у кеш після конвертації. Потребує більше вільного місця.
                </li>
            </ul>
        ',
        'setting_PageSpeed_critical' => 'critical',
        'setting_PageSpeed_critical_desc' => 'Не обов\'язковий. За замовчуванням <strong>true</strong>. Відповідає за генератор критичних стилів. Інтерпретується як <strong>boolean</strong>.',
        'setting_PageSpeed_crossorigin' => 'crossorigin',
        'setting_PageSpeed_crossorigin_desc' => 'Не обов\'язковий. За замовчуванням <strong>anonymous</strong>. Значення атрибуту <strong>сrossorigin</strong> для усіх ресурсів. Не чутливий до регістру. Можливі значення: <strong>anonymous</strong>, <strong>use-credentials</strong>, або порожнє значення.',
        'setting_PageSpeed_display' => 'display',
        'setting_PageSpeed_display_desc' => 'Не обов\'язковий. За замовчуванням <strong>swap</strong>. Значення CSS властивості <strong>font-display</strong>. Не чутливий до регістру. Можливі значення: <strong>auto</strong>, <strong>block</strong>, <strong>fallback</strong>, <strong>optional</strong>, <strong>swap</strong>.',
        'setting_PageSpeed_enable' => 'enable',
        'setting_PageSpeed_enable_desc' => 'Не обов\'язковий. За замовчуванням <strong>true</strong>. Відповідає за роботу додатку. Інтерпретується як <strong>boolean</strong>.',
        'setting_PageSpeed_integrity' => 'integrity',
        'setting_PageSpeed_integrity_desc' => 'Не обов\'язковий. За замовчуванням <strong>sha256</strong>. Алгоритм, що буде використано для обчислення хешу контролю цілісності ресурсів. Не чутливий до регістру. Можливі значення: <strong>sha256</strong>, <strong>sha384</strong>, <strong>sha512</strong>, будь-яка їх комбінація або порожнє значення.',
        'setting_PageSpeed_lifetime' => 'lifetime',
        'setting_PageSpeed_lifetime_desc' => 'Не обов\'язковий. За замовчуванням <strong>604800</strong>. Термін дії кешу ресурсів.',
        'setting_PageSpeed_loading' => 'loading',
        'setting_PageSpeed_loading_desc' => 'Не обов\'язковий. За замовчуванням  <strong>lazy</strong>. Значення атрибуту <strong>loading</strong> для тегів <strong>img</strong> та <strong>iframe</strong>. Не чутливий до регістру. Можливі значення: <strong>auto</strong>, <strong>eager</strong>, <strong>lazy</strong>.',
        'setting_PageSpeed_minify' => 'minify',
        'setting_PageSpeed_minify_desc' => 'Не обов\'язковий. За замовчуванням <strong>html link script</strong>. Визначає типи контенту, що будуть мініфіковані. Не чутливий до регістру. Можливі значення: <strong>css</strong>, <strong>html</strong>, <strong>js</strong>, <strong>json</strong>, <strong>link</strong>, <strong>script</strong>, будь-яка їх комбінація або порожнє значення.
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
                    <strong>json</strong> - inline JSON та JSON+LD мікродані.
                </li>
                <li>
                    <strong>link</strong> - CSS файли.
                </li>
                <li>
                    <strong>script</strong> - JS файли.
                </li>
            </ul>
        ',
        'setting_PageSpeed_path' => 'path',
        'setting_PageSpeed_path_desc' => 'Не обов\'язковий. За замовчуванням  <strong>{assets_path}PageSpeed/</strong>. Шлях директорії кешу. Може бути очищений, якщо вказаний у <strong>Системних параметрах</strong>, <strong>Налаштуваннях контексту</strong>, <strong>Налаштуваннях групи користувачів</strong> або у <strong>Налаштуваннях користувача</strong>.',
        'setting_PageSpeed_quality' => 'quality',
        'setting_PageSpeed_quality_desc' => 'Не обов\'язковий. За замовчуванням <strong>80</strong>. Якість відконвертованих <strong>webp</strong> зображень. Можливі значення: цілі числа від <strong>0</strong> до <strong>100</strong>.',
        'setting_PageSpeed_resize' => 'resize',
        'setting_PageSpeed_resize_desc' => 'Не обов\'язковий. За замовчуванням <strong>true</strong>. Відповідає за зміну розміру зображень у тегах <strong>img</strong>. Інтерпретується як <strong>boolean</strong>.',
        'setting_PageSpeed_script' => 'script',
        'setting_PageSpeed_script_desc' => 'Не обов\'язковий. За замовчуванням <strong>defer</strong>. Атрибут, що буде використано для тегів <strong>script</strong>. Не чутливий до регістру. Можливі значення: <strong>async</strong>, <strong>defer</strong>, або порожнє значення.',
        'setting_PageSpeed_subresources' => 'subresources',
        'setting_PageSpeed_subresources_desc' => 'Не обов\'язковий. За замовчуванням створюється автоматично. JSON-об\'єкт, що містить інформацію про ресурсі, їх версії та файли. Або <strong>URL</strong> або параметр <strong>name</strong> для <strong>cdnjs.com</strong> API є обов\'язковим, в той час як значення атрибуту <strong>media</strong> можна не вказувати. Для <strong>cdnjs.com</strong> API інші властивості заміняються відповідними за замовчуванням з API, якщо не вказані.',
        'setting_PageSpeed_url' => 'url',
        'setting_PageSpeed_url_desc' => 'Не обов\'язковий. За замовчуванням <strong>{url_scheme}{http_host}{assets_url}PageSpeed/</strong>. URL директорії кешу.',
        'topmenu_PageSpeed' => 'PageSpeed',
        'topmenu_PageSpeed_desc' => 'Очистити кеш PageSpeed'
    ];
