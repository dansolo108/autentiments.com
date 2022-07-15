<?php
/**
 * The base class for stikProductRemains.
 */

class stikProductRemains {

    /* @var modX $modx */
    public $modx;
    public $namespace = 'stik';
    public $config = array();
    public $options;
    public $order_status;
    public $orderback_status;
    public $active = false;
    public $active_before_add = false;
    public $active_before_order = false;
    public $active_bcstatus = false;
    public $check_options = false;
    public $default_remains = 0;
    public $front_js;
    public $hide_count = false;
    public $moreless_count = 10;

    /**
     * @param modX $modx
     * @param array $config
     */
    function __construct (modX &$modx, array $config = array()) {
        $this->modx =& $modx;
        $this->namespace = $this->modx->getOption('namespace', $config, 'stik');
        $corePath = $this->modx->getOption('core_path') . 'components/stik/';
        $assetsPath = $this->modx->getOption('assets_path') . 'components/stik/';
        $assetsUrl = $this->modx->getOption('assets_url') . 'components/stik/';
        $this->config = array_merge(array(
            'corePath' => $corePath,
            'assetsPath' => $assetsPath,
            'modelPath' => $corePath . 'model/',
            'chunksPath' => $corePath . 'elements/chunks/',
            'templatesPath' => $corePath . 'elements/templates/',
            'snippetsPath' => $corePath . 'elements/snippets/',
            'processorsPath' => $corePath . 'processors/',
            
            'assetsUrl' => $assetsUrl,
            'cssUrl' => $assetsUrl . 'css/',
            'jsUrl' => $assetsUrl . 'js/',
            'imagesUrl' => $assetsUrl . 'images/',
            'actionUrl' => $assetsUrl . 'action.php',
            'connectorUrl' => $assetsUrl . 'connector.php',
            
            'chunkSuffix' => '.chunk.tpl',
        ), $config);
        $this->options = $this->modx->getOption('stikpr_options', $config, null);
        $this->order_status = $this->modx->getOption('stikpr_order_status', $config, 0);
        $this->orderback_status = $this->modx->getOption('stikpr_orderback_status', $config, 0);
        $this->active = $this->modx->getOption('stikpr_active', $config, false);
        $this->active_before_add = $this->modx->getOption('stikpr_active_before_add', $config, false);
        $this->active_before_order = $this->modx->getOption('stikpr_active_before_order', $config, false);
        $this->active_bcstatus = $this->modx->getOption('stikpr_active_bcstatus', $config, false);
        $this->modx->addPackage('stik', $this->config['modelPath']);
        // $this->modx->lexicon->load('stik:default');
    }

    public function getRemains($scriptProperties) {
        $product_id = (int) $this->modx->getOption('id', $scriptProperties, null);
        $size = (string) $this->modx->getOption('size', $scriptProperties, null);
        $color = (string) $this->modx->getOption('color', $scriptProperties, null);
        $stores = $this->modx->getOption('store_id', $scriptProperties, ''); // id склада
        if (!is_array($stores) && $stores != '') {
            $stores = [$stores];
        }
        $strong = (bool) $this->modx->getOption('strong', $scriptProperties, false);
        $remains = false;
        if (empty($product_id)) $product_id = $this->modx->resource->get('id');
        if (is_null($size) || is_null($color)) return false;
        if ($strong) {
            $where = [
                'product_id' => $product_id,
                'size' => $size,
                'color' => $color,
            ];
            if (is_array($stores)) {
                $where['store_id:IN'] = $stores;
            }
            $query = $this->modx->newQuery('stikRemains', $where);
            $query->select('SUM(`stikRemains`.`remains`) as remains');
            if ( $query->prepare() && $query->stmt->execute() )
                $remains = $query->stmt->fetchColumn();
        } elseif (!$strong) {
            $query = $this->modx->newQuery('stikRemains', [
                'product_id' => $product_id,
                'remains:>' => 0
            ]);
            $query->select('SUM(`stikRemains`.`remains`) as remains');
            if ( $query->prepare() && $query->stmt->execute() )
                $remains = $query->stmt->fetchColumn();
        }
        return $remains;
    }

    public function saveRemains($scriptProperties) {
        $product_id = (int) $this->modx->getOption('product_id', $scriptProperties, null);
        $size = (string) $this->modx->getOption('size', $scriptProperties, null);
        $color = (string) $this->modx->getOption('color', $scriptProperties, null);
        $store = (int) $this->modx->getOption('store_id', $scriptProperties, 1); // id склада
        $count = (string) $this->modx->getOption('count', $scriptProperties, null);
        $price = (float) $this->modx->getOption('price', $scriptProperties, 0);
        $old_price = (float) $this->modx->getOption('old_price', $scriptProperties, 0);
        $set = (bool) $this->modx->getOption('set', $scriptProperties, false);
        if (empty($product_id) || empty($size) || empty($color) || is_null($count)) return false;
        
        if (!$set && $count < 0) {
            $this->writeOffRemains($scriptProperties);
            return true;
        }
        
        $rem = $this->modx->getObject('stikRemains', [
            'product_id' => $product_id,
            'size' => $size,
            'color' => $color,
            'store_id' => $store,
        ]);
        if (!empty($rem)) {
            $rem->set('remains', ($set ? intval($count) : $rem->get('remains')+intval($count)));
            if (isset($scriptProperties['price'])) $rem->set('price', $price);
            if (isset($scriptProperties['old_price'])) $rem->set('old_price', $old_price);
        } else {
            $rem = $this->modx->newObject('stikRemains', [
                'product_id' => $product_id,
                'size' => $size,
                'color' => $color,
                'store_id' => $store,
                'remains' => intval($count),
                'price' => $price,
                'old_price' => $old_price,
            ]);
        }
        $mode = ( $rem->get('id') > 0 ) ? 'upd' : 'new';
        $response = $this->modx->invokeEvent('stikprOnBeforeChangeRemains', [
            'mode' => $mode,
            'data' => $rem->toArray(),
            'id' => $rem->get('id'),
            'stikRemains' => $rem,
            'object' => $rem
        ]);
        $rem->save();
        $response = $this->modx->invokeEvent('stikprOnChangeRemains', [
            'mode' => $mode,
            'id' => $rem->get('id'),
            'stikRemains' => $rem,
            'object' => $rem
        ]);
        return true;
    }

    // перебираем все склады, отсортированные по id и списываем остатки
    public function writeOffRemains($params) {
        $stores = $this->getPreparedStores();
        $count = $params['count'];
        asort($stores);
        foreach ($stores as $k => $v) {
            $rem = $this->modx->getObject('stikRemains', [
                'product_id' => $params['product_id'],
                'size' => $params['size'],
                'color' => $params['color'],
                'store_id' => $v,
            ]);
            $count = $rem->get('remains')+intval($count);
            $rem->set('remains', max($count, 0));
            $rem->save();
            if ($count >= 0) break;
        }
    }
    
    public function getOfferPrices(int $product_id, string $color, $size, int $store_id = 1) {
        $output = [
            'price' => 0,
            'old_price' => 0,
        ];
        $remainsObject = $this->modx->getObject('stikRemains', [
            'product_id' => $product_id,
            'size' => $size,
            'color' => $color,
            'store_id' => $store_id,
        ]);
        if ($remainsObject->get('price') > 0) {
            $output['price'] = $remainsObject->get('price');
            $output['old_price'] = $remainsObject->get('old_price');
        } else {
            $msProduct = $this->modx->getObject('msProduct', $product_id);
            $output['price'] = $msProduct->get('price');
            $output['old_price'] = $msProduct->get('old_price');
        }
        return $output;
    }
    
    public function getStoreIdByCity($data_city) {
        $cacheManager = $this->modx->getCacheManager();
        $pickup_shops_json = $cacheManager->get('pickup_shops_json_' . $this->modx->getOption('cultureKey'));
        if (empty($pickup_shops_json)) {
            $pickup_shops_json = $this->modx->runSnippet('pdoResources', [
                'parents' => 0,
                'leftJoin' => '{
                    "Store" : {
                        "class" : "stikStore",
                        "alias" : "Store",
                        "on" : "Store.resource_id = modResource.id"
                    }
                }',
                'where' => [
                    'template' => 24,
                    'Store.pickup' => 1,
                    'Store.active' => 1,
                ],
                'select' => '{
                    "modResource":"modResource.pagetitle as city",
                    "Shop":"Store.id as store_id"
                }',
                'return' => 'json',
            ]);
            $pickup_shops_json = json_decode($pickup_shops, 1);
            $cacheManager->set('pickup_shops_json_' . $this->modx->getOption('cultureKey'), $pickup_shops_json, 3600);
        }
        
        $store = 0;
        foreach ($pickup_shops_json as $shop) {
            if (mb_strtolower($shop['city']) == mb_strtolower($data_city)) {
                $store = $shop['store_id'];
            }
        }
        
        return $store;
    }
    
    public function getSortSizes() {
        return [
            'XS',
            'S',
            'XS/S',
            'M',
            'L',
            'М/L',
            'XL',
            'XXL',
            'XXXL',
            'Onesize',
        ];
    }
    
    public function getStoresArray() {
        $cacheManager = $this->modx->getCacheManager();
        if (!$stores_array = $cacheManager->get('stores_array')) {
            $q = $this->modx->newQuery('stikStore');
            $q->select('stikStore.id,stikStore.name,stikStore.1c_id,stikStore.resource_id,stikStore.pickup,stikStore.active,Resource.pagetitle as city');
            $q->where([
            	'active' => 1
            ]);
            $q->leftJoin('modResource', 'Resource', 'stikStore.resource_id = Resource.id');
            if ($q->prepare() && $q->stmt->execute()) {
                $stores_array = $q->stmt->fetchAll(PDO::FETCH_ASSOC);
            }
            $cacheManager->set('stores_array', $stores_array, 3600);
        }

        return $stores_array;
    }
    
    public function getPreparedStores() {
        // кэшируем список складов
        $cacheManager = $this->modx->getCacheManager();
        if (!$stores = $cacheManager->get('stik_stores')) {
            $stikStores = $this->getStoresArray();
            
            $stores = [];
            
            if ($stikStores) {
                foreach ($stikStores as $stikStore) {
                   $stores[$stikStore['1c_id']] = $stikStore['id'];
                }
            }
            $cacheManager->set('stik_stores', $stores, 3600);
        }
        return $stores;
    }
    
    public function arrayUniqueKey($array, $key) {
        $tmp = $key_array = array(); 
        $i = 0; 
     
        foreach($array as $val) { 
            if (!in_array($val[$key], $key_array)) { 
                $key_array[$i] = $val[$key]; 
                $tmp[$i] = $val; 
            } 
            $i++; 
        } 
        return $tmp; 
    }

    public function loadPlugins() {
        if (!is_object($this->modx->msPlugins) && !is_object($this->modx->ms2Plugins)) {
            if (!class_exists('msPlugins') && file_exists(MODX_CORE_PATH . 'components/minishop2/model/minishop2/msplugins.class.php')) {
                require_once(MODX_CORE_PATH . 'components/minishop2/model/minishop2/msplugins.class.php');
                $this->modx->msPlugins = new msPlugins($this->modx, array());
            } elseif (!class_exists('ms2Plugins') && file_exists(MODX_CORE_PATH . 'components/minishop2/model/minishop2/plugins.class.php')) {
                require_once(MODX_CORE_PATH . 'components/minishop2/model/minishop2/plugins.class.php');
                $this->modx->ms2Plugins = new ms2Plugins($this->modx, array());
            } else return false;
        }
        $plugins = is_object($this->modx->msPlugins) ? $this->modx->msPlugins->getPlugins() : $this->modx->ms2Plugins->plugins;
        foreach ($plugins as $plugin) {
            if (!empty($plugin['manager']['stikRemains'])) {
                $this->modx->controller->addLastJavascript($plugin['manager']['stikRemains']);
            }
        }
    }

}
