INSERT INTO `%table_prefix%polylang_field` (`id`, `class_name`, `name`, `caption`, `description`, `meta`, `xtype`, `code`, `is_option`, `required`, `translate`, `sortable`, `active`, `system`, `rank`) VALUES
(1, 'PolylangContent', 'content_id', 'content_id', '', '{\"dbtype\":\"int\",\"phptype\":\"int\"}', 'hidden', '', 0, 0, 0, 0, 1, 1, 0),
(2, 'PolylangContent', 'culture_key', 'Language', '', '{\"dbtype\":\"TEXT\",\"phptype\":\"string\"}', 'polylang-combo-language', '', 0, 1, 0, 0, 1, 1, 0),
(3, 'PolylangContent', 'pagetitle', 'Title', '', '{\"dbtype\":\"TEXT\",\"phptype\":\"string\"}', 'polylang-field', '', 0, 1, 1, 0, 1, 0, 1),
(4, 'PolylangContent', 'seotitle', 'SEO Title', '', '{\"dbtype\":\"TEXT\",\"phptype\":\"string\"}', 'polylang-field', '', 0, 0, 1, 0, 1, 0, 2),
(5, 'PolylangContent', 'keywords', 'SEO Keywords', '', '{\"dbtype\":\"TEXT\",\"phptype\":\"string\"}', 'polylang-field', '', 0, 0, 1, 0, 1, 0, 3),
(6, 'PolylangContent', 'longtitle', 'Long Title', '', '{\"dbtype\":\"TEXT\",\"phptype\":\"string\"}', 'polylang-field', '', 0, 0, 1, 0, 1, 0, 4),
(7, 'PolylangContent', 'menutitle', 'Menu Title', '', '{\"dbtype\":\"TEXT\",\"phptype\":\"string\"}', 'polylang-field', '', 0, 0, 1, 0, 1, 0, 5),
(8, 'PolylangContent', 'introtext', 'Summary (introtext)', '', '{\"dbtype\":\"TEXT\",\"phptype\":\"string\"}', 'textarea', '', 0, 0, 1, 0, 1, 0, 7),
(9, 'PolylangContent', 'description', 'Description', '', '{\"dbtype\":\"TEXT\",\"phptype\":\"string\"}', 'textarea', '', 0, 0, 1, 0, 1, 0, 6),
(10, 'PolylangContent', 'content', 'Content', '', '{\"dbtype\":\"TEXT\",\"phptype\":\"string\"}', 'polylang-text-editor', '', 0, 0, 1, 0, 1, 0, 8),
(11, 'PolylangProduct', 'color', 'Colors', '', '{\"dbtype\":\"TEXT\",\"phptype\":\"json\"}', 'polylang-combo-auto-complete', '', 0, 0, 0, 0, 1, 0, 9),
(12, 'PolylangProduct', 'size', 'Sizes', '', '{\"dbtype\":\"TEXT\",\"phptype\":\"json\"}', 'polylang-combo-auto-complete', '', 0, 0, 0, 0, 1, 0, 10),
(13, 'PolylangProduct', 'tags', 'Tags', '', '{\"dbtype\":\"TEXT\",\"phptype\":\"json\"}', 'polylang-combo-auto-complete', '', 0, 0, 0, 0, 1, 0, 11);