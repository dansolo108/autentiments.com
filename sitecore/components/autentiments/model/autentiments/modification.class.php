<?php
class Modification extends xPDOSimpleObject {
    function __construct(xPDO &$xpdo)
    {
        parent::__construct($xpdo);
    }

    function getDetail($name){
        $q = $this->xpdo->newQuery('ModificationDetail');
        $q->where(['ModificationDetail.modification_id'=>$this->get('id'), 'DetailType'=>$name]);
        $q->leftJoin('DetailType','DetailType',['ModificationDetail.type_id = DetailType.id']);
        return $this->xpdo->getObject('ModificationDetail',$q);
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