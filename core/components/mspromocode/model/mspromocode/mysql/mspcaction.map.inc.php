<?php
$xpdo_meta_map['mspcAction']= array (
  'package' => 'mspromocode',
  'version' => '1.1',
  'table' => 'mspc_actions',
  'extends' => 'xPDOSimpleObject',
  'tableMeta' => 
  array (
    'engine' => 'MyISAM',
  ),
  'fields' => 
  array (
    'name' => '',
    'description' => '',
    'discount' => '0',
    'condition_logic' => 'and',
    'createdon' => NULL,
    'updatedon' => NULL,
    'begins' => NULL,
    'ends' => NULL,
    'ref' => 0,
    'active' => 1,
  ),
  'fieldMeta' => 
  array (
    'name' => 
    array (
      'dbtype' => 'varchar',
      'precision' => '100',
      'phptype' => 'string',
      'null' => false,
      'default' => '',
    ),
    'description' => 
    array (
      'dbtype' => 'text',
      'phptype' => 'string',
      'null' => true,
      'default' => '',
    ),
    'discount' => 
    array (
      'dbtype' => 'varchar',
      'precision' => '10',
      'phptype' => 'string',
      'null' => true,
      'default' => '0',
    ),
    'condition_logic' => 
    array (
      'dbtype' => 'varchar',
      'precision' => '10',
      'phptype' => 'string',
      'null' => false,
      'default' => 'and',
    ),
    'createdon' => 
    array (
      'dbtype' => 'datetime',
      'phptype' => 'datetime',
      'null' => true,
    ),
    'updatedon' => 
    array (
      'dbtype' => 'datetime',
      'phptype' => 'datetime',
      'null' => true,
    ),
    'begins' => 
    array (
      'dbtype' => 'datetime',
      'phptype' => 'datetime',
      'null' => true,
    ),
    'ends' => 
    array (
      'dbtype' => 'datetime',
      'phptype' => 'datetime',
      'null' => true,
    ),
    'ref' => 
    array (
      'dbtype' => 'tinyint',
      'precision' => '1',
      'phptype' => 'boolean',
      'null' => true,
      'default' => 0,
    ),
    'active' => 
    array (
      'dbtype' => 'tinyint',
      'precision' => '1',
      'phptype' => 'boolean',
      'null' => true,
      'default' => 1,
    ),
  ),
  'indexes' => 
  array (
    'name' => 
    array (
      'alias' => 'name',
      'primary' => false,
      'unique' => false,
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
    'discount' => 
    array (
      'alias' => 'discount',
      'primary' => false,
      'unique' => false,
      'type' => 'BTREE',
      'columns' => 
      array (
        'discount' => 
        array (
          'length' => '',
          'collation' => 'A',
          'null' => false,
        ),
      ),
    ),
    'ref' => 
    array (
      'alias' => 'ref',
      'primary' => false,
      'unique' => false,
      'type' => 'BTREE',
      'columns' => 
      array (
        'ref' => 
        array (
          'length' => '',
          'collation' => 'A',
          'null' => false,
        ),
      ),
    ),
    'active' => 
    array (
      'alias' => 'active',
      'primary' => false,
      'unique' => false,
      'type' => 'BTREE',
      'columns' => 
      array (
        'active' => 
        array (
          'length' => '',
          'collation' => 'A',
          'null' => false,
        ),
      ),
    ),
    'date' => 
    array (
      'alias' => 'date',
      'primary' => false,
      'unique' => false,
      'type' => 'BTREE',
      'columns' => 
      array (
        'begins' => 
        array (
          'length' => '',
          'collation' => 'A',
          'null' => false,
        ),
        'ends' => 
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
    'Coupons' => 
    array (
      'class' => 'mspcCoupon',
      'local' => 'id',
      'foreign' => 'action_id',
      'cardinality' => 'many',
      'owner' => 'local',
    ),
    'Conditions' => 
    array (
      'class' => 'mspcCondition',
      'local' => 'id',
      'foreign' => 'action_id',
      'cardinality' => 'many',
      'owner' => 'local',
    ),
    'Products' => 
    array (
      'class' => 'mspcResource',
      'local' => 'id',
      'foreign' => 'action_id',
      'cardinality' => 'many',
      'owner' => 'local',
      'criteria' => 
      array (
        'foreign' => 
        array (
          'type' => 'product',
        ),
      ),
    ),
    'Categories' => 
    array (
      'class' => 'mspcResource',
      'local' => 'id',
      'foreign' => 'action_id',
      'cardinality' => 'many',
      'owner' => 'local',
      'criteria' => 
      array (
        'foreign' => 
        array (
          'type' => 'category',
        ),
      ),
    ),
  ),
);
