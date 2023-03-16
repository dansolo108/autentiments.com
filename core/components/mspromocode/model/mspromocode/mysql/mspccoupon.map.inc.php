<?php
$xpdo_meta_map['mspcCoupon']= array (
  'package' => 'mspromocode',
  'version' => '1.1',
  'table' => 'mspc_coupons',
  'extends' => 'xPDOSimpleObject',
  'tableMeta' => 
  array (
    'engine' => 'MyISAM',
  ),
  'fields' => 
  array (
    'code' => '',
    'description' => '',
    'action_id' => 0,
    'referrer_id' => 0,
    'discount' => '',
    'count' => '',
    'condition_logic' => 'and',
    'begins' => NULL,
    'ends' => NULL,
    'createdon' => NULL,
    'updatedon' => NULL,
    'active' => 0,
    'freeze' => 0,
    'oldprice' => 0,
    'allcart' => 0,
  ),
  'fieldMeta' => 
  array (
    'code' => 
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
    'action_id' => 
    array (
      'dbtype' => 'int',
      'precision' => '10',
      'attributes' => 'unsigned',
      'phptype' => 'integer',
      'null' => true,
      'default' => 0,
    ),
    'referrer_id' => 
    array (
      'dbtype' => 'int',
      'precision' => '10',
      'attributes' => 'unsigned',
      'phptype' => 'integer',
      'null' => true,
      'default' => 0,
    ),
    'discount' => 
    array (
      'dbtype' => 'varchar',
      'precision' => '10',
      'phptype' => 'string',
      'null' => true,
      'default' => '',
    ),
    'count' => 
    array (
      'dbtype' => 'varchar',
      'precision' => '10',
      'phptype' => 'string',
      'null' => true,
      'default' => '',
    ),
    'condition_logic' => 
    array (
      'dbtype' => 'varchar',
      'precision' => '10',
      'phptype' => 'string',
      'null' => false,
      'default' => 'and',
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
    'active' => 
    array (
      'dbtype' => 'tinyint',
      'precision' => '1',
      'phptype' => 'boolean',
      'null' => true,
      'default' => 0,
    ),
    'freeze' => 
    array (
      'dbtype' => 'tinyint',
      'precision' => '1',
      'phptype' => 'boolean',
      'null' => true,
      'default' => 0,
    ),
    'oldprice' => 
    array (
      'dbtype' => 'tinyint',
      'precision' => '1',
      'phptype' => 'boolean',
      'null' => true,
      'default' => 0,
    ),
    'allcart' => 
    array (
      'dbtype' => 'tinyint',
      'precision' => '1',
      'phptype' => 'boolean',
      'null' => true,
      'default' => 0,
    ),
  ),
  'indexes' => 
  array (
    'action_id' => 
    array (
      'alias' => 'action_id',
      'primary' => false,
      'unique' => false,
      'type' => 'BTREE',
      'columns' => 
      array (
        'action_id' => 
        array (
          'length' => '',
          'collation' => 'A',
          'null' => false,
        ),
      ),
    ),
    'referrer_id' => 
    array (
      'alias' => 'referrer_id',
      'primary' => false,
      'unique' => false,
      'type' => 'BTREE',
      'columns' => 
      array (
        'referrer_id' => 
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
    'count' => 
    array (
      'alias' => 'count',
      'primary' => false,
      'unique' => false,
      'type' => 'BTREE',
      'columns' => 
      array (
        'count' => 
        array (
          'length' => '',
          'collation' => 'A',
          'null' => false,
        ),
      ),
    ),
    'code' => 
    array (
      'alias' => 'code',
      'primary' => false,
      'unique' => false,
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
    'freeze' => 
    array (
      'alias' => 'freeze',
      'primary' => false,
      'unique' => false,
      'type' => 'BTREE',
      'columns' => 
      array (
        'freeze' => 
        array (
          'length' => '',
          'collation' => 'A',
          'null' => false,
        ),
      ),
    ),
    'oldprice' => 
    array (
      'alias' => 'oldprice',
      'primary' => false,
      'unique' => false,
      'type' => 'BTREE',
      'columns' => 
      array (
        'oldprice' => 
        array (
          'length' => '',
          'collation' => 'A',
          'null' => false,
        ),
      ),
    ),
    'allcart' => 
    array (
      'alias' => 'allcart',
      'primary' => false,
      'unique' => false,
      'type' => 'BTREE',
      'columns' => 
      array (
        'allcart' => 
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
    'Conditions' => 
    array (
      'class' => 'mspcCondition',
      'local' => 'id',
      'foreign' => 'coupon_id',
      'cardinality' => 'many',
      'owner' => 'local',
    ),
    'Orders' => 
    array (
      'class' => 'mspcOrder',
      'local' => 'id',
      'foreign' => 'coupon_id',
      'cardinality' => 'many',
      'owner' => 'local',
    ),
    'Products' => 
    array (
      'class' => 'mspcResource',
      'local' => 'id',
      'foreign' => 'coupon_id',
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
      'foreign' => 'coupon_id',
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
  'aggregates' => 
  array (
    'Action' => 
    array (
      'class' => 'mspcAction',
      'local' => 'action_id',
      'foreign' => 'id',
      'cardinality' => 'one',
      'owner' => 'foreign',
    ),
    'Referrer' => 
    array (
      'class' => 'modUser',
      'local' => 'referrer_id',
      'foreign' => 'id',
      'cardinality' => 'one',
      'owner' => 'foreign',
    ),
    'ReferrerProfile' => 
    array (
      'class' => 'modUserProfile',
      'local' => 'referrer_id',
      'foreign' => 'internalKey',
      'owner' => 'foreign',
      'cardinality' => 'one',
    ),
  ),
);
