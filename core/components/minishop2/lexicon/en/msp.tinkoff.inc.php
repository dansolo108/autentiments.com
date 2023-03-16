<?php

$_lang['area_ms2_payment_tinkoff'] = 'Платежи Tinkoff';

$_lang['ms2_payment_tinkoff_service_name'] = 'Заказ #[[+num]]';

$_lang['setting_ms2_payment_tinkoff_checkoutUrl'] = 'Url Merchant Api.';
$_lang['setting_ms2_payment_tinkoff_checkoutUrl_desc'] = 'Url для отправки запроса к Merchant Api.';

$_lang['setting_ms2_payment_tinkoff_terminalKey'] = 'Идентификатор терминала';
$_lang['setting_ms2_payment_tinkoff_terminalKey_desc'] = 'Идентификатор терминала, выдается Продавцу Банком.';

$_lang['setting_ms2_payment_tinkoff_secretKey'] = 'Секретный ключ';
$_lang['setting_ms2_payment_tinkoff_secretKey_desc'] = 'Секретный ключ, выдается Продавцу Банком.';

$_lang['setting_ms2_payment_tinkoff_successId'] = 'Id ресурса при удачном платеже.';
$_lang['setting_ms2_payment_tinkoff_successId_desc'] = 'Id ресурса, на который будет перенаправлен пользователь (покупатель) в случае успешной оплаты.';

$_lang['setting_ms2_payment_tinkoff_failId'] = 'Id ресурса при неудачной оплаты';
$_lang['setting_ms2_payment_tinkoff_failId_desc'] = 'Id ресурса, на который будет перенаправлен пользователь (покупатель) при неудачной оплате.';

$_lang['setting_ms2_payment_tinkoff_currency'] = 'Валюта платежа.';
$_lang['setting_ms2_payment_tinkoff_currency_desc'] = 'Валюта платежа - 643.';

$_lang['setting_ms2_payment_tinkoff_showLog'] = 'Показать лог';
$_lang['setting_ms2_payment_tinkoff_showLog_desc'] = 'Показать лог работы';

$_lang['setting_ms2_payment_tinkoff_identifierOrder'] = 'Идентификатор заказа';
$_lang['setting_ms2_payment_tinkoff_identifierOrder_desc'] = 'Идентификатор заказа в системе. По умолчанию "id".';

$_lang['setting_ms2_payment_tinkoff_processReceipt'] = 'Обработать данные чека.';

$_lang['setting_ms2_payment_tinkoff_tax'] = 'Ставка налога.';
$_lang['setting_ms2_payment_tinkoff_tax_desc'] = 'Перечисление со значениями:
- «none» – без НДС;
- «vat0» – НДС по ставке 0%;
- «vat10» – НДС чека по ставке 10%;
- «vat18» – НДС чека по ставке 18%;
- «vat110» – НДС чека по расчетной ставке 10/110;
- «vat118» – НДС чека по расчетной ставке 18/118.';

$_lang['setting_ms2_payment_tinkoff_taxation'] = 'Система налогообложения.';
$_lang['setting_ms2_payment_tinkoff_taxation_desc'] = 'Перечисление со значениями: 
- «osn» – общая СН; 
- «usn_income» – упрощенная СН (доходы); 
- «usn_income_outcome» – упрощенная СН (доходы минус расходы); 
- «envd» – единый налог на вмененный доход; 
- «esn» – единый сельскохозяйственный налог; 
- «patent» – патентная СН. ';

$_lang['setting_ms2_payment_tinkoff_paymentReferenceTerm'] = 'Время жизни ссылки на оплату.';
$_lang['setting_ms2_payment_tinkoff_paymentReferenceTerm_desc'] = 'Время жизни ссылки в виде "5d" - (5 дней).
По умолчанию срок жизни ссылки 1 сутки.
- «m» - месяц; 
- «d» - день; 
- «h» - час; 
- «i» - минута; 
';
