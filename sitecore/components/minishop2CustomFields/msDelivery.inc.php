<?php
// SQL:
// ALTER TABLE `modx_ms2_deliveries` ADD `tariff` VARCHAR(100) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL AFTER `requires`;
return array(
    'fields' => array (
        'tariff' => NULL,
        'slug' => NULL,
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
    ),
);