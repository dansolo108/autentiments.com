<?php
//
    $_lang = [
        'message_PageSpeed' => '[[+message]]',
        'message_PageSpeed_HTTP' => 'HTTP protocol error "[[+code]]" at "[[+url]]"',
        'message_PageSpeed_cache' => 'Caching error "[[+filename]]"',
        'message_PageSpeed_class' => 'Unable to create "[[+name]]" instance',
        'message_PageSpeed_critical' => 'Critical CSS is already set for configuration key "[[+key]]"',
        'message_PageSpeed_curl_error' => 'cURL error "[[+message]]" at "[[+url]]"',
        'message_PageSpeed_fonts' => 'Preload fonts are already set for configuration key "[[+key]]"',
        'message_PageSpeed_fopen' => 'Unable to open file "[[+filename]]"',
        'message_PageSpeed_integrity' => 'None of the hashes in the integrity attribute match the content of the subresource "[[+url]]"',
        'message_PageSpeed_is_dir' => 'Specified inode "[[+filename]]" is not a directory',
        'message_PageSpeed_json_last_error_msg' => 'Unable to decode JSON data "[[+message]]"',
        'message_PageSpeed_key' => 'Configuration key "[[+key]]" does not exist',
        'message_PageSpeed_libxml_get_errors' => 'libXML error "[[+message]]" at line "[[+line]]" column "[[+column]]"',
        'message_PageSpeed_method' => 'Calling "[[+name]]" failed',
        'message_PageSpeed_mkdir' => 'Unable to create directory "[[+directory]]"',
        'message_PageSpeed_modContentType' => 'Unable to retrieve modContentType instance with name "[[+name]]"',
        'message_PageSpeed_require_once' => 'Unable to open required file "[[+filename]]"',
        'message_PageSpeed_tag' => 'Unable to locate "[[+name]]" tag in HTML document',
        'message_PageSpeed_upload_files' => 'Extension "[[+extension]]" is restricted for uploading',
        'message_PageSpeed_url' => 'Processing page "[[+url]]" in mode "[[+mode]]"',
        'refresh_PageSpeed' => 'Clearing PageSpeed cache: ',
        'setting_PageSpeed_bundle' => 'bundle',
        'setting_PageSpeed_bundle_desc' => 'Optional. Default is <strong>link script</strong>. Determines types of content that will be bundled into one file. Case insensitive. Possible values are: <strong>link</strong>, <strong>script</strong>, any their combination or empty value. 
            <ul>
                <li>
                    <strong>link</strong> - CSS files.
                </li>
                <li>
                    <strong>script</strong> - JS files.
                </li>
            </ul>
        ',
        'setting_PageSpeed_convert' => 'convert',
        'setting_PageSpeed_convert_desc' => 'Optional. Default is <strong>static</strong>. Enables convertion of <strong>gif</strong>, <strong>jpg</strong> and <strong>png</strong> images to <strong>webp</strong> format with specified quality. Case insensitive. Possible values are: <strong>disable</strong>, <strong>dynamic</strong>, <strong>static</strong>.
            <ul>
                <li>
                    <strong>disable</strong> - images are not converted.
                </li>
                <li>
                    <strong>dynamic</strong> - images are not cached after convertsion. Requires additional CPU resources.
                </li>
                <li>
                    <strong>persistent</strong> - images are saved alongside original files after conversion and persist during cache clear. Requires additional free space.
                </li>
                <li>
                    <strong>static</strong> - images are cached after conversion. Requires additional free space.
                </li>
            </ul>
        ',
        'setting_PageSpeed_critical' => 'critical',
        'setting_PageSpeed_critical_desc' => 'Optional. Default is <strong>true</strong>. Enables critical path CSS generator. Value is interpreted as a <strong>boolean</strong>.',
        'setting_PageSpeed_crossorigin' => 'crossorigin',
        'setting_PageSpeed_crossorigin_desc' => 'Optional. Default is <strong>anonymous</strong>. <strong>Crossorigin</strong> attribute value for subresource. Case insensitive. Possible values are: <strong>anonymous</strong>, <strong>use-credentials</strong>, or empty value.',
        'setting_PageSpeed_display' => 'display',
        'setting_PageSpeed_display_desc' => 'Optional. Default is <strong>swap</strong>. <strong>font-display</strong> CSS property value. Case insensitive. Possible values are: <strong>auto</strong>, <strong>block</strong>, <strong>fallback</strong>, <strong>optional</strong>, <strong>swap</strong>.',
        'setting_PageSpeed_enable' => 'enable',
        'setting_PageSpeed_enable_desc' => 'Optional. Default is <strong>true</strong>. Enables extension. Value is interpreted as a <strong>boolean</strong>.',
        'setting_PageSpeed_integrity' => 'integrity',
        'setting_PageSpeed_integrity_desc' => 'Optional. Default is <strong>sha256</strong>. Algorithms to use for subresource integrity hashing. Case insensitive. Possible values are: <strong>sha256</strong>, <strong>sha384</strong>, <strong>sha512</strong>, any their combination or empty value.',
        'setting_PageSpeed_lifetime' => 'lifetime',
        'setting_PageSpeed_lifetime_desc' => 'Optional. Default is <strong>604800</strong>. Subresource cache lifetime.',
        'setting_PageSpeed_loading' => 'loading',
        'setting_PageSpeed_loading_desc' => 'Optional. Default is <strong>lazy</strong>. <strong>loading</strong> attribute value for <strong>img</strong> and <strong>iframe</strong> tags. Case insensitive. Possible values are: <strong>auto</strong>, <strong>eager</strong>, <strong>lazy</strong>.',
        'setting_PageSpeed_minify' => 'minify',
        'setting_PageSpeed_minify_desc' => 'Optional. Default is <strong>html link script</strong>. Determines types of content that will be minified. Case insensitive. Possible values are: <strong>css</strong>, <strong>html</strong>, <strong>js</strong>, <strong>json</strong>, <strong>link</strong>, <strong>script</strong>, any their combination or empty value.
            <ul>
                <li>
                    <strong>css</strong> - inline CSS.
                </li>
                <li>
                    <strong>html</strong> - HTML content.
                </li>
                <li>
                    <strong>js</strong> - inline JS.
                </li>
                <li>
                    <strong>json</strong> - inline JSON and JSON+LD microdata.
                </li>
                <li>
                    <strong>link</strong> - CSS files.
                </li>
                <li>
                    <strong>script</strong> - JS files.
                </li>
            </ul>
        ',
        'setting_PageSpeed_path' => 'path',
        'setting_PageSpeed_path_desc' => 'Optional. Default is <strong>{assets_path}PageSpeed/</strong>. Cache directory path. Can be cleared, if specified in <strong>System Settings</strong>, <strong>Context Settings</strong>, <strong>User Group Settings</strong> or <strong>User Settings</strong>.',
        'setting_PageSpeed_quality' => 'quality',
        'setting_PageSpeed_quality_desc' => 'Optional. Default is <strong>80</strong>. Quality of converted <strong>webp</strong> images. Possible values are integer from <strong>0</strong> to <strong>100</strong>.',
        'setting_PageSpeed_resize' => 'resize',
        'setting_PageSpeed_resize_desc' => 'Optional. Default is <strong>true</strong>. Enables resizing of images in <strong>img</strong> tags. Value is interpreted as a <strong>boolean</strong>.',
        'setting_PageSpeed_script' => 'script',
        'setting_PageSpeed_script_desc' => 'Optional. Default is <strong>defer</strong>. Attribute to be used for <strong>script</strong> tags. Case insensitive. Possible values are: <strong>async</strong>, <strong>defer</strong>, or empty value.',
        'setting_PageSpeed_subresources' => 'subresources',
        'setting_PageSpeed_subresources_desc' => 'Optional. Default is built by automatic mode. JSON object, containing information about subresources, their versions and files. Either subresource <strong>URL</strong> or <strong>name</strong> parameter for <strong>cdnjs.com</strong> API is required, while <strong>media</strong> attribute is optional. For <strong>cdnjs.com</strong> API all other properties are replaced by API defaults, if unspecified.',
        'setting_PageSpeed_url' => 'url',
        'setting_PageSpeed_url_desc' => 'Optional. Default is <strong>{url_scheme}{http_host}{assets_url}PageSpeed/</strong>. Cache directory URL.',
        'topmenu_PageSpeed' => 'PageSpeed',
        'topmenu_PageSpeed_desc' => 'Clear PageSpeed cache'
    ];
