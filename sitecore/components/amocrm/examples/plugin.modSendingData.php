<?php
/** @var modX $modx */
$propsElem = $modx->getOption('amocrm_order_properties_element', 'amoCRMFields');
switch ($modx->event->name) {
    case 'amocrmOnBeforeOrderSend':

        $prefixName = '';

        /** @var msOrder $msOrder */
        /** @var msOrderProduct[] $goods */

        $values = & $scriptProperties['__returnedValues'];

        if (!is_array($values)) {
            $values = array();
        }
        if (!isset($values['lead']) or !is_array($values['lead'])) {
            $values['lead'] = array();
        }
        if (!isset($values['lead']['custom_fields']) or !is_array($values['lead']['custom_fields'])) {
            $values['lead']['custom_fields'] = array();
        }
        $form_name = '';

        foreach ($lead['custom_fields'] as $field) {
            if ($field['id'] == 567901) {
                $modx->log(1, 'PLUGIN. VALUE ARRAY: ' . print_r($field, 1));
                $form_name = $field['values'][0]['value'];
            }
        }
        $values['lead']['name'] = 'Medic dpo ' . $form_name;
        $values['lead']['custom_fields'][] = [
            'id' => 374445,
            'values' => [['value' => 'Заявка с сайта']],
        ];
        $values['lead']['custom_fields'][] = [
            'id' => 374451,
            'values' => [['value' => date('d.m.Y H:i:s')]],
        ];



        break;
    case 'amocrmOnBeforeUserSend':

        $prefixName = '';

        /** @var msOrder $msOrder */
        /** @var msOrderProduct[] $goods */

        $values = & $scriptProperties['__returnedValues'];

        if (!is_array($values)) {
            $values = array();
        }
        if (!isset($values['contact']) or !is_array($values['contact'])) {
            $values['contact'] = array();
        }
        // if (!isset($values['contact']['custom_fields']) or !is_array($values['contact']['custom_fields'])) {
        //     $values['contact']['custom_fields'] = array();
        // }
        $form_name = '';
        if (empty($contact['name'])) {
//            $parts = array();
//            foreach ($contact['custom_fields'] as $field) {
//                if ($field['id'] == 371903 || $field['id'] == 371905) {
//                    $modx->log(1, 'PLUGIN. VALUE ARRAY: ' . print_r($field, 1));
//                    $parts[] = $field['values'][0]['value'];
//                }
//            }
//            $values['contact']['name'] = implode(' ', $parts);
            $values['contact']['name'] = 'Имя не указано';
        }

        break;
}