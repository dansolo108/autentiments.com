<?php
return array(
    'msopModification' =>
        array(
            'fields' =>
                array(
                    'currency_id' => 0,
                    'currency_set_id' => 0,
                    'msmc_price' => 0,
                    'msmc_old_price' => 0,
                ),
            'fieldMeta' =>
                array(
                    'currency_id' =>
                        array(
                            'dbtype' => 'int',
                            'precision' => '11',
                            'phptype' => 'integer',
                            'default' => 0,
                            'null' => false,
                        ), 'currency_set_id' =>
                    array(
                        'dbtype' => 'int',
                        'precision' => '11',
                        'phptype' => 'integer',
                        'default' => 1,
                        'null' => false,
                    ),
                    'msmc_price' =>
                        array(
                            'dbtype' => 'decimal',
                            'precision' => '12.2',
                            'phptype' => 'float',
                            'default' => 0,
                            'null' => false,
                        ),
                    'msmc_old_price' =>
                        array(
                            'dbtype' => 'decimal',
                            'precision' => '12.2',
                            'phptype' => 'float',
                            'default' => 0,
                            'null' => false,
                        ),
                ),
            'indexes' =>
                array(
                    'currency_id' =>
                        array(
                            'alias' => 'currency_id',
                            'primary' => false,
                            'unique' => false,
                            'type' => 'BTREE',
                            'columns' =>
                                array(
                                    'currency_id' =>
                                        array(
                                            'length' => '',
                                            'collation' => 'A',
                                            'null' => false,
                                        ),
                                ),
                        ),
                    'currency_set_id' =>
                        array(
                            'alias' => 'currency_set_id',
                            'primary' => false,
                            'unique' => false,
                            'type' => 'BTREE',
                            'columns' =>
                                array(
                                    'currency_set_id' =>
                                        array(
                                            'length' => '',
                                            'collation' => 'A',
                                            'null' => false,
                                        ),
                                ),
                        ),
                ),
        ),
);