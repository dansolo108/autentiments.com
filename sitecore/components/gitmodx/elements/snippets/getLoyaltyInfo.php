<?php
$stikLoyalty = $modx->getService('stik_loyalty','stikLoyalty', $modx->getOption('core_path').'components/stik/model/', []);
if (!($stikLoyalty instanceof stikLoyalty)) return '';

$info = $stikLoyalty->getInfo();

if ($info) {
    $modx->setPlaceholders($info, 'loyalty.');
}

return;