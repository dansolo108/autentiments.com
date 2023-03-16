<?php

return array(
    'map' => array(
        'msProductData' => require_once 'msproductdata.inc.php',
        'msOrderAddress' => require_once 'msOrderAddress.inc.php',
        'msDelivery' => require_once 'msDelivery.inc.php',
    ),
    'manager' => array(
        'msProductData' => MODX_ASSETS_URL . 'components/minishop2CustomFields/msproductdata.js',
    ),
);