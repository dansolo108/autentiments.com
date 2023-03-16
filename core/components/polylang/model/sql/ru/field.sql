INSERT INTO `%table_prefix%polylang_field` (`id`, `class_name`, `name`, `caption`, `description`, `meta`, `xtype`, `code`, `is_option`, `required`, `translate`, `sortable`, `active`, `system`, `rank`) VALUES
(1, 'PolylangContent', 'content_id', 'content_id', '', '{\"dbtype\":\"int\",\"phptype\":\"int\"}', 'hidden', '', 0, 0, 0, 0, 1, 1, 0),
(2, 'PolylangContent', 'culture_key', 'Язык', '', '{\"dbtype\":\"TEXT\",\"phptype\":\"string\"}', 'polylang-combo-language', '', 0, 1, 0, 0, 1, 1, 0),
(3, 'PolylangContent', 'pagetitle', 'Заголовок', '', '{\"dbtype\":\"TEXT\",\"phptype\":\"string\"}', 'polylang-field', '', 0, 1, 1, 0, 1, 0, 1),
(4, 'PolylangContent', 'seotitle', 'SEO Заголовок', '', '{\"dbtype\":\"TEXT\",\"phptype\":\"string\"}', 'polylang-field', '', 0, 0, 1, 0, 1, 0, 2),
(5, 'PolylangContent', 'keywords', 'SEO Ключевые слова', '', '{\"dbtype\":\"TEXT\",\"phptype\":\"string\"}', 'polylang-field', '', 0, 0, 1, 0, 1, 0, 3),
(6, 'PolylangContent', 'longtitle', 'Расширенный заголовок', '', '{\"dbtype\":\"TEXT\",\"phptype\":\"string\"}', 'polylang-field', '', 0, 0, 1, 0, 1, 0, 4),
(7, 'PolylangContent', 'menutitle', 'Заголовок меню', '', '{\"dbtype\":\"TEXT\",\"phptype\":\"string\"}', 'polylang-field', '', 0, 0, 1, 0, 1, 0, 5),
(8, 'PolylangContent', 'introtext', 'Аннотация', '', '{\"dbtype\":\"TEXT\",\"phptype\":\"string\"}', 'textarea', '', 0, 0, 1, 0, 1, 0, 7),
(9, 'PolylangContent', 'description', 'Описание', '', '{\"dbtype\":\"TEXT\",\"phptype\":\"string\"}', 'textarea', '', 0, 0, 1, 0, 1, 0, 6),
(10, 'PolylangContent', 'content', 'Содержимое', '', '{\"dbtype\":\"TEXT\",\"phptype\":\"string\"}', 'polylang-text-editor', '', 0, 0, 1, 0, 1, 0, 8),
(11, 'PolylangProduct', 'color', 'Цвета', '', '{\"dbtype\":\"TEXT\",\"phptype\":\"json\"}', 'polylang-combo-auto-complete', '', 0, 0, 0, 0, 1, 0, 9),
(12, 'PolylangProduct', 'size', 'Размеры', '', '{\"dbtype\":\"TEXT\",\"phptype\":\"json\"}', 'polylang-combo-auto-complete', '', 0, 0, 0, 0, 1, 0, 10),
(13, 'PolylangProduct', 'tags', 'Теги', '', '{\"dbtype\":\"TEXT\",\"phptype\":\"json\"}', 'polylang-combo-auto-complete', '', 0, 0, 0, 0, 1, 0, 11);