<?php
// SQL:
// ALTER TABLE `modx_ms2_deliveries` ADD `tariff` VARCHAR(100) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL AFTER `requires`;
return array(
    'fields' => array (
        'tariff' => NULL,
        'slug' => NULL,
        'free_delivery_rf' => 0,
        'show_on_ru' => 1,
        'show_on_en' => 1,
    ),
    'fieldMeta' => array(
        'tariff' => array (
			'dbtype' => 'varchar',
			'phptype' => 'string',
			'precision' => 100,
            'null' => true,
            'default' => null,
        ),
        'slug' => array (
			'dbtype' => 'varchar',
			'phptype' => 'string',
			'precision' => 100,
            'null' => true,
            'default' => null,
        ),
        'free_delivery_rf' => array (
            'dbtype' => 'tinyint',
            'precision' => '1',
            'attributes' => 'unsigned',
            'phptype' => 'boolean',
            'null' => true,
            'default' => 0,
        ),
        'show_on_ru' => array (
            'dbtype' => 'tinyint',
            'precision' => '1',
            'attributes' => 'unsigned',
            'phptype' => 'boolean',
            'null' => true,
            'default' => 1,
        ),
        'show_on_en' => array (
            'dbtype' => 'tinyint',
            'precision' => '1',
            'attributes' => 'unsigned',
            'phptype' => 'boolean',
            'null' => true,
            'default' => 1,
        ),
    ),
);