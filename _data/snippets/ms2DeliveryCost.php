id: 91
source: 1
name: ms2DeliveryCost
description: 'ms2DeliveryCost snippet to list items'
category: ms2DeliveryCost
properties: 'a:1:{s:3:"tpl";a:7:{s:4:"name";s:3:"tpl";s:4:"desc";s:24:"ms2deliverycost_prop_tpl";s:4:"type";s:9:"textfield";s:7:"options";a:0:{}s:5:"value";s:19:"tpl.ms2DeliveryCost";s:7:"lexicon";s:26:"ms2deliverycost:properties";s:4:"area";s:0:"";}}'
static_file: core/components/ms2deliverycost/elements/snippets/ms2deliverycost.php

-----

/* 
Принимает на вход два параметра:
tpl - чанк вывода
cost - сумма заказа, если расчет нужно делать с учетом всего заказа
jsPath - путь до кастомного js
required - необходимые поля для перезагрузки методов доставки (через запятую)
*/
if (!$miniShop2 = $modx->getService('miniShop2')) {
    return ;
}
if (!$pdo = $modx->getService('pdoTools')) {
    return ;
}
if (!$cost) {
    $cost = 0;
}
if (!$tpl) {
	$tpl = 'tpl.ms2DeliveryCost';
}
if (!$jsPath) {
    $jsPath = MODX_ASSETS_URL.'components/ms2deliverycost/js/web/default.js';
}
if (!$required) {
    $required = 'city,index';
} else {
    $required = str_replace(' ', '', $required); 
}

$modx->regClientScript($jsPath);
$modx->regClientHTMLBlock('
    <script>
        if (typeof(ms2DeliveryCost) != "object") {
            var ms2DeliveryCost = {};
        }
        ms2DeliveryCost.required = "'.$required.'";
    </script>
');

$q = $modx->newQuery('msDelivery');
$q->where([
    'active' => 1,    
]);
$q->sortby('rank', 'ASC');
$col = $modx->getCollection('msDelivery', $q);
$order = $miniShop2->order;

$out = ['costs' => [], 'order' => []];

foreach ($col as $delivery) {
    $paymentsArr = array();
    $payments = $delivery->getMany('Payments');
    foreach($payments as $item) {
        $paymentsArr[] = $item->get('payment_id');
    }
    
    if ($_GET['deliveryGetCost'] == 'get') {
        $costDelivery = $delivery->getCost($order, $cost);
        if (is_array($costDelivery)) {
            if ($costDelivery[1] && $costDelivery[2]) {
                // $rates = $this->getRates($costDelivery[1], $costDelivery[2], $this->order['delivery']);
            } elseif ($costDelivery[1]) {
                // $rates = $this->getRates($costDelivery[1], $costDelivery[1], $this->order['delivery']);
            }
            // $rates = ($costDelivery[1] && $costDelivery[2]) ? $this->getRates($costDelivery[1], $costDelivery[2], $this->order['delivery']) : '';
            $costDelivery = $costDelivery[0];
        }
    } else {
        $costDelivery = $rates = false;
    }
    $rates = '1-2 дня';
    
    $out['costs'][] = [
        'cost' => $costDelivery,
        'rates' => $rates,
        'delivery' => $delivery->toArray(),
        'payments' => $paymentsArr,
    ];
}
$out['order'] = $order->get();
$modx->log(1, print_r($out,1));
return $pdo->getChunk($tpl, $out);