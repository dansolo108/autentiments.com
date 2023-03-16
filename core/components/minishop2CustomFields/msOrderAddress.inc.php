<?php
// SQL:
// ALTER TABLE `modx_ms2_order_addresses` ADD `name` VARCHAR(100) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL AFTER `receiver`, ADD `surname` VARCHAR(100) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL AFTER `name`;
// ALTER TABLE `modx_ms2_order_addresses` ADD `corpus` VARCHAR(10) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL AFTER `room`;
return array(
    'fields' => array (
        'name' => null,
        'surname' => null,
        'corpus' => null,
        'entrance' => null,
      ),
    'fieldMeta' => array(
        'name' => array (
			'dbtype' => 'varchar',
			'phptype' => 'string',
			'precision' => '100',
            'null' => true,
            'default' => null,
        ),
        'surname' => array (
			'dbtype' => 'varchar',
			'phptype' => 'string',
			'precision' => '100',
            'null' => true,
            'default' => null,
        ),
        'corpus' => array (
			'dbtype' => 'varchar',
			'phptype' => 'string',
			'precision' => '10',
            'null' => true,
            'default' => null,
        ),
        'entrance' => array (
            'dbtype' => 'int',
            'precision' => '2',
            'phptype' => 'integer',
            'null' => false,
        ),
    ),
);