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
        $values['lead']['custom_fields'][] = [
            'id' => 1981421,
            'values' => [['value' => $lead['payment'] ]],
        ];

        break;
}