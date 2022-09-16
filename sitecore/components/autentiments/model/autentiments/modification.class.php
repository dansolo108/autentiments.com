<?php
class Modification extends xPDOSimpleObject {
    function __construct(xPDO &$xpdo)
    {
        parent::__construct($xpdo);
    }

    function getDetail($name){
        $type = $this->xpdo->getObject('DetailType',['name'=>$name]);
        if($type && $detail = $this->getMany('Details',['type_id'=>$type->get('id')])){
            return $detail[0];
        }
        return null;
    }
    function getRemains(){
        $remains = $this->getMany('Remains');
        $sum = 0;
        foreach ($remains as $remain){
            $sum += $remain->get('remains');
        }
        return $sum;
    }
}