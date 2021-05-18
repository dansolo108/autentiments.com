<?php
/** @var modX $modx */
$propsElem = $modx->getOption('amocrm_order_properties_element', 'amoCRMFields');
switch ($modx->event->name) {
    case 'amocrmOnBeforeOrderSend':

        $prefixName = '';
        /** @var msOrder $msOrder */
        /** @var msOrderProduct[] $goods */

        // $modx->log(1, print_r($lead, 1));

        if (is_object($msOrder)) {
            $goods = $msOrder->getMany('Products');
            foreach ($goods as $good) {
                $goodName = $good->get('name');
                $productId = $good->get('product_id');
                $productParents = $modx->getParentIds($productId);


                /*********
                 *
                 * Поиск по названию товара в заказе.
                 * Актуально для виртуальных товаров
                 *
                 */
                // Покупка абонемента
                // if ($goodName == 'Партнерство') {
                //     $prefixName = 'Абонемент ';
                //     break;
                // }


                /*********
                 *
                 * Поиск по по родителям товара
                 *
                 */
                // if (in_array(40, $productParents)) {
                //     $prefixName = 'Семинар ';
                //     break;
                // }
                // if (in_array(39, $productParents)) {
                //     $prefixName = 'Вебинар ';
                //     break;
                // }
                // if (in_array(38, $productParents)) {
                //     $prefixName = 'Видеокурс ';
                //     break;
                // }


                /*********
                 *
                 * Поиск по ID товара
                 *
                 */
                if ($productId == -11111) {
                    $prefixName = 'Определенный товар';
                    break;
                }
            }
        }

        // $modx->log(1, 'PIPELINE: ' . $lead['pipeline_id'] . ', EQUAL: ' . ($lead['pipeline_id'] == 1500643) );
        if (!empty($lead['pipeline_id']) and $lead['pipeline_id'] == 1510342) {
            $prefixName = 'Абонемент ';
        }
        if (!empty($lead['pipeline_id']) and $lead['pipeline_id'] == 1505995) {
            $prefixName = 'Вебинар ';
        }
        if (!empty($lead['pipeline_id']) and $lead['pipeline_id'] == 1505971) {
            $prefixName = 'Семинар ';
        }
        if (!empty($lead['pipeline_id']) and $lead['pipeline_id'] == 1543543) {
            $prefixName = 'Видеокурс ';
        }

        $leadName = $prefixName . $lead['name'];

        // $modx->log(1, 'PIPELINE: ' . $lead['pipeline_id'] . ', EQUAL: ' . ($lead['pipeline_id'] == 1500643) );
        if (!empty($lead['pipeline_id']) and $lead['pipeline_id'] == 1500643) {
            $leadName = 'Квиз ' . date('d.m.y');
        }

        $prefixName = $prefixName ? $prefixName . ' ' : '';
        // $modx->log(1, 'PREFIX NAME: ' . $prefixName);

        $values = & $scriptProperties['__returnedValues'];

        if (!is_array($values)) {
            $values = array();
        }
        if (!isset($values['lead']) or !is_array($values['lead'])) {
            $values['lead'] = array();
        }
        $values['lead']['name'] = $leadName;

        break;
}