<?php
/** @var modX $modx */
/** @var array $scriptProperties */
/** @var fiHooks $hook */
/** @var array $formFields */
/** @var amoCRM $amo */
$nameFields = 'name==contact_name||phone==contact_phone||email==contact_email||291505==contact_from||where==contact_where||what==contact_what';

// echo print_r($formFields, 1);

if (!$amo = $modx->getService('amocrm', 'amoCRM', $modx->getOption('amocrm_core_path', null,
        $modx->getOption('core_path') . 'components/amocrm/') . 'model/amocrm/', array())
) {
    return 'Could not load amoCRM class!';
}

$formFields = $amo->parseFieldsSet($nameFields);
$formValues = array(

    'contact_name' => 'test1',
    'contact_email' => 'test@email.com',
    'contact_phone' => '33423423',
    'contact_from' => 'ewe',
    'contact_where' => 'erwre',
    'contact_what' => 'ewrwrew',
    'fz152' => 'on',
    'submitTop' => 'Рассчитать'
);

// $hook->getValues();

$data = array();

foreach ($formValues as $key => $value) {
    if (in_array($key, array_keys($formFields))) {
        $data[$formFields[$key]] = $value;
    } elseif (in_array($key, array('name', 'phone', 'email'))) {
        $data[$key] = $value;
    }
}

//echo print_r($data, 1);

//    echo print_r($amo->account_config, 1);
echo $amo->addForm($data);

return true;