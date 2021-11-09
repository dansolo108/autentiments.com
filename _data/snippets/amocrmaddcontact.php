id: 71
source: 1
name: amocrmaddcontact
category: amocrm
properties: 'a:0:{}'
static_file: core/components/amocrm/elements/snippets/amocrm.php

-----

/** @var modX $modx */
/** @var array $scriptProperties */
/** @var fiHooks $hook */
/** @var array $formFields */
/** @var amoCRM $amo */
$modxAmoFieldsEq = $modx->getOption('amoCRMmodxAmoFieldsEq', $scriptProperties, null);
$nameFields = $modx->getOption('amoCRMNameField', $scriptProperties, null);

$connectParams = array(
    'account' => $modx->getOption('amoCRMAccount', $scriptProperties, ''),
    'pipeline' => $modx->getOption('amoCRMPipelineId', $scriptProperties, ''),
    'form_pipeline' => $modx->getOption('amoCRMFormPipelineId', $scriptProperties, ''),
    'form_status_new' => $modx->getOption('amoCRMFormStatusNew', $scriptProperties, ''),
    'form_filled_fields' => $modx->getOption('amoCRMFormFilledFields', $scriptProperties, ''),
);
foreach ($connectParams as $param => $v) {
    if (empty($v)) {
        unset($connectParams[$param]);
    }
}
$amoParams = array_merge($connectParams, $scriptProperties);
$hash = md5($modx->toJSON($connectParams));

if (!$amo = $modx->getService('amocrm' . $hash, 'amoCRM', $modx->getOption('amocrm_core_path', null,
        $modx->getOption('core_path') . 'components/amocrm/') . 'model/amocrm/', $amoParams)
) {
    return 'Could not load amoCRM class!';
}

if (!empty($modxAmoFieldsEq)) {
    $formFields = $amo->parseFieldsSet($modxAmoFieldsEq);
} else {
    $fieldsSet = explode('||', $nameFields);
    foreach ($fieldsSet as & $field) {
        $field = explode('==', $field);
        $formFields[$field[1]] = $field[0];
    }
}
$formValues = $hook->getValues();
$data = array();

foreach ($formValues as $key => $value) {
    if (in_array($key, array_keys($formFields))) {
        $data[$formFields[$key]] = $value;
    } elseif (in_array($key, $amo->config['defaultUserFields'])) {
        $data[$key] = $value;
    }
}
if (!empty($amoParams)) {
    $data['amoCRMcomponentParams'] = $connectParams;
}

return (bool)$amo->addForm($data, true);