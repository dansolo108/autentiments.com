<?php
class Modification extends xPDOSimpleObject {
    function __construct(xPDO &$xpdo)
    {
        parent::__construct($xpdo);
    }

    function getDetail($name){
        $type = $this->xpdo->getObject('DetailType',['name'=>$name]);
        if(empty($type)){
            return null;
        }
        $details = $this->getMany('Details',['type_id'=>$type->get('id')]);
        if(count($details) !== 0) {
            if (count($details) > 1) {
                $this->xpdo->log(MODX_LOG_LEVEL_ERROR, 'detail find error count details > 1 modification id:' . $this->get('id') . ', detail type id:' . $type->get('id') . 'name:'.$name);
                foreach($details as $detail){
                    $this->xpdo->log(MODX_LOG_LEVEL_ERROR, 'detail'. var_export($detail->toArray(),1));

                }
            }
            return array_pop($details);
        }
        return null;
    }
    function getDetails(){
        $details = $this->getMany('Details');
        $result = [];
        foreach ($details as $detail){
            $type = $detail->getOne('Type');
            $result[$type->get('name')] = $detail->get('value');
        }
        return $result;
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