<?php
/** @var modX $modx */
/** @var array $scriptProperties */
/** @var fiHooks $hook */
/** @var array $formFields */
/** @var amoCRM $amo */
$nameFields = $modx->getOption('amoCRMNameField', $scriptProperties, null);

$formFields = array();
if (!empty($nameFields)) {
    $nameFields = explode('||', $nameFields);
    foreach ($nameFields as & $field) {
        $field = explode('==', $field);
        $formFields[$field[1]] = $field[0];
    }
}

if (!$amo = $modx->getService('amocrm', 'amoCRM', $modx->getOption('amocrm_core_path', null,
        $modx->getOption('core_path') . 'components/amocrm/') . 'model/amocrm/', $scriptProperties)
) {
    return 'Could not load amoCRM class!';
}

$formValues = $hook->getValues();
$data = array();

foreach ($formValues as $key => $value) {
    if (in_array($key, array_keys($formFields))) {
        $data[$formFields[$key]] = $value;
    } elseif (in_array($key, array('name', 'phone', 'email'))) {
        $data[$key] = $value;
    }
}


$class = $scriptProperties['class'] = 'glCity';
$limit = $scriptProperties['limit'] = 10;
$offset = $scriptProperties['offset'] = 0;
$sortby = $scriptProperties['sortby'] = 'name_ru';
$sortdir = $scriptProperties['sortdir'] = 'ASC';
$outputSeparator = $scriptProperties['outputSeparator'] = $modx->getOption('outputSeparator', $scriptProperties, "\n", true);
/** @var gl $gl */
if (!$gl = $modx->getService('gl', 'gl',
    $modx->getOption('gl_core_path', null, $modx->getOption('core_path') . 'components/gl/') . 'model/gl/',
    array())
) {
    return 'Could not load gl class!';
}

$gl->initialize($context, $scriptProperties);
$rows = array();
$q = $modx->newQuery($class);
$q->where(array('active'  => 1));
$q->limit($limit, $offset);
$q->sortby($sortby, $sortdir);
$q->select($modx->getSelectColumns($class, $class));
if ($q->prepare() AND $q->stmt->execute()) {
    $rows = $q->stmt->fetchAll(PDO::FETCH_ASSOC);
}

// Для сравнения логина с email региона
$dataEmail = $gl->opts['current']['data']['email'];
// Для сравнения обозначения региона по ISO
$isoRegion = $gl->opts['current']['region']['iso'];
// Получение массива соответствий ISO обозначений регионов и пользователей в amoCRM
// Массив хранится в JSON в системной настройке amocrm_geo_users, которую необходимо создать
// Формат: {"<amo@login.email":"<GL ISO REGION>"}
// Пример: {"email1@domain.com":"RU-SPE"}
$geoUsers = $modx->fromJSON($modx->getOption('amocrm_geo_users', null, '{}'));
$login = '';
foreach ($geoUsers as $loginTemp => $isoArray) {
    $isoArray = array_map('trim', explode(',', $isoArray));
    if (in_array($isoRegion, $isoArray)) {
        $login = $loginTemp;
        break;
    }
}

foreach ($amo->account_config['out']['account']['users'] as $user) {
    // Поиск соответствия по JSON-массиву в системной настройке
    $compare = ($user['login'] == $login);
    // Поиск соответствия по совпадению логина пользователя amoCRM с email региона
    // $compare = ($user['login'] == $dataEmail;
    if ($compare) {
        // Установка ответственного
        $data['responsible_user_id'] = (string) $user['id'];
        break;
    }
}

return $amo->addForm($data);