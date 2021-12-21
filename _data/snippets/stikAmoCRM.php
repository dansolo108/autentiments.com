id: 109
source: 1
name: stikAmoCRM
description: 'stikAmoCRM snippet to list items'
category: stikAmoCRM
properties: "a:6:{s:3:\"tpl\";a:7:{s:4:\"name\";s:3:\"tpl\";s:4:\"desc\";s:19:\"stikamocrm_prop_tpl\";s:4:\"type\";s:9:\"textfield\";s:7:\"options\";a:0:{}s:5:\"value\";s:19:\"tpl.stikAmoCRM.item\";s:7:\"lexicon\";s:21:\"stikamocrm:properties\";s:4:\"area\";s:0:\"\";}s:6:\"sortby\";a:7:{s:4:\"name\";s:6:\"sortby\";s:4:\"desc\";s:22:\"stikamocrm_prop_sortby\";s:4:\"type\";s:9:\"textfield\";s:7:\"options\";a:0:{}s:5:\"value\";s:4:\"name\";s:7:\"lexicon\";s:21:\"stikamocrm:properties\";s:4:\"area\";s:0:\"\";}s:7:\"sortdir\";a:7:{s:4:\"name\";s:7:\"sortdir\";s:4:\"desc\";s:23:\"stikamocrm_prop_sortdir\";s:4:\"type\";s:4:\"list\";s:7:\"options\";a:2:{i:0;a:2:{s:4:\"text\";s:3:\"ASC\";s:5:\"value\";s:3:\"ASC\";}i:1;a:2:{s:4:\"text\";s:4:\"DESC\";s:5:\"value\";s:4:\"DESC\";}}s:5:\"value\";s:3:\"ASC\";s:7:\"lexicon\";s:21:\"stikamocrm:properties\";s:4:\"area\";s:0:\"\";}s:5:\"limit\";a:7:{s:4:\"name\";s:5:\"limit\";s:4:\"desc\";s:21:\"stikamocrm_prop_limit\";s:4:\"type\";s:11:\"numberfield\";s:7:\"options\";a:0:{}s:5:\"value\";i:10;s:7:\"lexicon\";s:21:\"stikamocrm:properties\";s:4:\"area\";s:0:\"\";}s:15:\"outputSeparator\";a:7:{s:4:\"name\";s:15:\"outputSeparator\";s:4:\"desc\";s:31:\"stikamocrm_prop_outputSeparator\";s:4:\"type\";s:9:\"textfield\";s:7:\"options\";a:0:{}s:5:\"value\";s:1:\"\n\";s:7:\"lexicon\";s:21:\"stikamocrm:properties\";s:4:\"area\";s:0:\"\";}s:13:\"toPlaceholder\";a:7:{s:4:\"name\";s:13:\"toPlaceholder\";s:4:\"desc\";s:29:\"stikamocrm_prop_toPlaceholder\";s:4:\"type\";s:13:\"combo-boolean\";s:7:\"options\";a:0:{}s:5:\"value\";b:0;s:7:\"lexicon\";s:21:\"stikamocrm:properties\";s:4:\"area\";s:0:\"\";}}"
static_file: core/components/stikamocrm/elements/snippets/stikamocrm.php

-----

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