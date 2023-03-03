<?php

$extBlockKey = 'amoCRMBlockChanges';

switch ($modx->event->name) {
    case 'amocrmOnBeforeUserSend':
        /** @var modUserProfile $userProfile */
        /** @var int $modUserId */
        /** @var int $amoUserId */
        /** @var amoCRM $amoCRM */
        /** @var array $contact */
        /** @var array $amoContacts */
        $amoContact = array('custom_fields' => array());
        if ($amoUserId) {
            $amoContacts = $amoCRM->getContacts(array($amoUserId));
            $amoContact = array_shift($amoContacts);
        }

        $enumTypes = array();
        foreach ($amoCRM->account_config['out']['account']['custom_fields']['contacts'] as $field) {
            if (isset($field['enums'])) {
                $id = $field['id'];
                $enumTypes[$field['id']] = array_values($field['enums']);
            }
        }
//        $modx->log(1, 'COLLECT ENUM FIELDS. ENUM TYPES: ' . print_r($enumTypes, 1));

        $values = & $scriptProperties['__returnedValues'];
        if (!is_array($values)) {
            $values = array();
        }
        if (!isset($values['contact']) or !is_array($values['contact'])) {
            $values['contact'] = array();
        }

//        $modx->log(1, 'COLLECT ENUM FIELDS. INBOUND CONTACT: ' . print_r($contact, 1));
//        $modx->log(1, 'COLLECT ENUM FIELDS. AMO CONTACT: ' . print_r($amoContact, 1));
        $phoneFields = array(1014757);
        $workContact = array_merge($contact, $values['contact']);

        foreach ($workContact['custom_fields'] as & $custom_field) {

            $cfId = $custom_field['id'];
//            $modx->log(1, 'COLLECT ENUM FIELDS. FIELD ID: ' . $cfId);

            if (isset($enumTypes[$cfId])) {
//                $modx->log(1, 'COLLECT ENUM FIELDS. FIELD ' . $cfId . ' IS ENUM');

                $value = & $custom_field['values'][0]['value'];
                $enums = $amoCRM->tools->deleteFromArray($value, $enumTypes[$cfId]);
                $amoCurrentValues = array();
//                $modx->log(1, 'COLLECT ENUM FIELDS. ENUMS: ' . print_r($enums, 1));

                if (in_array($cfId, $phoneFields)) {
                    $value = $amoCRM->tools->normalizeRusPhone($value);
//                    $modx->log(1, 'COLLECT ENUM FIELDS. NORMALIZED PHONE : ' . $value);
                }

                $values = array($value);

                foreach ($amoContact['custom_fields'] as $amoCustomField) {

                    if ($amoCustomField['id'] == $cfId) {

//                        $modx->log(1, 'COLLECT ENUM FIELDS. FIELD ID: ' . $cfId . 'AMO CUSTOM FIELD: ' . print_r($amoCustomField, 1));

                        foreach ($amoCustomField['values'] as $amoVal) {

                            $amoValue = $amoVal['value'];
                            $enum = array_shift($enums);

//                            $modx->log(1, 'COLLECT ENUM FIELDS. AMO VALUE: ' . $amoValue);
//                            $modx->log(1, 'COLLECT ENUM FIELDS. ENUM: ' . $enum);
                            if (!$enum) {
                                break;
                            }

                            if (in_array($cfId, $phoneFields)) {
                                $amoValue = $amoCRM->tools->normalizeRusPhone($amoValue);
                            }

                            if (!in_array($amoValue, $values)) {

                                $values[] = $amoValue;
                                $custom_field['values'][]  = array(
                                    'value' => $amoValue,
                                    'enum' => $enum,
                                );

                            }
//                            $modx->log(1, 'COLLECT ENUM FIELDS. NORMALIZED AMO PHONE : ' . $amoValue['value']);
                        }
                        break;
                    }
                }
            }
        }

        $values['contact']['custom_fields'] = $workContact['custom_fields'];

//        $modx->log(1, 'PREPARED CONTACT: ' . print_r($values['contact'], 1));
//        $modx->log(1, 'FOUND AMO CONTACT: ' . print_r($amoContact, 1));

        break;


}