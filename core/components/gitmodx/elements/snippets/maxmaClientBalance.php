<?php
$maxma = $modx->getService('maxma', 'maxma', $modx->getOption('core_path').'components/stik/model/', []);

if ($id) {
    return $maxma->getClientBalanceByExternalId($id);
} elseif ($phone) {
    return $maxma->getClientBalanceByPhone($phone);
}

return 0;