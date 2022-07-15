<?php
/** @var modX $modx */
/** @var array $scriptProperties */
/** @var stikAmoCRM $stikAmoCRM */
$stikAmoCRM = $modx->getService('stikAmoCRM', 'stikAmoCRM', MODX_CORE_PATH . 'components/stikamocrm/model/', $scriptProperties);
if (!$stikAmoCRM) {
    return 'Could not load stikAmoCRM class!';
}

$amoFields = $modx->getOption('amoFields', $scriptProperties, null);

if ($amoFields) {
    $formFields = [];
    $fieldsSet = explode('||', $amoFields);
    foreach ($fieldsSet as & $field) {
        $field = explode('==', $field);
        $formFields[$field[0]] = $field[1];
    }

    $formValues = $hook->getValues();

    $stikAmoCRM->createFormLead($formValues, $formFields);
}