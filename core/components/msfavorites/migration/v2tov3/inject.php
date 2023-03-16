<?php

if (!class_exists('msFavorite')) {
    class msFavorite extends xPDOObject
    {
    }

    class msFavorite_mysql extends msFavorite
    {
    }
}

if (empty($modx->map['msFavorite'])) {
    $modx->map['msFavorite'] = [
        'package'   => 'msfavorites',
        'version'   => '1.1',
        'table'     => 'ms_favorites',
        'extends'   => 'xPDOObject',
        'tableMeta' => [
            'engine' => 'MyISAM',
        ],
        'fields'    => [
            'uid'        => '',
            'rid'        => null,
            'list'       => null,
            'temporary'  => 0,
            'timestamp'  => 'CURRENT_TIMESTAMP',
            'properties' => '',
        ],
        'fieldMeta' => [
            'uid'        => [
                'dbtype'    => 'varchar',
                'precision' => '40',
                'phptype'   => 'string',
                'null'      => false,
                'index'     => 'pk',
                'default'   => '',
            ],
            'rid'        => [
                'dbtype'     => 'int',
                'precision'  => '10',
                'attributes' => 'unsigned',
                'phptype'    => 'integer',
                'null'       => false,
                'index'      => 'pk',
            ],
            'list'       => [
                'dbtype'    => 'varchar',
                'precision' => '75',
                'phptype'   => 'string',
                'null'      => false,
                'index'     => 'pk',
            ],
            'temporary'  => [
                'dbtype'     => 'tinyint',
                'precision'  => '1',
                'attributes' => 'unsigned',
                'phptype'    => 'boolean',
                'null'       => false,
                'default'    => 0,
                'index'      => 'index',
            ],
            'timestamp'  => [
                'dbtype'  => 'timestamp',
                'phptype' => 'timestamp',
                'null'    => false,
                'default' => 'CURRENT_TIMESTAMP',
            ],
            'properties' => [
                'dbtype'  => 'text',
                'phptype' => 'json',
                'null'    => false,
                'default' => '',
            ],
        ],
        'indexes'   => [
            'favorite'  => [
                'alias'   => 'favorite',
                'primary' => false,
                'unique'  => false,
                'type'    => 'BTREE',
                'columns' => [
                    'uid'  => [
                        'length'    => '40',
                        'collation' => 'A',
                        'null'      => false,
                    ],
                    'rid'  => [
                        'length'    => '',
                        'collation' => 'A',
                        'null'      => false,
                    ],
                    'list' => [
                        'length'    => '75',
                        'collation' => 'A',
                        'null'      => false,
                    ],
                ],
            ],
            'uid'       => [
                'alias'   => 'uid',
                'primary' => false,
                'unique'  => false,
                'type'    => 'BTREE',
                'columns' => [
                    'uid' => [
                        'length'    => '40',
                        'collation' => 'A',
                        'null'      => false,
                    ],
                ],
            ],
            'rid'       => [
                'alias'   => 'rid',
                'primary' => false,
                'unique'  => false,
                'type'    => 'BTREE',
                'columns' => [
                    'rid' => [
                        'length'    => '',
                        'collation' => 'A',
                        'null'      => false,
                    ],
                ],
            ],
            'list'      => [
                'alias'   => 'list',
                'primary' => false,
                'unique'  => false,
                'type'    => 'BTREE',
                'columns' => [
                    'list' => [
                        'length'    => '75',
                        'collation' => 'A',
                        'null'      => false,
                    ],
                ],
            ],
            'temporary' => [
                'alias'   => 'temporary',
                'primary' => false,
                'unique'  => false,
                'type'    => 'BTREE',
                'columns' => [
                    'temporary' => [
                        'length'    => '',
                        'collation' => 'A',
                        'null'      => false,
                    ],
                ],
            ],
        ],
    ];

}
