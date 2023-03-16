<?php
$xpdo_meta_map['msfFavoriteList']= array (
  'package' => 'msfavorites',
  'version' => '1.1',
  'table' => 'msf_favorite_lists',
  'extends' => 'xPDOSimpleObject',
  'tableMeta' => 
  array (
    'engine' => 'InnoDB',
  ),
  'fields' => 
  array (
    'list' => NULL,
    'type' => NULL,
  ),
  'fieldMeta' => 
  array (
    'list' => 
    array (
      'dbtype' => 'varchar',
      'precision' => '40',
      'phptype' => 'string',
      'null' => false,
    ),
    'type' => 
    array (
      'dbtype' => 'varchar',
      'precision' => '40',
      'phptype' => 'string',
      'null' => false,
    ),
  ),
  'indexes' => 
  array (
    'list' => 
    array (
      'alias' => 'list',
      'primary' => false,
      'unique' => false,
      'type' => 'BTREE',
      'columns' => 
      array (
        'list' => 
        array (
          'length' => '40',
          'collation' => 'A',
          'null' => false,
        ),
      ),
    ),
    'type' => 
    array (
      'alias' => 'type',
      'primary' => false,
      'unique' => false,
      'type' => 'BTREE',
      'columns' => 
      array (
        'type' => 
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
        'list' => 
        array (
          'length' => '40',
          'collation' => 'A',
          'null' => false,
        ),
        'type' => 
        array (
          'length' => '40',
          'collation' => 'A',
          'null' => false,
        ),
      ),
    ),
  ),
  'composites' => 
  array (
    'FavoriteObject' => 
    array (
      'class' => 'msfFavoriteObject',
      'local' => 'id',
      'foreign' => 'lid',
      'cardinality' => 'many',
      'owner' => 'local',
    ),
  ),
  'validation' => 
  array (
    'rules' => 
    array (
      'list' => 
      array (
        'preventBlank' => 
        array (
          'type' => 'xPDOValidationRule',
          'rule' => 'xPDOMinLengthValidationRule',
          'value' => '1',
          'message' => 'msfavorites_err_value',
        ),
      ),
      'type' => 
      array (
        'preventBlank' => 
        array (
          'type' => 'xPDOValidationRule',
          'rule' => 'xPDOMinLengthValidationRule',
          'value' => '1',
          'message' => 'msfavorites_err_value',
        ),
      ),
    ),
  ),
);
