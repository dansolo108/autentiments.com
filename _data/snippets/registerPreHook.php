id: 101
name: registerPreHook
category: Login
properties: 'a:0:{}'

-----

$joinLoyalty = (bool)$hook->getValue('join_loyalty');
$mobilephone = $hook->getValue('mobilephone');

if ($joinLoyalty && !$mobilephone) {
    $hook->addError('mobilephone', $modx->lexicon('stik_register_mobilephone_error'));
    return false;
}

return true;