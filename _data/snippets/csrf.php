id: 105
name: csrf
category: AjaxForm
properties: 'a:0:{}'

-----

if (request()->checkCsrfToken('post') === false) {
    // Выставляем плейсхолдер ошибки
    $hook->addError('csrf_token', 'Ошибка! Указан некорректный токен.');
    return false;
}
return true;