<?php
$xpdo_meta_map['ModificationSubscriber']= array (
  'package' => 'autentiments',
  'version' => '1.1',
  'table' => 'auten_subscribers',
  'extends' => 'xPDOObject',
  'tableMeta' => 
  array (
    'engine' => 'InnoDB',
  ),
  'fields' => 
  array (
    'modification_id' => NULL,
    'date' => NULL,
    'phone' => NULL,
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
    'date' => 
    array (
      'dbtype' => 'datetime',
      'phptype' => 'datetime',
      'null' => false,
    ),
    'phone' => 
    array (
      'dbtype' => 'varchar',
      'precision' => '12',
      'phptype' => 'string',
      'null' => false,
    ),
  ),
  'indexes' => 
  array (
    'PRIMARY' => 
    array (
      'alias' => 'PRIMARY',
      'primary' => true,
      'unique' => true,
      'type' => 'BTREE',
      'columns' => 
      array (
        'phone' => 
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
  ),
);
