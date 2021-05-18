<?php
$xpdo_meta_map['PolylangTv']= array (
  'package' => 'polylang',
  'version' => '1.1',
  'table' => 'polylang_tv',
  'extends' => 'xPDOSimpleObject',
  'tableMeta' => 
  array (
    'engine' => 'InnoDB',
  ),
  'fields' => 
  array (
    'content_id' => NULL,
    'culture_key' => NULL,
    'tmplvarid' => NULL,
    'value' => NULL,
  ),
  'fieldMeta' => 
  array (
    'content_id' => 
    array (
      'dbtype' => 'int',
      'precision' => '11',
      'phptype' => 'integer',
      'null' => false,
      'index' => 'index',
    ),
    'culture_key' => 
    array (
      'dbtype' => 'varchar',
      'precision' => '100',
      'phptype' => 'string',
      'null' => false,
    ),
    'tmplvarid' => 
    array (
      'dbtype' => 'int',
      'precision' => '11',
      'phptype' => 'integer',
      'null' => false,
    ),
    'value' => 
    array (
      'dbtype' => 'text',
      'phptype' => 'string',
      'null' => false,
    ),
  ),
  'indexes' => 
  array (
    'content_id' => 
    array (
      'alias' => 'content_id',
      'primary' => false,
      'unique' => false,
      'type' => 'BTREE',
      'columns' => 
      array (
        'content_id' => 
        array (
          'length' => '',
          'collation' => 'A',
          'null' => false,
        ),
        'culture_key' => 
        array (
          'length' => '',
          'collation' => 'A',
          'null' => false,
        ),
        'tmplvarid' => 
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
    'SiteTmplvarTemplates' => 
    array (
      'class' => 'modTemplateVarTemplate',
      'local' => 'tmplvarid',
      'foreign' => 'tmplvarid',
      'cardinality' => 'one',
      'owner' => 'foreign',
    ),
    'Resource' => 
    array (
      'class' => 'modResource',
      'local' => 'content_id',
      'foreign' => 'id',
      'cardinality' => 'one',
      'owner' => 'foreign',
    ),
    'PolylangLanguage' => 
    array (
      'class' => 'PolylangLanguage',
      'local' => 'culture_key',
      'foreign' => 'culture_key',
      'cardinality' => 'one',
      'owner' => 'foreign',
    ),
  ),
);
