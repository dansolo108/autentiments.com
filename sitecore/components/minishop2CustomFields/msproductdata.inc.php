<?php
// SQL
// ALTER TABLE `modx_ms2_products` ADD `soon` TINYINT(1) UNSIGNED NULL DEFAULT '0' AFTER `favorite`, ADD `discount` TINYINT(1) UNSIGNED NULL DEFAULT '0' AFTER `soon`;
return array(
    'fields' => array(
        'material' => null,
        'video' => null,
        // 'color' => null,
        'soon' => 0,
        'sale' => 0,
        'sortindex' => 0,
        // 'feed' => 0,
    ),
    'fieldMeta' => array(
        'material' => array(
            'dbtype' => 'text',
            'phptype' => 'json',
            'null' => true,
        ),
        'video' => array(
            'dbtype' => 'varchar',
            'precision' => '255',
            'phptype' => 'string',
            'null' => true,
            'default' => null,
        ),
        // 'color' => array(
        //     'dbtype' => 'varchar',
        //     'precision' => '255',
        //     'phptype' => 'string',
        //     'null' => true,
        //     'default' => null,
        // ),
        'soon' => array (
            'dbtype' => 'tinyint',
            'precision' => '1',
            'attributes' => 'unsigned',
            'phptype' => 'boolean',
            'null' => true,
            'default' => 0,
        ),
        'sale' => array (
            'dbtype' => 'tinyint',
            'precision' => '1',
            'attributes' => 'unsigned',
            'phptype' => 'boolean',
            'null' => true,
            'default' => 0,
        ),
        'sortindex' => array(
            'dbtype' => 'int',
            'precision' => '10',
            'phptype' => 'integer',
            'default' => 0,
            'null' => false,
        ),
        // 'feed' => array (
        //     'dbtype' => 'tinyint',
        //     'precision' => '1',
        //     'attributes' => 'unsigned',
        //     'phptype' => 'boolean',
        //     'null' => true,
        //     'default' => 0,
        // ),
    ),
    'indexes' => array(
        'color' => array(
            'alias' => 'color',
            'primary' => false,
            'unique' => false,
            'type' => 'BTREE',
            'columns' => array(
                'color' => array(
                    'length' => '',
                    'collation' => 'A',
                    'null' => false,
                ),
            ),
        ),
        // 'feed' => array(
        //     'alias' => 'feed',
        //     'primary' => false,
        //     'unique' => false,
        //     'type' => 'BTREE',
        //     'columns' => array(
        //         'feed' => array(
        //             'length' => '',
        //             'collation' => 'A',
        //             'null' => false,
        //         ),
        //     ),
        // ),
        'sale' => array(
            'alias' => 'sale',
            'primary' => false,
            'unique' => false,
            'type' => 'BTREE',
            'columns' => array(
                'sale' => array(
                    'length' => '',
                    'collation' => 'A',
                    'null' => false,
                ),
            ),
        ),
        'soon' => array(
            'alias' => 'soon',
            'primary' => false,
            'unique' => false,
            'type' => 'BTREE',
            'columns' => array(
                'soon' => array(
                    'length' => '',
                    'collation' => 'A',
                    'null' => false,
                ),
            ),
        ),
    ),
);