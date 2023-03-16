<?php
/** @var modX $modx */
if (!$amo = $modx->getService('amocrm', 'amoCRM', $modx->getOption('amocrm_core_path', null,
        $modx->getOption('core_path') . 'components/amocrm/') . 'model/amocrm/', array())
) {
    return 'Could not load amoCRM class!';
}

$q = $modx->newQuery('modUserProfile');
$q->leftJoin('amoCRMUser', 'amoCRMUser', 'amoCRMUser.user = modUserProfile.internalKey');
$q->where(array('amoCRMUser.user IS null'));
$q->limit(12);
$q->prepare();
// echo $q->toSQL() . PHP_EOL;

/** @var modUserProfile[] $profiles */
$profiles = $modx->getCollection('modUserProfile', $q);
// echo count($naUsers);

foreach ($profiles as $profile) {
    echo $profile->get('email');
    $amo->addContact(
        array(
            'name' => $profile->get('fullname'),
            'email' => $profile->get('email'),
            'phone' => $profile->get('phone')
        ),
        $profile->get('internalKey'));
}