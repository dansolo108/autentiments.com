<?php

if (!isset($_SESSION['UTM'])) {
    $_SESSION['UTM'] = array();
}
$sessionUTM = & $_SESSION['UTM'];

switch ($modx->event->name) {

    case 'OnMODXInit':
        if ($modx->context->key != 'mgr') {
            $utm_source = $modx->getOption('utm_source', $_GET, '');
            $utm_medium = $modx->getOption('utm_medium', $_GET, '');
            $utm_campaign = $modx->getOption('utm_campaign', $_GET, '');

                if (($utm_campaign or $utm_medium or $utm_source) and empty($sessionUTM)) {
                $sessionUTM = array(
                    'utm_source' => $utm_source,
                    'utm_medium' => $utm_medium,
                    'utm_campaign' => $utm_campaign,
                );
            }
        }

        break;

    case 'msOnBeforeCreateOrder':
    case 'PasOnBeforeCreateOrder':

        /** @var msOrder $msOrder */
        /** @var amoCRM $amoCRM */
        $amoCRM = $modx->getService('amocrm');
        $orderProps = $amoCRM->tools->mergeOrderOptions(
            $msOrder->get('properties'),
            array(
                '1939355' =>$sessionUTM['utm_source'],
                '1939357' => $sessionUTM['utm_medium'],
                '1939359' => $sessionUTM['utm_campaign']
            )
            // array(
            //     '1229368' =>$sessionUTM['utm_source'],
            //     '1229372' => $sessionUTM['utm_medium'],
            //     '1229370' => $sessionUTM['utm_campaign']
            // )
        );

        $msOrder->set('properties', $orderProps);

        break;

    case 'OnUserProfileBeforeSave':
        if (empty($userprofile->get['id']) and !empty($sessionUTM)) {

            $extended = $userprofile->get('extended');
            $extended['amoCRMFields']['custom_fields'] = array(
                array('id' => 1229368, 'values' => array(array('value' => $sessionUTM['utm_source']))),
                array('id' => 1229372, 'values' => array(array('value' => $sessionUTM['utm_medium']))),
                array('id' => 1229370, 'values' => array(array('value' => $sessionUTM['utm_campaign'])))
            );
            $userprofile->set('extended', $extended);

        }
}