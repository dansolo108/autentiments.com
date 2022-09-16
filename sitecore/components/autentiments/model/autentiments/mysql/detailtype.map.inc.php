<?php
$xpdo_meta_map['DetailType']= array (
  'package' => 'autentiments',
  'version' => '1.1',
  'table' => 'auten_detail_types',
  'extends' => 'xPDOSimpleObject',
  'tableMeta' => 
  array (
    'engine' => 'InnoDB',
  ),
  'fields' => 
  array (
    'name' => NULL,
  ),
  'fieldMeta' => 
  array (
    'name' => 
    array (
      'dbtype' => 'varchar',
      'precision' => '255',
      'phptype' => 'string',
      'null' => false,
    ),
  ),
  'indexes' => 
  array (
    'name' => 
    array (
      'alias' => 'name',
      'primary' => false,
      'unique' => true,
      'type' => 'BTREE',
      'columns' => 
      array (
        'name' => 
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
    'Details' => 
    array (
      'class' => 'ModificationDetail',
      'local' => 'id',
      'foreign' => 'type_id',
      'cardinality' => 'many',
      'owner' => 'local',
    ),
  ),
);
