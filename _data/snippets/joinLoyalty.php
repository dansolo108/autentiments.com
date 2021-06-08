id: 96
name: joinLoyalty
category: AjaxForm
properties: 'a:0:{}'

-----

if (!$_POST['submitted']) return false;

$join = $_POST['join_loyalty'] ? 1 : 0;

$errors = array();

$profile = $modx->user->getOne('Profile');

if (!$profile) {
    return $AjaxForm->error($modx->lexicon('join_loyalty_form_auth_error'));
}

if (!$profile->get('mobilephone')) {
    return $AjaxForm->error($modx->lexicon('join_loyalty_form_phone_error'));
}

$profile->set('join_loyalty', $join);
$profile->save();

$maxma = $modx->getService('maxma', 'maxma', $modx->getOption('core_path').'components/stik/model/', []);
$maxma->createNewClient([
    'phoneNumber' => $profile->get('mobilephone'),
    'email' => $profile->get('email'),
    'surname' => $profile->get('surname'),
    'name' => $profile->get('name'),
    'externalId' => 'modx'.$modx->user->get('id'),
]);

return $AjaxForm->success($modx->lexicon('join_loyalty_success'));