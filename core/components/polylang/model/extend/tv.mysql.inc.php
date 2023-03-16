<?php
return array(
    'modTemplateVar' =>
        array(
            'fields' =>
                array(
                    'polylang_enabled' => 0,
                    'polylang_translate' => 0,
                ),
            'fieldMeta' =>
                array(
                    'polylang_enabled' => array(
                        'dbtype' => 'tinyint',
                        'precision' => 1,
                        'attributes' => 'unsigned',
                        'phptype' => 'boolean',
                        'null' => false,
                        'default' => 0,
                        'index' => 'index',
                    ),
                    'polylang_translate' => array(
                        'dbtype' => 'tinyint',
                        'precision' => 1,
                        'attributes' => 'unsigned',
                        'phptype' => 'boolean',
                        'null' => false,
                        'default' => 0,
                        'index' => 'index',
                    ),
                ),
            'indexes' =>
                array(
                    'polylang_enabled' =>
                        array(
                            'alias' => 'polylang_enabled',
                            'primary' => false,
                            'unique' => false,
                            'type' => 'BTREE',
                            'columns' =>
                                array(
                                    'polylang_enabled' =>
                                        array(
                                            'length' => '',
                                            'collation' => 'A',
                                            'null' => false,
                                        ),
                                ),
                        ),
                    'polylang_translate' =>
                        array(
                            'alias' => 'polylang_translate',
                            'primary' => false,
                            'unique' => false,
                            'type' => 'BTREE',
                            'columns' =>
                                array(
                                    'polylang_translate' =>
                                        array(
                                            'length' => '',
                                            'collation' => 'A',
                                            'null' => false,
                                        ),
                                ),
                        )
                ),
        ),
);