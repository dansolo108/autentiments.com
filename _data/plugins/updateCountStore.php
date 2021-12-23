id: 19
source: 1
name: updateCountStore
description: mSync
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

        // массив складов ['1c_id' => id]
        $stores = $stikProductRemains->getPreparedStores();
        
        // остатки для каждого склада
        foreach($xml->Склад as $sklad){
            $id_sklad = trim((string)$sklad['ИдСклада']);
            if(!array_key_exists($id_sklad, $stores)) continue; // пропускаем, если нет такого склада
            $count = (int)str_replace('.0', '', $sklad['КоличествоНаСкладе']);
            $count = max($count, 0);
            
            // устанавливаем цену для оффера и товара
            if(count($xml->Цены->Цена) == 1){
                foreach($xml->Цены->Цена as $item){
                    $offer_price = (int)$item->ЦенаЗаЕдиницу;
                    $offer_old_price = 0;
                    $resource->set('price', $offer_price);
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
            
            // $modx->log(1, 'product-'.$resource->get('id') . ' - ' . $count . ' - ' . $size . ' - ' . $color);
            if(!empty($size)){
                $stikProductRemains->saveRemains([
                    'product_id' => $resource->get('id'),
                    'store_id' => $stores[$id_sklad],
                    'size' => $size,
                    'color' => $color,
                    'count' => $count,
                    'price' => $offer_price,
                    'old_price' => $offer_old_price,
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
                    $modx->log(1, 'Ошибка загрузки изображения оффера (' . $initial_id . '), файл (' . $full_url . ') не найден');
                }
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
                        $color = str_replace(['ё','Ё'], ['е','Е'], $req->Значение);
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
                        if (str_replace(['ё','Ё'], ['е','Е'], $option->get('value')) == $color) $cMatch = true;
                    }
                    if ($sMatch === true && $cMatch === true) $product->Ид = $offer->get('uuid_1c');
                }
            }
        }
        break;
}