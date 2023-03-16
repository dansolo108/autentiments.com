<?php
$xpdo_meta_map['PolylangLanguage']= array (
  'package' => 'polylang',
  'version' => '1.1',
  'table' => 'polylang_language',
  'extends' => 'xPDOSimpleObject',
  'tableMeta' => 
  array (
    'engine' => 'InnoDB',
  ),
  'fields' => 
  array (
    'currency_id' => 0,
    'name' => NULL,
    'culture_key' => NULL,
    'site_url' => NULL,
    'rank' => 0,
    'rank_translation' => 0,
    'group' => NULL,
    'active' => 1,
  ),
  'fieldMeta' => 
  array (
    'currency_id' => 
    array (
      'dbtype' => 'int',
      'precision' => '11',
      'phptype' => 'integer',
      'null' => false,
      'default' => 0,
    ),
    'name' => 
    array (
      'dbtype' => 'varchar',
      'precision' => '100',
      'phptype' => 'string',
      'null' => false,
    ),
    'culture_key' => 
    array (
      'dbtype' => 'varchar',
      'precision' => '100',
      'phptype' => 'string',
      'null' => false,
      'index' => 'unique',
    ),
    'site_url' => 
    array (
      'dbtype' => 'varchar',
      'precision' => '100',
      'phptype' => 'string',
      'null' => false,
    ),
    'rank' => 
    array (
      'dbtype' => 'int',
      'precision' => '11',
      'phptype' => 'integer',
      'null' => false,
      'default' => 0,
      'index' => 'index',
    ),
    'rank_translation' => 
    array (
      'dbtype' => 'int',
      'precision' => '11',
      'phptype' => 'integer',
      'null' => false,
      'default' => 0,
      'index' => 'index',
    ),
    'group' => 
    array (
      'dbtype' => 'varchar',
      'precision' => '190',
      'phptype' => 'string',
      'null' => true,
    ),
    'active' => 
    array (
      'dbtype' => 'tinyint',
      'precision' => '1',
      'phptype' => 'boolean',
      'null' => false,
      'default' => 1,
      'index' => 'index',
    ),
  ),
  'indexes' => 
  array (
    'culture_key' => 
    array (
      'alias' => 'culture_key',
      'primary' => false,
      'unique' => true,
      'type' => 'BTREE',
      'columns' => 
      array (
        'culture_key' => 
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
    'rank' => 
    array (
      'alias' => 'rank',
      'primary' => false,
      'unique' => false,
      'type' => 'BTREE',
      'columns' => 
      array (
        'rank' => 
        array (
          'length' => '',
          'collation' => 'A',
          'null' => false,
        ),
      ),
    ),
    'rank_translation' => 
    array (
      'alias' => 'rank_translation',
      'primary' => false,
      'unique' => false,
      'type' => 'BTREE',
      'columns' => 
      array (
        'rank_translation' => 
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
    'PolylangContent' => 
    array (
      'class' => 'PolylangContent',
      'local' => 'culture_key',
      'foreign' => 'culture_key',
      'cardinality' => 'many',
      'owner' => 'local',
    ),
    'PolylangProduct' => 
    array (
      'class' => 'PolylangProduct',
      'local' => 'culture_key',
      'foreign' => 'culture_key',
      'cardinality' => 'many',
      'owner' => 'local',
    ),
    'PolylangTv' => 
    array (
      'class' => 'PolylangTv',
      'local' => 'culture_key',
      'foreign' => 'culture_key',
      'cardinality' => 'many',
      'owner' => 'local',
    ),
    'PolylangProductOption' => 
    array (
      'class' => 'PolylangProductOption',
      'local' => 'culture_key',
      'foreign' => 'culture_key',
      'cardinality' => 'many',
      'owner' => 'local',
    ),
    'PolylangTvTmplvars' => 
    array (
      'class' => 'PolylangTvTmplvars',
      'local' => 'culture_key',
      'foreign' => 'culture_key',
      'cardinality' => 'many',
      'owner' => 'local',
    ),
  ),
  'aggregates' => 
  array (
    'MultiCurrency' => 
    array (
      'class' => 'MultiCurrency',
      'local' => 'currency_id',
      'foreign' => 'id',
      'cardinality' => 'one',
      'owner' => 'foreign',
    ),
  ),
);
