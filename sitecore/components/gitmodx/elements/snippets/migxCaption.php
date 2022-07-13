<?php
$out = $key;
$object = $modx->getObject('migxConfig', array('name' => $key));
// $modx->log(1, $object->get('extended'));
if($object) {
    $extended = $object->get('extended');
    // $extended = json_decode($extended, true);
    $out = $extended['multiple_formtabs_optionstext'];
}

return $out;