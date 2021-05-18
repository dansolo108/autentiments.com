id: 89
name: dobHook
category: Login
properties: null

-----

$dob = $hook->getValue('dob');
$profile = $modx->user->getOne('Profile');
if (!empty($dob)) {
    $hook->setValue('dob', strtotime($dob));
}
if ($profile->get('dob')) {
    $hook->setValue('dob', $profile->get('dob'));
}
return true;