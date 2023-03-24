<?php
switch ($modx->event->name) {
    case 'msOnBeforeCreateOrder':
        /** @var msOrder $msOrder */
        /** @var amoCRM $amo */
        if (!$amo = $modx->getService('amocrm', 'amoCRM', $modx->getOption('amocrm_core_path', null,
                $modx->getOption('core_path') . 'components/amocrm/') . 'model/amocrm/', $amoParams)
        ) {
            return 'Could not load amoCRM class!';
        }

        $orderProps = $amo->tools->mergeOrderOptions($msOrder->get('properties'), $amo->findCategoryPipeline($msOrder));
        $msOrder->set('properties', $orderProps);
        break;
}