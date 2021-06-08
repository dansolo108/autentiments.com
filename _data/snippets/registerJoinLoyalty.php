id: 95
name: registerJoinLoyalty
category: Login
properties: 'a:0:{}'

-----

$joinLoyalty = (bool)$hook->getValue('join_loyalty');
$mobilephone = $hook->getValue('mobilephone');
if ($joinLoyalty) {
    $profile = $hook->getValue('register.profile');
    $profile->set('join_loyalty', 1);
    $profile->set('mobilephone', $mobilephone);
    $profile->save();
    
    if ($profile->get('mobilephone')) {
        $user = $hook->getValue('register.user');
        $maxma = $modx->getService('maxma', 'maxma', $modx->getOption('core_path').'components/stik/model/', []);
        $maxma->createNewClient([
            'phoneNumber' => $profile->get('mobilephone'),
            'email' => $profile->get('email'),
            'surname' => $profile->get('surname'),
            'name' => $profile->get('name'),
            'externalId' => 'modx'.$user->get('id'),
        ]);
    }
}
return true;