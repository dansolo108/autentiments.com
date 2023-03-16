<?php

return array(
    'fields' => array(
        'hexcolor' => 0,
    ),
    'fieldMeta' => array(
        'hexcolor' => array(
            'dbtype' => 'int',
            'precision' => '10',
            'attributes' => 'unsigned',
            'phptype' => 'integer',
            'null' => false,
            'default' => 0,
        ),
    ),
    'indexes' => array (
      'alias' => 'hexcolor',
      'primary' => false,
      'unique' => false,
      'type' => 'BTREE',
      'columns' => 
      array (
        'hexcolor' => 
        array (
          'length' => '',
          'collation' => 'A',
          'null' => false,
        ),
      ),
    ),
);