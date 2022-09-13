<?php
$xpdo_meta_map['Modification'] = array(
    'package' => 'autentiments',
    'version' => '1.1',
    'table' => 'auten_product_modifications',
    'extends' => 'xPDOSimpleObject',
    'tableMeta' =>
        array(
            'engine' => 'InnoDB',
        ),
    'fields' =>
        [
            'product_id' => NULL,
            'code' => NULL,
            'hide' => 0,
            'price' => 0.0,
            'old_price' => 0.0,
        ],
    'fieldMeta' =>
        array(
            'product_id' =>
                array(
                    'dbtype' => 'int',
                    'precision' => '10',
                    'phptype' => 'integer',
                    'null' => false,
                ),
            'code' =>
                array(
                    'dbtype' => 'varchar',
                    'precision' => '255',
                    'phptype' => 'string',
                    'null' => false,
                ),
            'hide' =>
                array(
                    'dbtype' => 'tinyint',
                    'precision' => '1',
                    'phptype' => 'boolean',
                    'null' => false,
                    'default' => 0,
                ),
            'price' =>
                array(
                    'dbtype' => 'decimal',
                    'precision' => '12,2',
                    'phptype' => 'float',
                    'null' => true,
                    'default' => 0.0,
                ),
            'old_price' =>
                array(
                    'dbtype' => 'decimal',
                    'precision' => '12,2',
                    'phptype' => 'float',
                    'null' => true,
                    'default' => 0.0,
                ),
        ),
    'indexes' =>
        array(
            'code' =>
                array(
                    'alias' => 'code',
                    'primary' => false,
                    'unique' => false,
                    'type' => 'BTREE',
                    'columns' =>
                        array(
                            'code' =>
                                array(
                                    'length' => '',
                                    'collation' => 'A',
                                    'null' => false,
                                ),
                        ),
                ),
        ),
    'composites' =>
        array(
            'Details' =>
                array(
                    'class' => 'ModificationDetail',
                    'local' => 'id',
                    'foreign' => 'modification_id',
                    'cardinality' => 'many',
                    'owner' => 'local',
                ),
            'Files' =>
                [
                    'class' => 'ModificationFile',
                    'local' => 'id',
                    'foreign' => 'modification_id',
                    'cardinality' => 'many',
                    'owner' => 'local',
                ],
            'Remains' =>
                [
                    'class' => 'ModificationRemain',
                    'local' => 'id',
                    'foreign' => 'modification_id',
                    'cardinality' => 'many',
                    'owner' => 'local',
                ],
        ),
    'aggregates' =>
        array(
            'Product' =>
                array(
                    'class' => 'msProduct',
                    'local' => 'product_id',
                    'foreign' => 'id',
                    'cardinality' => 'one',
                    'owner' => 'foreign',
                ),
        ),
);
