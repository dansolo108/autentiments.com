<?php
$xpdo_meta_map['ModificationRemain']= array (
  'package' => 'autentiments',
  'version' => '1.1',
  'table' => 'auten_product_modification_remains',
  'extends' => 'xPDOSimpleObject',
  'tableMeta' => 
  array (
    'engine' => 'InnoDB',
  ),
  'fields' => 
  array (
    'modification_id' => NULL,
    'remains' => 0,
    'store_id' => NULL,
  ),
  'fieldMeta' => 
  array (
    'modification_id' => 
    array (
      'dbtype' => 'int',
      'precision' => '10',
      'phptype' => 'integer',
      'null' => false,
    ),
    'remains' => 
    array (
      'dbtype' => 'int',
      'precision' => '10',
      'default' => 0,
      'phptype' => 'integer',
      'null' => false,
    ),
    'store_id' => 
    array (
      'dbtype' => 'int',
      'precision' => '10',
      'phptype' => 'integer',
      'null' => false,
    ),
  ),
  'indexes' => 
  array (
    'remain' => 
    array (
      'alias' => 'remain',
      'primary' => false,
      'unique' => true,
      'type' => 'BTREE',
      'columns' => 
      array (
        'modification_id' => 
        array (
          'length' => '',
          'collation' => 'A',
          'null' => false,
        ),
        'store_id' => 
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
    'Modification' => 
    array (
      'class' => 'Modification',
      'local' => 'modification_id',
      'foreign' => 'id',
      'cardinality' => 'one',
      'owner' => 'foreign',
    ),
    'Store' => 
    array (
      'class' => 'Store',
      'local' => 'store_id',
      'foreign' => 'id',
      'cardinality' => 'one',
      'owner' => 'foreign',
    ),
  ),
);
