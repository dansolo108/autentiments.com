<?php
require_once MODX_CORE_PATH . 'components/stik_cdek/vendor/autoload.php';
use CdekSDK\Requests;
use CdekSDK\CdekClient;

if (!$miniShop2 = $modx->getService('miniShop2')) {
	return;
}

$miniShop2->initialize($modx->context->key);
$order = $miniShop2->order;

$modx->regClientStartupScript('https://api-maps.yandex.ru/2.1/?lang=ru_RU');
// $modx->regClientScript('/assets/components/stik_cdek/js/web/deliveryPoints.js?v=0.0.1');

if ($order) {
	$order = $order->get();
}

$deliveryid = $order['delivery'];

if (!$deliveryid) return false;

$msDelivery = $modx->getObject('msDelivery', $deliveryid);
if ($msDelivery->get('slug') != 'cdek_pvz') {
	return false;
}

$pdo = $modx->getService('pdoTools');

if ($modx->getOption('stik_cdek_calc_city')) {
	$cdekID = $order['cdek_id'];
	if ($scriptProperties['cityid']) {
		$cdekID = $scriptProperties['cityid'];
	}
}
$postCode = $order['index'];
if ($scriptProperties['citypostcode']) {
	$postCode = $scriptProperties['citypostcode'];
}

if (!empty($cdekID)) {
	$req = array(
		'cityid' => $cdekID
	);
} else {
	$req = array(
		'citypostcode' => $postCode
	);
}

if (empty($req['cityid']) && empty($req['citypostcode'])) {
	return ;
}

$client = new CdekClient($modx->getOption('stik_cdek_auth_login'), $modx->getOption('stik_cdek_auth_password'));
$request = new Requests\PvzListRequest();
if (!empty($req['cityid'])) {
    $request->setCityId($req['cityid']);
}
if (!empty($req['citypostcode'])) {
    $request->setCityPostCode($req['citypostcode']);
}
$request->setType(Requests\PvzListRequest::TYPE_ALL);
// $request->setCashless(true);
// $request->setCash(true);
// $request->setCodAllowed(true);
// $request->setDressingRoom(true);

$response = $client->sendPvzListRequest($request);

if ($response->hasErrors()) {
    foreach ($response->getMessages() as $message) {
        if ($message->getErrorCode() !== '') {
            $modx->log(modX::LOG_LEVEL_ERROR, 'CDEK ErrorCode: ' . $message->getErrorCode() . '<br> CDEK ErrorMessage: ' . $message->getMessage());
        }
    }
    return '';
}

$coords = '';
$result = [];
/** @var \CdekSDK\Responses\PvzListResponse $points */
foreach ($response as $item) {
    /** @var \CdekSDK\Common\Pvz $item */
    $coordX = str_replace(',', '.', $item->coordX);
    $coordY = str_replace(',', '.', $item->coordY);
    $result[] = [
        'coordX' => $coordX,
        'coordY' => $coordY,
        'City' => $item->City,
        'Name' => $item->Name,
        'FullAddress' => $item->FullAddress,
        'Code' => $item->Code,
        'WorkTime' => $item->WorkTime,
        'Phone' => $item->Phone,
        'Email' => $item->Email,
    ];
    $coords .= $coordX.'|'.$coordY.',';
}


$countResult = count($result);
$out = array(
	'pvz' => $result,
	'city' => $result[0]['City'],
	'count' => $countResult,
	'coords' => $coords
);

if ($countResult == 0) {
	return ;
}

if ($scriptProperties['tpl']) {
	return $pdo->getChunk($scriptProperties['tpl'], $out);
}

return $pdo->getChunk('stik_cdek.getpvz', $out);