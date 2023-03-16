<?php
$stikLoyalty = $modx->getService('stik_loyalty', 'stikLoyalty', $modx->getOption('core_path').'components/stik/model/', []);
return $stikLoyalty->userHasFirstOrderDiscount();