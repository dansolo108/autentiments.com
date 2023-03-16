<?php
$xpdo_meta_map['msfFavoriteObjectExtra']= array (
  'package' => 'msfavorites',
  'version' => '1.1',
  'table' => 'msf_favorite_extra_objects',
  'extends' => 'xPDOObject',
  'tableMeta' => 
  array (
    'engine' => 'InnoDB',
  ),
  'fields' => 
  array (
    'lid' => NULL,
    'kid' => NULL,
    'uid' => NULL,
    'extra' => '',
  ),
  'fieldMeta' => 
  array (
    'lid' => 
    array (
      'dbtype' => 'int',
      'precision' => '10',
      'attributes' => 'unsigned',
      'phptype' => 'integer',
      'null' => false,
      'index' => 'pk',
    ),
    'kid' => 
    array (
      'dbtype' => 'int',
      'precision' => '10',
      'attributes' => 'unsigned',
      'phptype' => 'integer',
      'null' => false,
      'index' => 'pk',
    ),
    'uid' => 
    array (
      'dbtype' => 'varchar',
      'precision' => '40',
      'phptype' => 'string',
      'null' => false,
      'index' => 'pk',
    ),
    'extra' => 
    array (
      'dbtype' => 'text',
      'phptype' => 'array',
      'null' => false,
      'default' => '',
    ),
  ),
  'indexes' => 
  array (
    'lid' => 
    array (
      'alias' => 'lid',
      'primary' => false,
      'unique' => false,
      'type' => 'BTREE',
      'columns' => 
      array (
        'lid' => 
        array (
          'length' => '',
          'collation' => 'A',
          'null' => false,
        ),
      ),
    ),
    'kid' => 
    array (
      'alias' => 'kid',
      'primary' => false,
      'unique' => false,
      'type' => 'BTREE',
      'columns' => 
      array (
        'kid' => 
        array (
          'length' => '',
          'collation' => 'A',
          'null' => false,
        ),
      ),
    ),
    'uid' => 
    array (
      'alias' => 'uid',
      'primary' => false,
      'unique' => false,
      'type' => 'BTREE',
      'columns' => 
      array (
        'uid' => 
        array (
          'length' => '40',
          'collation' => 'A',
          'null' => false,
        ),
      ),
    ),
    'unique' => 
    array (
      'alias' => 'unique',
      'primary' => false,
      'unique' => true,
      'type' => 'BTREE',
      'columns' => 
      array (
        'lid' => 
        array (
          'length' => '',
          'collation' => 'A',
          'null' => false,
        ),
        'kid' => 
        array (
          'length' => '',
          'collation' => 'A',
          'null' => false,
        ),
        'uid' => 
        array (
          'length' => '40',
          'collation' => 'A',
          'null' => false,
        ),
      ),
    ),
  ),
);
