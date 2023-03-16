<?php
$xpdo_meta_map['extSmsCode']= array (
  'package' => 'sms',
  'version' => '1.1',
  'table' => 'sms_code',
  'extends' => 'xPDOSimpleObject',
  'fields' => 
  array (
    'phone' => '',
    'code' => '',
    'service' => '',
    'mode' => '',
    'properties' => '[]',
    'ip' => '0.0.0.0',
    'client' => '',
    'createdon' => '0000-00-00 00:00:00',
    'used' => 0,
    'attempt' => 0,
  ),
  'fieldMeta' => 
  array (
    'phone' => 
    array (
      'dbtype' => 'varchar',
      'precision' => '250',
      'phptype' => 'string',
      'null' => true,
      'default' => '',
    ),
    'code' => 
    array (
      'dbtype' => 'text',
      'phptype' => 'string',
      'null' => true,
      'default' => '',
    ),
    'service' => 
    array (
      'dbtype' => 'varchar',
      'precision' => '50',
      'phptype' => 'string',
      'null' => true,
      'default' => '',
    ),
    'mode' => 
    array (
      'dbtype' => 'varchar',
      'precision' => '250',
      'phptype' => 'string',
      'null' => true,
      'default' => '',
    ),
    'properties' => 
    array (
      'dbtype' => 'text',
      'phptype' => 'json',
      'null' => true,
      'default' => '[]',
    ),
    'ip' => 
    array (
      'dbtype' => 'varchar',
      'precision' => '20',
      'phptype' => 'string',
      'null' => true,
      'default' => '0.0.0.0',
    ),
    'client' => 
    array (
      'dbtype' => 'text',
      'phptype' => 'string',
      'null' => true,
      'default' => '',
    ),
    'createdon' => 
    array (
      'dbtype' => 'datetime',
      'phptype' => 'string',
      'null' => false,
      'default' => '0000-00-00 00:00:00',
    ),
    'used' => 
    array (
      'dbtype' => 'tinyint',
      'precision' => '1',
      'phptype' => 'boolean',
      'null' => false,
      'default' => 0,
    ),
    'attempt' => 
    array (
      'dbtype' => 'int',
      'precision' => '10',
      'phptype' => 'integer',
      'null' => false,
      'default' => 0,
    ),
  ),
  'indexes' => 
  array (
    'mode' => 
    array (
      'alias' => 'mode',
      'primary' => false,
      'unique' => false,
      'type' => 'BTREE',
      'columns' => 
      array (
        'mode' => 
        array (
          'length' => '',
          'collation' => 'A',
          'null' => false,
        ),
      ),
    ),
  ),
);
