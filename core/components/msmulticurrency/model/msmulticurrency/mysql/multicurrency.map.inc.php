<?php
$xpdo_meta_map['MultiCurrency']= array (
  'package' => 'msmulticurrency',
  'version' => '1.1',
  'table' => 'multi_currency',
  'extends' => 'xPDOSimpleObject',
  'tableMeta' => 
  array (
    'engine' => 'InnoDB',
  ),
  'fields' => 
  array (
    'name' => NULL,
    'code' => NULL,
    'symbol_left' => NULL,
    'symbol_right' => NULL,
    'precision' => 2,
  ),
  'fieldMeta' => 
  array (
    'name' => 
    array (
      'dbtype' => 'varchar',
      'precision' => '255',
      'phptype' => 'string',
      'null' => true,
    ),
    'code' => 
    array (
      'dbtype' => 'varchar',
      'precision' => '3',
      'phptype' => 'string',
      'null' => false,
      'index' => 'unique',
    ),
    'symbol_left' => 
    array (
      'dbtype' => 'varchar',
      'precision' => '100',
      'phptype' => 'string',
      'null' => true,
    ),
    'symbol_right' => 
    array (
      'dbtype' => 'varchar',
      'precision' => '100',
      'phptype' => 'string',
      'null' => true,
    ),
    'precision' => 
    array (
      'dbtype' => 'tinyint',
      'precision' => '2',
      'phptype' => 'integer',
      'null' => false,
      'default' => 2,
    ),
  ),
  'indexes' => 
  array (
    'code' => 
    array (
      'alias' => 'code',
      'primary' => false,
      'unique' => true,
      'type' => 'BTREE',
      'columns' => 
      array (
        'code' => 
        array (
          'length' => '',
          'collation' => 'A',
          'null' => false,
        ),
      ),
    ),
  ),
  'composites' => 
  array (
    'MultiCurrencySetMember' => 
    array (
      'class' => 'MultiCurrencySetMember',
      'local' => 'id',
      'foreign' => 'cid',
      'cardinality' => 'many',
      'owner' => 'local',
    ),
  ),
);
