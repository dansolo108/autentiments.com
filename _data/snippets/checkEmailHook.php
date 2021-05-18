id: 88
name: checkEmailHook
category: Login
properties: null

-----

$username = $hook->getValue('username');

if ($username) {
    $user = $modx->getObject('modUser', ['username' => $username]);
    // Также проверяем группу пользователя, чтобы не раскрывать email админов
    if($user == null || !$user->isMember('Users')) {
        $hook->addError('username', $modx->lexicon('stik_check_email_hook'));
        return false;
    }
}
return true;