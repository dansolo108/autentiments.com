<?php
define(MODX_API_MODE,true);
require_once dirname(__FILE__, 4) . '/index.php';

print $modx->runSnippet('cdekDeliveryPoints');
exit;