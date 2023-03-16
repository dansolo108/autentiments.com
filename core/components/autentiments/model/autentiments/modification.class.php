<?php
class Modification extends xPDOSimpleObject {
    function __construct(xPDO &$xpdo)
    {
        parent::__construct($xpdo);
    }

    function getDetail($name){
        $q = $this->xpdo->newQuery('ModificationDetail');
        $q->where(['ModificationDetail.modification_id'=>$this->get('id'), 'DetailType.name'=>$name]);
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
        if ($this->get('hide_remains') || $this->xpdo->getObject('msProduct',$this->get('product_id'))->get('soon'))
            return 0;
        $remains = $this->getMany('Remains');
        if(count($remains) === 0){
            return null;
        }
        $sum = 0;
        foreach ($remains as $remain){
            $sum += $remain->get('remains');
        }
        return $sum;
    }
}