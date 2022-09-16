<?php
$delivery_id = $delivery_id ?: 0;
$rates = '';

if (!$periodMin || !$periodMax) return '';

$pdotools = $modx->getService('pdoTools');;

// увеличиваем сроки доставки
if ($delivery_id == in_array($delivery_id, [4,5])) {
    // почта
    $periodMin += 10;
    $periodMax += 10;
} elseif (in_array($delivery_id, [1,2])) {
    // СДЭК
    $periodMin += 3;
    $periodMax += 4;
} elseif ($delivery_id == 3) {
    // DHL
    if (!is_numeric($periodMin)) {
        $periodMin = (string) $periodMin;
        $datetime = date_create($periodMin);
        $interval = date_diff(date_create('now'), $datetime);
        $dhlDays = $interval->format('%a');
        $periodMin = $dhlDays + 3;
        $periodMax = $dhlDays + 4;
    } else {
        return '';
    }
}

if ($declension = $pdotools->getFenom()->getModifier('declension')) {
	$days_text = $declension($periodMax, $modx->lexicon('stik_declension_days'));
} else {
    $days_text = $modx->lexicon('stik_days');
}
if ($periodMin == $periodMax) {
    $rates = $periodMin . ' ' . $days_text;
} else {
    $rates = $periodMin . '-' . $periodMax . ' ' . $days_text;
}

return $rates;