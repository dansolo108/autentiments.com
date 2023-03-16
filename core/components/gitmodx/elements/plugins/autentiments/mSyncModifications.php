<?php
/** @var $scriptProperties array */
/** @var $modx gitModx */
switch ($modx->event->name) {
    case 'mSyncOnPrepareProduct':
        $res = $modx->getObject('msProduct', $productId);
        if ($res) {
            // if ($productId == 742) {
            //     $modx->log(1, print_r($properties,1));
            //     $modx->log(1, print_r($data,1));
            // }
            $values = &$modx->event->returnedValues;
            // Записываем значения в $values, поскольку там пусто
            $values['data'] = $data;
            $values['properties'] = $properties;

            $values['data']['name'] = $res->get('pagetitle');
            // $values['data']['article'] = $res->get('article');
            $values['data']['description'] = $res->get('content');
            $values['properties']['data']['content'] = $res->get('content');
            $values['properties']['data']['pagetitle'] = $res->get('pagetitle');
            $values['properties']['data']['longtitle'] = $res->get('longtitle');
            // $values['properties']['data']['article'] = $res->get('article');
            // не загружаем картинки для товара, загружаем для оффера
            $values['data']['images'] = [];
        }
        break;
    case 'mSyncOnProductImport':
        if ($mode == 'category') {
            $parent = $resource->get('parent');
            if ($parent == 35) {
                $resource->set('parent', 7);
                $resource->save();
            }
        } /*else {
            $resource->set('uuid_1c', $data['uuid']);
            $resource->save();
        }*/

        break;
    case 'mSyncOnProductOffers':
        /** @var mSync $mSync */
        $mSync = $modx->getService('msync');
        /** @var msProduct $resource */
        $product_id = $resource->get('id');
        /** @var Modification $modification */
        /** @var SimpleXMLElement $xml */
        /** @var Autentiments $autentiments */
        $autentiments = $modx->getService('autentiments');
        $modification = $modx->getObject('Modification',[ '1c_id'=> (string)$xml->Ид]);
        if(!$modification) {
            $modification = $modx->newObject('Modification',['1c_id'=> (string)$xml->Ид,'hide'=>true]);
            $modification->save();
            $mSync->log('Создана новая модификация 1с_id:'.(string)$xml->Ид,1);
        }
        else{
            $mSync->log('Найдена модификация id:'.$modification->get('id'),1);
        }
        $color = false;
        // ищем характеристики модификации
        if (isset($xml->ХарактеристикиТовара->ХарактеристикаТовара)) {
            foreach ($xml->ХарактеристикиТовара->ХарактеристикаТовара as $feature) {
                $mSync->log('Обработка характеристики: '.$feature->Наименование,1);
                $detailName = mb_strtolower($feature->Наименование);
                $mSync->log('Значение характеристики: '.str_replace('ё','е',trim((string)$feature->Значение)),1);
                $value = str_replace('ё','е',trim((string)$feature->Значение));
                if (preg_match('/размер/', mb_strtolower($feature->Наименование))) {
                    $detailName = "size";
                }
                else if (preg_match('/цвет/', mb_strtolower($feature->Наименование))) {
                    $detailName = "color";
                    $color = $value;
                }
                else if (preg_match('/состав/', mb_strtolower($feature->Наименование))) {
                    $detailName = "composition";
                }
                $detailType = $modx->getObject('DetailType',['name'=>$detailName]);
                if(!$detailType) {
                    $detailType = $modx->newObject('DetailType',['name'=>$detailName]);
                    if(!$detailType->save()) {
                        $modx->log(MODX_LOG_LEVEL_ERROR,'detailType save error'.var_export($detailType->toArray(),1));
                    }
                    $mSync->log('Создан новый тип характеристик: '.var_export($detailType->toArray(),1),1);
                }
                else{
                    $mSync->log('Найден тип характеристик: '.var_export($detailType->toArray(),1),1);
                }
                $type_id = $detailType->get('id');
                if(!$modification->isNew() && $detail = $modx->getObject('ModificationDetail',['modification_id'=>$modification->get('id'),'type_id'=>$type_id])) {
                    $mSync->log('Найдена опция :'.$detail->get('id'),1);
                    $detail->set('value',$value);
                    if(!$detail->save()){
                        $modx->log(MODX_LOG_LEVEL_ERROR,'detail save error'.var_export($detail->toArray(),1));
                    }
                    continue;
                }
                $detail = $modx->newObject('ModificationDetail',[
                    'type_id'=>$type_id,
                    'value'=> $value
                ]);
                $mSync->log('Создана опция :'.var_export($detail->toArray(),1),1);
                $modification->addMany($detail);
            }
        }
        //ищем цены
        if (count($xml->Цены->Цена) == 1) {
            foreach ($xml->Цены->Цена as $item) {
                $offer_price = (int)$item->ЦенаЗаЕдиницу;
                $offer_old_price = 0;
                $resource->set('price', $offer_price);
                $resource->set('old_price', 0);
            }
        } else if (count($xml->Цены->Цена) == 2) {
            foreach ($xml->Цены->Цена as $item) {
                if ((string)$item->ИдТипаЦены == 'cbcf493b-55bc-11d9-848a-00112f43529a') {
                    $price = (int)$item->ЦенаЗаЕдиницу;
                } else if ((string)$item->ИдТипаЦены == 'dc9f4294-866f-45bb-8e9d-b589a7aee0ef') {
                    $discount_price = (int)$item->ЦенаЗаЕдиницу;
                }
            }
            if ($discount_price > 0) {
                $offer_price = $discount_price;
                $offer_old_price = $price;
                $resource->set('price', $discount_price);
                $resource->set('old_price', $price);
            } else {
                $offer_price = $price;
                $offer_old_price = 0;
                $resource->set('price', $price);
                $resource->set('old_price', 0);
            }
        }
        $mSync->log('Обновлена цена:'.$offer_price,1);
        $modification->set('price',$offer_price);
        $mSync->log('Обновлена старая цена :'.$offer_old_price,1);
        $modification->set('old_price',$offer_old_price);
        // остатки для каждого склада
        foreach ($xml->Склад as $storeXML) {
            $store_id = trim((string)$storeXML['ИдСклада']);
            $store = $modx->getObject('Store', ['1c_id' => $store_id]);
            if (!$store) {
                $store = $modx->newObject('Store', ['1c_id' => $store_id,'name'=>'Неизвестный склад']);
                $store->save();
            }
            $count = (int)str_replace('.0', '', $storeXML['КоличествоНаСкладе']);
            $count = max($count, 0);
            if(!$modification->isNew() && $remain = $modx->getObject('ModificationRemain',['modification_id'=>$modification->get('id'),'store_id'=>$store->get('id')])) {
                $mSync->log('Обновлены остатки:'.$remain->get('id'),1);
                $autentiments->runProcessor('mgr/remains/update',[
                    'id'=>$remain->get('id'),
                    'remains'=>$count,
                ]);
                continue;
            }
            $response = $autentiments->runProcessor('mgr/remains/create',[
                'modification_id'=>$modification->get('id'),
                'store_id' =>$store->get('id'),
                'remains'=>$count,
                ]);
            $mSync->log('Созданы остатки:'.var_export($response->object,1),1);
        }
        // Привязка цвета оффера к изображению
        if ($xml->Картинка && $color) {
            foreach ($xml->Картинка as $k => $v) {
                // $fileName = preg_replace('/\.\w+$/', '', $v);
                $full_url = MODX_ASSETS_PATH . 'components/msync/1c_temp/' . $v;
                if (file_exists($full_url)) {
                    $file = array('id' => $product_id, 'name' => $v, 'file' => $full_url, 'description' => $color);
                    $response = $modx->runProcessor('mgr/gallery/upload', $file, array('processors_path' => MODX_CORE_PATH . 'components/minishop2/processors/'));
                    // не очень красиво, вероятно стоит проверять хеш
                    if ($response->isError() && $response->getMessage() != $modx->lexicon('ms2_err_gallery_exists')) {
                        $modx->log(1, 'Ошибка загрузки изображения оффера (' . $product_id . "): \r\n" . print_r($response->getMessage(), 1));
                    } else {
                        $object = $response->getObject();
                        if ($options = $offer->getMany('Options')) {
                            foreach ($options as $option) {
                                if ($option->get('option') == 'color') {
                                    $opt_color = $option->get('value');
                                }
                            }
                        }
                        if ($opt_color) {
                            $msProductFile = $modx->getObject('msProductFile', $object['id']);
                            $msProductFile->set('description', $opt_color);
                            $msProductFile->save();
                        } else {
                            $modx->log(1, 'Не удалось определить цвет: ' . $object['id']);
                        }
                    }
                } else {
                    $modx->log(1, 'Ошибка загрузки изображения оффера (' . $product_id . '), файл (' . $full_url . ') не найден');
                }
            }
        }
        $modification->set('code',(string)$xml->Артикул);
        $mSync->log('обновлено поле code:'.(string)$xml->Артикул,1);
        $modification->set('product_id',$product_id);
        $mSync->log('обновлено поле product_id:'.$product_id,1);
        if(!$modification->save()){
            $modx->log(1, 'Ошибка сохранения модификации '.var_export($modification->toArray(),1));
        }
        break;

    case 'mSyncOnSalesExport':
        // Исключаем заказы, оформленные по спец.ссылке и отправленные в amoCRM
        foreach ($xml->Документ as $k => $doc) {
            $id = (int)$doc->Ид;
            $properties = $orders[$id]->get('properties');
            $delivery_cost = $orders[$id]->get('delivery_cost');

            $doc->addChild("СтоимостьДоставки", $delivery_cost);

            $amo_userid = isset($properties['amo_userid']) ? $properties['amo_userid'] : '';
            if ($amo_userid) {
                $dom = dom_import_simplexml($doc);
                $dom->parentNode->removeChild($dom);
            }

            $products = $doc->Товары->children();
            foreach ($products as $product) {
                if ($product->Ид == 'ORDER_DELIVERY') continue;
                $msProductData = $modx->getObject('msProductData', ['article' => (string)$product->Артикул]);
                $msProduct = $msProductData->getOne('Product');
                $msProductId = $msProduct->get('id');

                foreach ($product->ХарактеристикиТовара->children() as $req) {
                    if ($req->Наименование == 'Размер') {
                        $size = $req->Значение;
                    }
                    if ($req->Наименование == 'Цвет') {
                        $color = str_replace(['ё', 'Ё'], ['е', 'Е'], $req->Значение);
                    }
                }
                if (!$size || !$color) continue;


                $mSyncProductData = $modx->getObject('mSyncProductData', ['product_id' => $msProductId]);
                if (!$mSyncProductData) continue;

                $Offers = $mSyncProductData->getMany('Offers');
                if (!$Offers) continue;

                foreach ($Offers as $offer) {
                    $sMatch = $cMatch = false;
                    $Options = $offer->getMany('Options');
                    foreach ($Options as $option) {
                        if ($option->get('value') == $size) $sMatch = true;
                        if (str_replace(['ё', 'Ё'], ['е', 'Е'], $option->get('value')) == $color) $cMatch = true;
                    }
                    if ($sMatch === true && $cMatch === true) $product->Ид = $offer->get('uuid_1c');
                }
            }
        }
        break;
}