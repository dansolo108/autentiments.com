id: 19
source: 1
name: updateCountStore
description: mSync2
properties: 'a:0:{}'

-----

switch ($modx->event->name) {
    
    case 'mSyncOnPrepareProduct':
        $res = $modx->getObject('msProduct', $productId);
        if ($res) {
            // if ($productId == 742) {
            //     $modx->log(1, print_r($properties,1));
            //     $modx->log(1, print_r($data,1));
            // }
            $values = & $modx->event->returnedValues;
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
                $resource->set('parent', 7); // перемещаем в другую категорию
                $resource->save();
            }
        } /*else {
            $resource->set('uuid_1c', $data['uuid']);
            $resource->save();
        }*/
        
        break;
    
    case 'mSyncOnProductOffers':
        
        $stikProductRemains = $modx->getService('stik','stikProductRemains', $modx->getOption('core_path').'components/stik/model/');
        if (!($stikProductRemains instanceof stikProductRemains) || !$stikProductRemains->active) return '';
        
        $initial_id = $resource->get('id');
        
        $sizes = $resource->get('size');
        $colors = $resource->get('color');
        
        if (!$sizes) {
            $sizes = [];
        }
        if (!$colors) {
            $colors = [];
        }
        
        $size = '';
        $color = '';
        
        // Заполняем размер у найденного товара
        if (isset($xml->ХарактеристикиТовара->ХарактеристикаТовара)) {
            foreach ($xml->ХарактеристикиТовара->ХарактеристикаТовара as $feature) {
                // $modx->log(1, $feature->Наименование);
                // exit;
                if (preg_match('/размер/', mb_strtolower($feature->Наименование))) {
                    $size = trim((string)$feature->Значение);
                    $sizes = array_merge($sizes, [$size]);
                }
                if (preg_match('/цвет/', mb_strtolower($feature->Наименование))) {
                    $color = trim((string)$feature->Значение);
                    $colors = array_merge($colors, [$color]);
                }
            }
        }
        
        // остатки для каждого склада
        foreach($xml->Склад as $sklad){
            $id_sklad = trim((string)$sklad['ИдСклада']);
            if($id_sklad != 'jtv4DfrCgwf8axwPSQQFw2') continue; // пропускаем, если нет такого склада
            $count = (int)str_replace('.0', '', $sklad['КоличествоНаСкладе']);
            $count = max($count, 0);
            
            if(!empty($size)){
                $stikProductRemains->saveRemains([
                    'product_id' => $resource->get('id'),
                    'store_id' => 1,
                    'size' => $size,
                    'color' => $color,
                    'count' => $count,
                    'set' => true
                ]);
            }
        }
        
        // Привязка цвета оффера к изображению
        if ($xml->Картинка) {
            foreach ($xml->Картинка as $k => $v) {
                // $fileName = preg_replace('/\.\w+$/', '', $v);
                $full_url = MODX_ASSETS_PATH.'components/msync/1c_temp/' . $v;
                if (file_exists($full_url)) {
                    $file = array('id' => $initial_id, 'name' => $v, 'file' => $full_url, 'description' => $color);
    
                    $response = $modx->runProcessor('mgr/gallery/upload', $file, array('processors_path' => MODX_CORE_PATH . 'components/minishop2/processors/'));
                    // не очень красиво, вероятно стоит проверять хеш
                    if ($response->isError() && $response->getMessage() != $modx->lexicon('ms2_err_gallery_exists')) {
                        $modx->log(1, 'Ошибка загрузки изображения оффера (' . $initial_id . "): \r\n" . print_r($response->getMessage(), 1));
                    } else {
                        $object = $response->getObject();
                        if ($options = $offer->getMany('Options')) {
                            foreach ($options as $option) {
                                if ($option->get('option') == 'color') {
                                    $color = $option->get('value');
                                }
                            }
                        }
                        if ($color) {
                            $msProductFile = $modx->getObject('msProductFile', $object['id']);
                            $msProductFile->set('description', $color);
                            $msProductFile->save();
                        } else {
                            $modx->log(1, 'Не удалось определить цвет: ' . $object['id']);
                        }
                    }
                } else {
                    $modx->log(1, 'Ошибка загрузки изображения оффера (' . $initial_id . '), файл (' . $full_url . ') не найден');
                }
            }
        }
        
        if(count($xml->Цены->Цена) == 1){
            foreach($xml->Цены->Цена as $item){
                $resource->set('price', (int)$item->ЦенаЗаЕдиницу);
                $resource->set('old_price', 0);
            }
        } else if(count($xml->Цены->Цена) == 2) {
            foreach($xml->Цены->Цена as $item){
                if((string)$item->ИдТипаЦены == 'cbcf493b-55bc-11d9-848a-00112f43529a'){
                    $price = (int)$item->ЦенаЗаЕдиницу;
                } else if((string)$item->ИдТипаЦены == 'dc9f4294-866f-45bb-8e9d-b589a7aee0ef'){
                    $discount_price = (int)$item->ЦенаЗаЕдиницу;
                }
            }
            if ($discount_price > 0) {
                $resource->set('price', $discount_price);
                $resource->set('old_price', $price);
            } else {
                $resource->set('price', $price);
                $resource->set('old_price', 0);
            }
        }
        
        $sizes = array_unique($sizes);
        $colors = array_unique($colors);
        
        $resource->set('size', $sizes);
        $resource->set('color', $colors);
        $resource->save();
        
        // копируем размеры и цвета в переводы
        // фикс фильтров в английской версии
        $polylangProduct = $modx->getObject('PolylangProduct', array('content_id' => $initial_id, 'culture_key' => 'en'));
        if (!$polylangProduct) {
            $polylangProduct = $modx->newObject('PolylangProduct');
            $polylangProduct->set('content_id', $initial_id);
            $polylangProduct->set('culture_key', 'en');
        }
        $polylangProduct->set('size', $sizes);
        $polylangProduct->set('color', $colors);
        $polylangProduct->save();
        break;

    case 'mSyncOnSalesExport':
        // Исключаем заказы, оформленные по спец.ссылке и отправленные в amoCRM
        foreach ($xml->Документ as $k => $doc) {
            $id = (int) $doc->Ид;
            $properties = $orders[$id]->get('properties');
            $amo_userid = isset($properties['amo_userid']) ? $properties['amo_userid'] : '';
            if ($amo_userid) {
                $dom = dom_import_simplexml($doc);
                $dom->parentNode->removeChild($dom);
            }
        }
        break;
}