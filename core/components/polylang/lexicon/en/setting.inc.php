<?php
/**
 * Setting English Lexicon Entries for Polylang
 *
 * @package polylang
 * @subpackage lexicon
 */
$_lang['area_polylang_main'] = 'Main';
$_lang['area_polylang_mse2'] = 'mSearch2';
$_lang['area_polylang_translate'] = 'Translate';
$_lang['area_polylang_editor'] = 'Editor';
$_lang['setting_polylang_working_templates'] = 'Active Templates';
$_lang['setting_polylang_working_templates_desc'] = 'A list of id patterns, separated by commas, for which you want to activate sets.';
$_lang['setting_polylang_tools_handler_class'] = 'Class Tools';
$_lang['setting_polylang_tools_handler_class_desc'] = '';
$_lang['setting_polylang_content_classes'] = 'Content Classes';
$_lang['setting_polylang_content_classes_desc'] = '';
$_lang['setting_polylang_input_types'] = 'Input types';
$_lang['setting_polylang_input_types_desc'] = '';
$_lang['setting_polylang_skip_empty_value'] = 'Skip empty translation value';
$_lang['setting_polylang_skip_empty_value_desc'] = 'If "Yes" is selected, then for fields for which a translation is not specified, the original field knowledge will be used.';
$_lang['setting_polylang_default_site_url'] = 'Website URL for the default language version';
$_lang['setting_polylang_default_site_url_desc'] = 'Specify the site URL for the default language version. For example http://mysite.ru/';
$_lang['setting_polylang_default_language'] = 'Language key for the default language version';
$_lang['setting_polylang_default_language_desc'] = '';
$_lang['setting_polylang_detect_visitor_language'] = 'Auto detect visitor language';
$_lang['setting_polylang_detect_visitor_language_desc'] = 'When you first visit the site, its language version is determined based on the language of the visitor.';
$_lang['setting_polylang_force_language'] = 'Force set language';
$_lang['setting_polylang_force_language_desc'] = 'Enter the code (ISO) of the language in which to display the site when you first visit it. If the "Autodetect visitor language" option is enabled, it will be ignored.';
$_lang['setting_polylang_visitor_default_language'] = 'Default visitor language key';
$_lang['setting_polylang_visitor_default_language_desc'] = 'If the visitor’s language is not in the list of localizations, then the specified.';
$_lang['setting_polylang_mse2_index'] = 'Index fields in mSearch2';
$_lang['setting_polylang_mse2_index_desc'] = 'Translation for the fields specified by the mse2_index_fields option will be added to the mSearch2 int';
$_lang['setting_polylang_translate_yandex_key'] = 'API key for Yandex translator';
$_lang['setting_polylang_translate_yandex_key_desc'] = '';
$_lang['setting_polylang_translate_google_key'] = 'API key for Google translator';
$_lang['setting_polylang_translate_google_key_desc'] = '';
$_lang['setting_polylang_translate_promt_config'] = 'API settings for PROMT translator';
$_lang['setting_polylang_translate_promt_config_desc'] = '';
$_lang['setting_polylang_translate_data_source_language'] = 'Data source language';
$_lang['setting_polylang_translate_data_source_language_desc'] = 'If necessary, you can specify the language of the lexicon from which the text should be taken for translation. For example, what would the German text be taken from the English lexicon, and for Polish from Russian, then you should indicate: {"de":"en","pl":"ru"}';
$_lang['setting_polylang_disallow_translation_completed_field'] = 'Disallow translation of a completed field';
$_lang['setting_polylang_disallow_translation_completed_field_desc'] = 'If "Yes" is selected and the field already contains text, then its translation from the admin panel will be ignored.';
$_lang['setting_polylang_class_translator'] = 'Translator Class';
$_lang['setting_polylang_class_translator_desc'] = 'Available Values: PolylangTranslatorGoogle; PolylangTranslatorYandex; PolylangTranslatorPromt.';
$_lang['setting_polylang_show_translate_btn'] = 'Show translate button';
$_lang['setting_polylang_show_translate_btn_desc'] = 'If "Yes" is selected, then the "Translate" button will always be displayed in the fields, and not only when hovering over the field.';
$_lang['setting_polylang_reload_lexicon'] = 'Reload lexicons';
$_lang['setting_polylang_reload_lexicon_desc'] = 'Set the lexicons separated by commas that should be reloaded when changing the language. Example: minishop2:product, minishop2:cart';
$_lang['setting_polylang_debug'] = 'Debug';
$_lang['setting_polylang_debug_desc'] = '';
$_lang['setting_polylang_editor_height'] = 'Editor height';
$_lang['setting_polylang_editor_height_desc'] = '';
$_lang['setting_polylang_use_code_editor'] = 'Code editor';
$_lang['setting_polylang_use_code_editor_desc'] = 'Whether to use the code editor when the HTML editor is disabled.';
$_lang['setting_polylang_use_resource_editor_status'] = 'Use HTML resource editor status';
$_lang['setting_polylang_use_resource_editor_status_desc'] = 'If "Yes" is selected, then whether the HTML editor is enabled for the localization will depend on whether it is enabled for the resource itself.';
$_lang['setting_polylang_post_processing_translation'] = 'Post processing translation';
$_lang['setting_polylang_post_processing_translation_desc'] = 'Enabling this option removes extra spaces in modx tags that appear during translation.';
$_lang['setting_polylang_default_language_group'] = 'Default language group';
$_lang['setting_polylang_default_language_group_desc'] = 'The default value for the "language group" parameter for snippets. This value will be used if no value is explicitly specified when calling the snippet.';
$_lang['setting_polylang_set_currency_for_language'] = 'Set default currency';
$_lang['setting_polylang_set_currency_for_language_desc'] = 'Allows you to set the default currency for msMultiCurrency based on the language specified.';