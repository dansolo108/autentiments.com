<?php
$xpdo_meta_map['PolylangTvTmplvars']= array (
  'package' => 'polylang',
  'version' => '1.1',
  'table' => 'polylang_tv_tmplvars',
  'extends' => 'xPDOSimpleObject',
  'tableMeta' => 
  array (
    'engine' => 'InnoDB',
  ),
  'fields' => 
  array (
    'culture_key' => NULL,
    'tmplvarid' => NULL,
    'values' => NULL,
    'default_text' => NULL,
  ),
  'fieldMeta' => 
  array (
    'culture_key' => 
    array (
      'dbtype' => 'varchar',
      'precision' => '100',
      'phptype' => 'string',
      'null' => true,
      'index' => 'index',
    ),
    'tmplvarid' => 
    array (
      'dbtype' => 'int',
      'precision' => '11',
      'phptype' => 'integer',
      'null' => false,
    ),
    'values' => 
    array (
      'dbtype' => 'text',
      'phptype' => 'string',
      'null' => true,
    ),
    'default_text' => 
    array (
      'dbtype' => 'mediumtext',
      'phptype' => 'string',
      'null' => true,
    ),
  ),
  'indexes' => 
  array (
    'culture_key' => 
    array (
      'alias' => 'culture_key',
      'primary' => false,
      'unique' => false,
      'type' => 'BTREE',
      'columns' => 
      array (
        'culture_key' => 
        array (
          'length' => '',
          'collation' => 'A',
          'null' => true,
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
