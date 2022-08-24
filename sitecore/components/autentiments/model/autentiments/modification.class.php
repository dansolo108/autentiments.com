<?php
class Modification extends xPDOSimpleObject {
    function getDetail($name): ModificationDetail
    {
        return $this->getOne('Details',['name'=>$name]);
    }

    function getRemain($store_id): ModificationRemain
    {
        return $this->getOne('Remains',['store_id'=>$store_id]);
    }
}