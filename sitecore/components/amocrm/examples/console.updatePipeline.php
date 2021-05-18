<?php
/** @var modX $modx */
/** @var amoCRM $amo */

$pipelineId = 0;

if (!$amo = $modx->getService('amocrm', 'amoCRM', $modx->getOption('amocrm_core_path', null, $modx->getOption('core_path') . 'components/amocrm/') . 'model/amocrm/', array('autoUpdatePipelines' => true))) {
    return 'Could not load amoCRM class!';
}
echo $amo->addMiniShopPipeline($pipelineId);
return true;