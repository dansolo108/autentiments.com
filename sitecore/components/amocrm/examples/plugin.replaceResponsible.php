<?php
if (!function_exists('setValues')) {
    function setValues(modX $modx, $returned, $city)
    {
        // $modx->log(1, 'SET VALUES. BEGIN. CITY: ' . $city);
        if ($ctx = $modx->getObject('modContext', array('name' => $city))) {
            // $modx->log(1, 'SET VALUES. CONTEXT FOUND: ' . $ctx->get('key'));
            if ($setting = $modx->getObject('modContextSetting',
                array('context_key' => $ctx->key, 'key' => 'amocrm_default_responsible_user_id'))) {

                // $modx->log(1, 'SET VALUES. SETTING FOUND: ' . $setting->get('key') . ', ' . $setting->get('value'));
                $returned['responsible_user_id'] = $setting->get('value');
            }
        }

        // $modx->log(1, 'SET VALUES. END. RETURNED: ' . print_r($returned, 1));

        return $returned;
    }
}

switch ($modx->event->name) {
    case 'amocrmOnBeforeUserSend':

        $result = & $scriptProperties['__returnedValues'];
        if (!is_array(($result))) {
            $result = array();
        }
        if (!is_array($result['contact'])) {
            $result['contact'] = array();
        }
        if (isset($contact['responsible_user_id']) and substr('++++', $contact['responsible_user_id']) === 0) {
            $result['contact']['responsible_user_id'] = str_replace('++++', '', $contact['responsible_user_id']);
        }
        elseif (!empty($modUserId) and $profile = $modx->getObject('modUserProfile', array('internalKey' => $modUserId))) {
            $result['contact'] = setValues($modx, $result['contact'], $profile->get('city'));
        }
        break;

    case 'amocrmOnBeforeOrderSend':
        /** @var msOrder $msOrder */
        /** @var array $lead */
        $result = & $scriptProperties['__returnedValues'];
        // $modx->log(1, 'REPLACE RESPONSIBLE FOR ORDER. BEGIN. RESULT: ' . print_r($result, 1));
        if (!is_array(($result))) {
            $result = array();
        }
        if (!is_array(($result['lead']))) {
            $result['lead'] = array();
        }
        // $modx->log(1, 'REPLACE RESPONSIBLE FOR ORDER. PREPARED. RESULT: ' . print_r($result, 1));
        if (isset($lead['responsible_user_id']) and substr('++++', $lead['responsible_user_id']) === 0) {
            $result['lead']['responsible_user_id'] = str_replace('++++', '', $lead['responsible_user_id']);
            // $modx->log(1, 'REPLACE RESPONSIBLE FOR ORDER. ++++ FOUND. RESULT: ' . print_r($result, 1));
        }
        elseif ($msOrder and $profile = $msOrder->getOne('UserProfile')) {
            $result['lead'] = setValues($modx, $result['lead'], $profile->get('city'));
            // $modx->log(1, 'REPLACE RESPONSIBLE FOR ORDER. PROFILE FOUND. RESULT: ' . print_r($result, 1));
        }
        // else {
        //     $modx->log(1, 'REPLACE RESPONSIBLE FOR ORDER. TYPE OF ORDER: ' . gettype($msOrder));
        //     if ($msOrder) {
        //         $modx->log(1, 'REPLACE RESPONSIBLE FOR ORDER. ORDER: ' . print_r($msOrder->toArray(), 1));
        //     }
        // }

        // $modx->log(1, 'REPLACE RESPONSIBLE FOR ORDER. END. RESULT: ' . print_r($result, 1));


        break;
}