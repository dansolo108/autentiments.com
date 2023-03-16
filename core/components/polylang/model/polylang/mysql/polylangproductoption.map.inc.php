<?php
$xpdo_meta_map['PolylangProductOption']= array (
  'package' => 'polylang',
  'version' => '1.1',
  'table' => 'polylang_product_option',
  'extends' => 'xPDOObject',
  'tableMeta' => 
  array (
    'engine' => 'InnoDB',
  ),
  'fields' => 
  array (
    'content_id' => NULL,
    'culture_key' => NULL,
    'key' => NULL,
    'value' => NULL,
  ),
  'fieldMeta' => 
  array (
    'content_id' => 
    array (
      'dbtype' => 'int',
      'precision' => '11',
      'phptype' => 'integer',
      'null' => false,
      'index' => 'index',
    ),
    'culture_key' => 
    array (
      'dbtype' => 'varchar',
      'precision' => '100',
      'phptype' => 'string',
      'null' => false,
    ),
    'key' => 
    array (
      'dbtype' => 'varchar',
      'precision' => '255',
      'phptype' => 'string',
      'null' => false,
    ),
    'value' => 
    array (
      'dbtype' => 'text',
      'phptype' => 'string',
      'null' => false,
    ),
  ),
  'indexes' => 
  array (
    'product' => 
    array (
      'alias' => 'product',
      'primary' => false,
      'unique' => false,
      'type' => 'BTREE',
      'columns' => 
      array (
        'content_id' => 
        array (
          'length' => '',
          'collation' => 'A',
          'null' => false,
        ),
        'culture_key' => 
        array (
          'length' => '',
          'collation' => 'A',
          'null' => false,
        ),
        'key' => 
        array (
          'length' => '',
          'collation' => 'A',
          'null' => false,
        ),
      ),
    ),
  ),
  'aggregates' => 
  array (
    'Resource' => 
    array (
      'class' => 'modResource',
      'local' => 'content_id',
      'foreign' => 'id',
      'cardinality' => 'one',
      'owner' => 'foreign',
    ),
    'PolylangLanguage' => 
    array (
      'class' => 'PolylangLanguage',
      'local' => 'culture_key',
      'foreign' => 'culture_key',
      'cardinality' => 'one',
      'owner' => 'foreign',
    ),
    'Ms2Options' => 
    array (
      'class' => 'msOption',
      'local' => 'key',
      'foreign' => 'key',
      'cardinality' => 'one',
      'owner' => 'foreign',
    ),
  ),
);
