<?php
if(empty($input)){
    $resource = $modx->resource->toArray();
}
else{
    $resource = $modx->getObject('modResource',$input)->toArray();
}

if($resource['class_key'] === 'msProduct'){
    $resource['category'] = [];
    $parent = $resource['parent'];
    while(($parent = $modx->getObject('msCategory',$parent))){
        $resource['category'][] = $parent->get('pagetitle');
        $parent = $parent->get('parent');
    }
    $resource['category'] = implode('/',array_reverse($resource['category']));
}

return json_encode($resource);