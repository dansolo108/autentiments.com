<?php
$xpdo_meta_map['stikLoyalty']= array (
  'package' => 'stik',
  'version' => '1.1',
  'table' => 'stik_loyalty',
  'extends' => 'xPDOSimpleObject',
  'tableMeta' => 
  array (
    'engine' => 'MyISAM',
  ),
  'fields' => 
  array (
    'amount' => 0.0,
    'discount' => 0,
  ),
  'fieldMeta' => 
  array (
    'amount' => 
    array (
      'dbtype' => 'decimal',
      'precision' => '12,2',
      'phptype' => 'float',
      'null' => true,
      'default' => 0.0,
    ),
    'discount' => 
    array (
      'dbtype' => 'int',
      'precision' => '3',
      'phptype' => 'integer',
      'null' => false,
      'default' => 0,
    ),
  ),
);
