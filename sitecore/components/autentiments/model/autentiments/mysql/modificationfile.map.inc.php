<?php
$xpdo_meta_map['ModificationFile']= array (
  'package' => 'autentiments',
  'version' => '1.1',
  'table' => 'auten_product_modification_files',
  'extends' => 'xPDOSimpleObject',
  'tableMeta' => 
  array (
    'engine' => 'InnoDB',
  ),
  'fields' => 
  array (
    'modification_id' => NULL,
    'file' => NULL,
    'visible_index' => 0,
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
    'file' => 
    array (
      'dbtype' => 'varchar',
      'precision' => '255',
      'phptype' => 'string',
      'null' => false,
    ),
    'visible_index' => 
    array (
      'dbtype' => 'int',
      'precision' => '10',
      'phptype' => 'integer',
      'default' => 0,
      'null' => false,
    ),
  ),
  'indexes' => 
  array (
    'modification_id' => 
    array (
      'alias' => 'modification_id',
      'primary' => false,
      'unique' => false,
      'type' => 'BTREE',
      'columns' => 
      array (
        'modification_id' => 
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
  ),
);
