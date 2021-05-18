<?php
$xpdo_meta_map['stikPurchaseAmount']= array (
  'package' => 'stik',
  'version' => '1.1',
  'table' => 'stik_purchases_amounts',
  'extends' => 'xPDOSimpleObject',
  'tableMeta' => 
  array (
    'engine' => 'MyISAM',
  ),
  'fields' => 
  array (
    'name' => '',
    'phone' => '',
    'amount' => 0.0,
  ),
  'fieldMeta' => 
  array (
    'name' => 
    array (
      'dbtype' => 'varchar',
      'phptype' => 'string',
      'precision' => '256',
      'null' => false,
      'default' => '',
    ),
    'phone' => 
    array (
      'dbtype' => 'varchar',
      'phptype' => 'string',
      'precision' => '256',
      'null' => false,
      'default' => '',
    ),
    'amount' => 
    array (
      'dbtype' => 'decimal',
      'precision' => '12,2',
      'phptype' => 'float',
      'null' => true,
      'default' => 0.0,
    ),
  ),
  'indexes' => 
  array (
    'phone' => 
    array (
      'alias' => 'phone',
      'primary' => false,
      'unique' => false,
      'type' => 'BTREE',
      'columns' => 
      array (
        'phone' => 
        array (
          'length' => '',
          'collation' => 'A',
          'null' => false,
        ),
      ),
    ),
  ),
);
