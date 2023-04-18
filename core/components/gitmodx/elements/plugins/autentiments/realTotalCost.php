<?php
switch ($modx->event->name) {
    case 'msOnGetStatusCart':
        $values = & $modx->event->returnedValues;
        $values['status'] = $status;
        $values['status']['real_total_cost'] = $status['total_cost'] + $status['total_discount'];
    break;
}