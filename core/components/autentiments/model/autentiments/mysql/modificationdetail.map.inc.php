<?php
$xpdo_meta_map['ModificationDetail']= array (
  'package' => 'autentiments',
  'version' => '1.1',
  'table' => 'auten_product_modification_details',
  'extends' => 'xPDOSimpleObject',
  'tableMeta' => 
  array (
    'engine' => 'InnoDB',
  ),
  'fields' => 
  array (
    'type_id' => NULL,
    'modification_id' => NULL,
    'value' => NULL,
  ),
  'fieldMeta' => 
  array (
    'type_id' => 
    array (
      'dbtype' => 'varchar',
      'precision' => '255',
      'phptype' => 'string',
      'null' => false,
    ),
    'modification_id' => 
    array (
      'dbtype' => 'int',
      'precision' => '10',
      'phptype' => 'integer',
      'null' => false,
    ),
    'value' => 
    array (
      'dbtype' => 'varchar',
      'precision' => '255',
      'phptype' => 'string',
      'null' => false,
    ),
  ),
  'indexes' => 
  array (
    'unique_index' => 
    array (
      'alias' => 'unique_index',
      'primary' => false,
      'unique' => true,
      'type' => 'BTREE',
      'columns' => 
      array (
        'type_id' => 
        array (
          'length' => '',
          'collation' => 'A',
          'null' => false,
        ),
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
    'Type' => 
    array (
      'class' => 'DetailType',
      'local' => 'type_id',
      'foreign' => 'id',
      'cardinality' => 'one',
      'owner' => 'foreign',
    ),
  ),
);
