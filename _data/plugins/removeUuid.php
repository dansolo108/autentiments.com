id: 16
source: 1
name: removeUuid
description: 'mSync plugin for delete resource 1c uuid'
category: mSync
properties: null
static_file: core/components/msync/elements/plugins/plugin.delete_resource_uuid.php

-----

if ($modx->event->name == 'OnBeforeEmptyTrash') {
    $mSync = $modx->getService('msync','mSync', $modx->getOption('core_path').'components/msync/model/msync/');
    $mSync->initialize($modx->context->key);

    $deletedids = $modx->event->params['ids'];
    foreach ($deletedids as $resourceid) {
        if( $category = $modx->getObject( 'mSyncCategoryData', array('category_id' => $resourceid)) ){
            $category->remove();
        }
        if( $product = $modx->getObject( 'mSyncProductData', array('product_id' => $resourceid)) ){
            $product->remove();
        }
    }
}
return;