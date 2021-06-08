id: 102
name: registerAmoCrmId
category: Login
properties: 'a:0:{}'

-----

$amoUserid = $_SESSION['amo_userid'];
if ($amoUserid) {
    $profile = $hook->getValue('register.profile');
    $profile->set('amo_userid', $amoUserid);
    $profile->save();
    
    $contact = $modx->newObject('amoCRMUser', array('user' => $profile->get('internalKey'), 'user_id' => $amoUserid));
    $contact->save();
}
return true;