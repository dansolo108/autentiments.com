<?php

$extBlockKey = 'amoCRMBlockChanges';

switch ($modx->event->name) {
    case 'amocrmOnBeforeUserSend':
        /** @var modUserProfile $userProfile */
        /** @var int $modUserId */
        if ($userProfile = $modx->getObject('modUserProfile', array('internalKey' => $modUserId))) {
            $extended = $userProfile->get('extended');
            if (!empty($extended[$extBlockKey]) and !empty($extended[$extBlockKey]['name'])) {
                if ($link = $modx->getObject('amoCRMUser', array('user' => $modUserId))) {
                    /** @var amoCRM $amoCRM */
                    $amoUsers = $amoCRM->getContacts($link->get('user_id'));
                    $amoUser = array_shift($amoUsers);
                    if (!empty($amoUser)) {
                        $values = & $scriptProperties['__returnedValues'];
                        if (!is_array($values)) {
                            $values = array();
                        }
                        if (!isset($values['contact']) or !is_array($values['contact'])) {
                            $values['contact'] = array();
                        }
                        $values['contact']['name'] = $amoUser['name'];
                    }
                }
            }
        }
//        $modx->log(1, 'PREPARED CONTACT: ' . print_r($values['contact'], 1));
        break;

    case 'amocrmOnBeforeWebhookProcess':
        if (!empty($webhookData['contacts'])) {
            $contacts = array();

            $values = &$modx->event->returnedValues;
            if (!is_array($values)) {
                $values = array();
            }
            if (!isset($values['contacts']) or !is_array($values['contacts'])) {
                $values['contacts'] = array();
            }

            foreach (array('add', 'update') as $action) {
//                $modx->log(1, 'amo block wh. CONTACT ACTION ' . $action);
                if (!empty($webhookData['contacts'][$action])) {
//                    $modx->log(1, 'amo block wh. FOUND CONTACT ACTION ' . $action);
                    foreach ($webhookData['contacts'][$action] as & $contact) {
//                        $modx->log(1, 'amo block wh. CONTACT FROM AMO ' . print_r($contact, 1));
                        /** @var modUserProfile $userProfile */
                        if (
                            $link = $modx->getObject(
                                'amoCRMUser',
                                array('user_id' => $contact['id'])
                            )
                            and $userProfile = $link->getOne('UserProfile')
                        ) {
//                            $modx->log(1, 'amo block wh. CONTACT NAME AMO ' . $contact['name'] . ', USER FULLLNAME: ' . $userProfile->get('fullname'));

                            if ($userProfile->get('fullname') != $contact['name']) {
//                                $modx->log(1, 'amo block wh. NAMES NOT EQUAL');
//                                $contact['name'] = $userProfile->get('fullname');

                                $extended = $userProfile->get('extended');
                                if (empty($extended[$extBlockKey]) or empty($extended[$extBlockKey]['name'])) {
//                                    $modx->log(1, 'amo block wh. BLOCK NOT FOUND. SETTING');
                                    if (!is_array($extended[$extBlockKey])) {
                                        $extended[$extBlockKey] = array();
                                    }
                                    $extended[$extBlockKey]['name'] = true;
                                    $userProfile->set('extended', $extended);
                                    $userProfile->save();
                                }
                            }
                        }
                    }
                }
            }
//            $modx->log(1, 'amo block wh. FINAL WEBHOOK DATA: ' . print_r($webhookData, 1));
//            $values['webhookData'] = $webhookData;
        }
        break;


    case 'OnBeforeUserFormSave':
        /** @var amoCRM $amo */
        if (!$amo = $modx->getService('amocrm', 'amoCRM', $modx->getOption('amocrm_core_path', null,
                $modx->getOption('core_path') . 'components/amocrm/') . 'model/amocrm/', array())
        ) {
            return 'Could not load amoCRM class!';
        }
        if (!$amo->isWebhookMode()) {
            return;
        }
        /** @var modUserProfile $profile */
        $profile = $object->getOne('Profile');
        $extended = $profile->get('extended');
        if (
            !empty($extended[$extBlockKey])
            and !empty($extended[$extBlockKey]['name'])
            and $originalProfile = $modx->getObject('modUserProfile', array('internalKey' => $object->get('id')))
        ) {
            $profile->set('fullname', $originalProfile->get('fullname'));
        }
}