<?php
/** @var array $scriptProperties */
/** @var msoptionsprice $msoptionsprice */
if ($modx->event->name != 'msopOnManagerPrepareObjectData') {
    return;
}

if (!$self OR !is_object($self) OR $self->classKey != 'msopModification') {
    return;
}
if (!$type OR $type != 'getlist') {
    return;
}

$data = $modx->getOption('data', $scriptProperties);
$data = is_array($data) ? $data : array();

$extra = $modx->getOption('extra', $data);
$extra = is_array($extra) ? $extra : array();

$options = $modx->getOption('options', $data);
$options = is_array($options) ? $options : array();

foreach ($options as $k => $v) {
    if (strpos($k, '.value')) {
        $extra[] = $v;
    }
}

if (!empty($extra)) {
    $modx->event->returnedValues['data'] = array_merge($data, array('extra' => $extra));
}