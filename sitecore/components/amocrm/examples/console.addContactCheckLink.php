<?php

$managerPath = 'manager';

$field = 'email';
//$field = 'internalKey';
//$field = 'phone';

$values = array(

);

/** @var amoCRM $amo */
$amo = $modx->getService('amocrm');
$amo->auth();

echo 'FIELD: ' . $field;

foreach ($values as $value) {
    $c = array($field => $value);

    if ($profile = $modx->getObject('modUserProfile', $c)) {
        $profile = $profile->toArray();
        echo 'VALUE: ' . $value . ', FULLNAME: ' . $profile['fullname'] . '<br>' . PHP_EOL;
        echo 'USER ID: ' . $profile['internalKey'] . '<br>' . PHP_EOL;
        $link = 'https://mmir.pro/' . $managerPath . '/?a=security/user/update&id=' . $profile['internalKey'];
        echo 'LINK TO FOUND USER PAGE: <a href="' . $link . '">' . $link . ' </a>' . '<br>' . PHP_EOL;
        $amoId = $amo->addContact(
            array(
                'name' => $profile['fullname'],
                'email' => $profile['email'],
                'phone' => $profile['phone'],
            ),
            $profile['internalKey']);
        echo 'AMO ID: ' . $amoId . '<br>' . PHP_EOL;
        if ($link = $modx->getObject('amoCRMUser', array('user_id' => $amoId))) {
            $link = 'https://mmir.pro/' . $managerPath . '/?a=security/user/update&id=' . $link->get('user');
            echo 'LINK TO USER PAGE FROM AMO CONTACT: <a href="' . $link . '">' . $link . ' </a>' . '<br>' . PHP_EOL;
        }
        if ($contacts = $amo->getContacts([], $value)) {
            echo 'AMO CONTACTS WITH ' . $field . ' = ' . $value . ': ' . implode(', ', array_keys($contacts)) . '<br>' . PHP_EOL;
        }
        echo '<br>' . PHP_EOL;
    }
}