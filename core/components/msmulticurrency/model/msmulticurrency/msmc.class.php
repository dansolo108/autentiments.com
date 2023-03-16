<?php

/**
 * MODx MsMC Class
 *
 * @package msmulticurrency
 */
class MsMC
{

    const version = '1.2.5';

    /** @var modX $modx */
    public $modx;
    /** @var MsMCController $controller */
    public $controller;
    /** @var MsMCProvider $provider */
    public $provider;
    /** @var pdoTools $pdoTools */
    public $pdoTools;
    /** @var miniShop2 $ms2 */
    public $ms2;
    public $namespace = 'msmulticurrency';

    /**
     * MsMC constructor.
     * @param modX $modx
     * @param array $config
     */
    function __construct(modX &$modx, array $config = array())
    {
        $this->modx =& $modx;
        $this->modx->lexicon->load('msmulticurrency:default');
        $corePath = $modx->getOption('msmulticurrency.core_path', $config, $modx->getOption('core_path', null, MODX_CORE_PATH) . 'components/msmulticurrency/');
        $assetsUrl = $modx->getOption('msmulticurrency.assets_url', $config, $modx->getOption('assets_url') . 'components/msmulticurrency/');
        $assetsPath = $modx->getOption('msmulticurrency.assets_path', $config, $modx->getOption('assets_path', null, MODX_ASSETS_PATH) . 'components/msmulticurrency/');
        $this->config = array_merge(array(
            'chunksPath' => $corePath . 'elements/chunks/',
            'controllersPath' => $corePath . 'controllers/',
            'corePath' => $corePath,
            'assetsUrl' => $assetsUrl,
            'assetsPath' => $assetsPath,
            'modelPath' => $corePath . 'model/',
            'processorsPath' => $corePath . 'processors/',
            'providerPath' => $corePath . 'providers/',
            'jsUrl' => $assetsUrl . 'js/',
            'cssUrl' => $assetsUrl . 'css/',
            'templatesPath' => $corePath . 'elements/templates/',
            'connectorUrl' => $assetsUrl . 'connector.php',
            'actionUrl' => $assetsUrl . 'action.php',
            'managerUrl' => $this->modx->config['manager_url'],
            'date_format' => $modx->getOption('msmulticurrency.date_format', null, '%d.%m.%y <span class="gray">%H:%M</span>', true),
            'baseCurrencyId' => $modx->getOption('msmulticurrency.base_currency', null, 1, true),
            'baseCurrencySetId' => $modx->getOption('msmulticurrency.base_currency_set', null, 1, true),
        ), $config);
        $this->modx->addPackage('msmulticurrency', $this->config['modelPath']);
        // $this->checkStat();
    }

    /**
     * @param string $ctx
     */
    public function initialize($ctx = 'web')
    {
        $this->getPdoToolsInstance();
        $this->pdoTools->setConfig($this->config);
    }

    /**
     * Load the appropriate controller
     * @param string $controller
     * @return null|MsMCController
     */
    public function loadController($controller)
    {
        if ($this->modx->loadClass('MsMCController', $this->config['modelPath'] . 'msmulticurrency/', true, true)) {
            $classPath = $this->config['controllersPath'] . 'web/' . mb_strtolower($controller) . '.php';
            $className = 'msMultiCurrency' . $controller . 'Controller';
            if (file_exists($classPath)) {
                if (!class_exists($className)) {
                    $className = require_once $classPath;
                }
                if (class_exists($className)) {
                    $this->controller = new $className($this, $this->config);
                } else {
                    $this->modx->log(modX::LOG_LEVEL_ERROR, '[msMultiCurrency] Could not load controller: ' . $className . ' at ' . $classPath);
                }
            } else {
                $this->modx->log(modX::LOG_LEVEL_ERROR, '[msMultiCurrency] Could not load controller file: ' . $classPath);
            }
        } else {
            $this->modx->log(modX::LOG_LEVEL_ERROR, '[msMultiCurrency] Could not load MsMCController class.');
        }
        return $this->controller;
    }

    /**
     * @param int $id
     * @return MsMCProvider
     */
    public function getProviderInstance($id = 0)
    {
        if (!is_object($this->provider)) {
            if (!$provider = $this->getProviderObj($id)) return null;
            if ($class = $this->modx->loadClass($provider->get('class_name'), $this->config['providerPath'], true, true)) {
                $properties = empty($provider->get('properties')) ? array() : $provider->get('properties');
                $this->provider = new $class($this, $properties);
                if (!($this->provider instanceof MsMCProvider)) return null;
            }

        }
        return $this->provider;
    }

    /**
     * @param int $id
     * @return null|MultiCurrencyProvider
     */
    public function getProviderObj($id = 0)
    {
        $q = $this->modx->newQuery('MultiCurrencyProvider');
        if (empty($id)) {
            $q->where(array('enable' => 1));
        } else {
            $q->where(array('id' => $id));
        }
        return $this->modx->getObject('MultiCurrencyProvider', $q);
    }

    /**
     * @return pdoTools
     */
    public function getPdoToolsInstance()
    {
        if (!is_object($this->pdoTools) || !($this->pdoTools instanceof pdoTools)) {
            $this->pdoTools = $this->modx->getService('pdoFetch');
        }

        return $this->pdoTools;
    }

    /**
     * @return miniShop2|null|object
     */
    public function getMs2Instance()
    {
        if (!is_object($this->ms2) || !($this->ms2 instanceof miniShop2)) {
            $this->ms2 = $this->modx->getService('miniShop2');
            $this->ms2->initialize($this->modx->context->key);
        }

        return $this->ms2;
    }

    /**
     * Loads the Validator class.
     *
     * @access public
     * @param string $type The name to give the service on the msmc object
     * @param array $config An array of configuration parameters for the
     * MsMCValidator class
     * @return MsMCValidator An instance of the MsMCValidator class.
     */
    public function loadValidator($type = 'validator', $config = array())
    {
        if (!$this->modx->loadClass('MsMCValidator', $this->config['modelPath'], true, true)) {
            $this->modx->log(modX::LOG_LEVEL_ERROR, '[msMultiCurrency] Could not load Validator class.');
            return false;
        }
        $this->$type = new MsMCValidator($this, $config);
        return $this->$type;
    }

    /**
     * Helper function to get a chunk or tpl by different methods.
     *
     * @access public
     * @param string $name The name of the tpl/chunk.
     * @param array $properties The properties to use for the tpl/chunk.
     * @param string $type The type of tpl/chunk. Can be embedded,
     * modChunk, file, or inline. Defaults to modChunk.
     * @return string The processed tpl/chunk.
     */
    public function getChunk($name, $properties, $type = 'modChunk')
    {
        $output = '';
        switch ($type) {
            case 'embedded':
                if (!$this->modx->user->isAuthenticated($this->modx->context->get('key'))) {
                    $this->modx->setPlaceholders($properties);
                }
                break;
            case 'modChunk':
                $output .= $this->modx->getChunk($name, $properties);
                break;
            case 'file':
                $name = str_replace(array(
                    '{base_path}',
                    '{assets_path}',
                    '{core_path}',
                ), array(
                    $this->modx->getOption('base_path'),
                    $this->modx->getOption('assets_path'),
                    $this->modx->getOption('core_path'),
                ), $name);
                $output .= file_get_contents($name);
                $this->modx->setPlaceholders($properties);
                break;
            case 'inline':
            default:
                /* default is inline, meaning the tpl content was provided directly in the property */
                $chunk = $this->modx->newObject('modChunk');
                $chunk->setContent($name);
                $chunk->setCacheable(false);
                $output .= $chunk->process($properties);
                break;
        }
        return $output;
    }

    /**
     * Shorthand for original modX::invokeEvent() method with some useful additions.
     *
     * @param $eventName
     * @param array $params
     * @param $glue
     *
     * @return array
     */
    public function invokeEvent($eventName, array $params = array(), $glue = '<br/>')
    {
        if (isset($this->modx->event->returnedValues)) {
            $this->modx->event->returnedValues = null;
        }

        $response = $this->modx->invokeEvent($eventName, $params);
        if (is_array($response) && count($response) > 1) {
            foreach ($response as $k => $v) {
                if (empty($v)) {
                    unset($response[$k]);
                }
            }
        }

        $message = is_array($response) ? implode($glue, $response) : trim((string)$response);
        if (isset($this->modx->event->returnedValues) && is_array($this->modx->event->returnedValues)) {
            $params = array_merge($params, $this->modx->event->returnedValues);
        }

        return array(
            'success' => empty($message),
            'message' => $message,
            'data' => $params,
        );
    }


    /**
     * @param string $key
     * @param string $value
     * @param bool $clearCache
     * @return bool
     */
    public function setOption($key, $value, $clearCache = true)
    {
        if (!$setting = $this->modx->getObject('modSystemSetting', $key)) {
            $setting = $this->modx->newObject('modSystemSetting');
            $setting->set('namespace', $this->namespace);
        }
        $setting->set('value', $value);
        if ($setting->save()) {
            $this->modx->config[$key] = $value;
            if ($clearCache) {
                $this->modx->cacheManager->refresh(array('system_settings' => array()));
            }
            return true;
        }
        return false;
    }


    /**
     * Sets data to cache
     *
     * @param mixed $data
     * @param mixed $options
     *
     * @return string $cacheKey
     */
    public function setCache($data = array(), $options = array())
    {
        $cacheKey = $this->getCacheKey($options);
        $cacheOptions = $this->getCacheOptions($options);
        if (!empty($cacheKey) and !empty($cacheOptions) and $this->modx->getCacheManager()) {
            $this->modx->cacheManager->set(
                $cacheKey,
                $data,
                $cacheOptions[xPDO::OPT_CACHE_EXPIRES],
                $cacheOptions
            );
        }

        return $cacheKey;
    }

    /**
     * Returns data from cache
     *
     * @param mixed $options
     *
     * @return mixed
     */
    public function getCache($options = array())
    {
        $cacheKey = $this->getCacheKey($options);
        $cacheOptions = $this->getCacheOptions($options);
        $cached = '';
        if (!empty($cacheOptions) and !empty($cacheKey) and $this->modx->getCacheManager()) {
            $cached = $this->modx->cacheManager->get($cacheKey, $cacheOptions);
        }

        return $cached;
    }


    /**
     * @param array $options
     *
     * @return bool
     */
    public function clearCache($options = array())
    {
        $cacheOptions = $this->getCacheOptions($options);
        if (!empty($cacheOptions) and $this->modx->getCacheManager()) {
            return $this->modx->cacheManager->clean($cacheOptions);
        }

        return false;
    }

    public function deleteCache($options = array())
    {
        $cacheKey = $this->getCacheKey($options);
        $cacheOptions = $this->getCacheOptions($options);
        if (!empty($cacheOptions) and $this->modx->getCacheManager()) {
            return $this->modx->cacheManager->delete($cacheKey, $cacheOptions);
        }

        return false;
    }

    /**
     * Returns array with options for cache
     *
     * @param $options
     *
     * @return array
     */
    public function getCacheOptions($options = array())
    {
        if (empty($options)) {
            $options = $this->config;
        }
        $cacheOptions = array(
            xPDO::OPT_CACHE_KEY => empty($options['cache_key'])
                ? 'default' : 'default/' . $this->namespace . '/',
            xPDO::OPT_CACHE_HANDLER => !empty($options['cache_handler'])
                ? $options['cache_handler'] : $this->modx->getOption('cache_resource_handler', null, 'xPDOFileCache'),
            xPDO::OPT_CACHE_EXPIRES => $options['cacheTime'] !== ''
                ? (integer)$options['cacheTime'] : (integer)$this->modx->getOption('cache_resource_expires', null, 0),
        );

        return $cacheOptions;
    }

    /**
     * Returns key for cache of specified options
     *
     * @return bool|string
     * @var mixed $options
     */
    public function getCacheKey($options = array())
    {
        if (empty($options)) {
            $options = $this->config;
        }
        if (!empty($options['cache_key'])) {
            return $options['cache_key'];
        }
        $key = !empty($this->modx->resource) ? $this->modx->resource->getCacheKey() : '';

        return $key . '/' . sha1(serialize($options));
    }

    /**
     * @return bool
     */
    public function clearCacheMSearch2()
    {
        return $this->modx->cacheManager->clean(array(
            'cache_key' => 'default/msearch2',
        ));
    }

    /**
     * @return bool
     */
    public function clearAllCache()
    {
        return $this->modx->cacheManager->refresh(array());
    }

    /**
     * @param string $path
     * @return mixed|string
     */
    public function preparePath($path = '')
    {
        $path = str_replace(array(
            '[[+assets_path]]',
            '[[+base_path]]',
            '[[+core_path]]',
            '[[+mgr_path]]',
        ), array(
            $this->modx->getOption('assets_path', null, MODX_BASE_PATH . 'assets/'),
            $this->modx->getOption('base_path', null, MODX_BASE_PATH),
            $this->modx->getOption('core_path', null, MODX_CORE_PATH),
            $this->modx->getOption('manager_path', null, MODX_MANAGER_PATH),
        ), $path);
        return $path;
    }


    /**
     * @param int $setId
     * @return bool
     */
    public function isBaseSet($setId = 0)
    {
        return $this->config['baseCurrencySetId'] == $setId ? true : false;
    }

    /**
     * @param bool $cache
     * @param bool $onlyEnable
     * @param int $setId
     * @return array|mixed
     */
    public function getCurrencies($cache = true, $onlyEnable = true, $setId = 0)
    {
        $setId = $setId ? $setId : $this->config['baseCurrencySetId'];
        $cacheOptions = array(
            'cache_key' => $this->namespace . 'get_currencies_' . $setId . '_' . (int)$onlyEnable,
            'cacheTime' => 0,
        );

        if (!$cache || !$list = $this->getCache($cacheOptions)) {
            $list = array();

            $q = $this->modx->newQuery('MultiCurrencySetMember');

            $q->leftJoin('MultiCurrency', 'MultiCurrency', '`MultiCurrency`.`id` = `MultiCurrencySetMember`.`cid`');

            $q->select($this->modx->getSelectColumns('MultiCurrencySetMember', 'MultiCurrencySetMember', '', array('id'), true));
            $q->select($this->modx->getSelectColumns('MultiCurrency', 'MultiCurrency'));

            $q->where(array(
                '`sid`' => $setId,
            ));

            if ($onlyEnable) {
                $q->where(array(
                    '`enable`' => 1,
                ));
            }
            $q->sortby('`rank`');

            if ($q->prepare() && $q->stmt->execute()) {
                while ($item = $q->stmt->fetch(PDO::FETCH_ASSOC)) {
                    $list[$item['id']] = $item;
                }
            }
            $this->setCache($list, $cacheOptions);
        }

        return $list;
    }

    /**
     * @param int $currencyId
     * @param int $setId
     * @return array
     */
    public function getProductsWithCurrency($currencyId = 0, $setId = 0)
    {

        $list = array();
        $setId = $setId ? $setId : $this->config['baseCurrencySetId'];
        $q = $this->modx->newQuery('msProductData');
        $q->select($this->modx->getSelectColumns('msProductData', 'msProductData'));
        $q->where(array('currency_set_id' => $setId));
        if (empty($currencyId)) {
            $q->where(array('currency_id:!=' => 0));
        } else {
            $q->where(array('currency_id' => $currencyId));
        }
        if ($q->prepare() && $q->stmt->execute()) {
            $list = $q->stmt->fetchAll(PDO::FETCH_ASSOC);
        }

        return $list;
    }

    /**
     * @param int $currencyId
     * @param int $setId
     * @return array
     */
    public function getProductsOptionsPriceWithCurrency($currencyId = 0, $setId = 0)
    {

        $list = array();
        $setId = $setId ? $setId : $this->config['baseCurrencySetId'];
        $q = $this->modx->newQuery('msopModification');
        $q->select($this->modx->getSelectColumns('msopModification', 'msopModification', '', array('id', 'currency_set_id', 'currency_id', 'msmc_price', 'msmc_old_price')));
        $q->where(array('currency_set_id' => $setId));
        if (empty($currencyId)) {
            $q->where(array('currency_id:!=' => 0));
        } else {
            $q->where(array('currency_id' => $currencyId));
        }
        if ($q->prepare() && $q->stmt->execute()) {
            $list = $q->stmt->fetchAll(PDO::FETCH_ASSOC);
        }

        return $list;
    }

    /**
     * @param int $productId
     * @param float $price
     * @param float $oldPrice
     */
    public function updateProductPrice($productId = 0, $price = 0, $oldPrice = 0)
    {
        $table = $this->modx->getTableName('msProductData');
        $sql = "UPDATE {$table} SET `price` = {$this->prepareNumeric($price)}, `old_price` = {$this->prepareNumeric($oldPrice)} WHERE `id`= {$productId}";
        $this->modx->exec($sql);
    }

    /**
     * @param int $modificationId
     * @param float $price
     * @param float $oldPrice
     */
    public function updateProductOptionsPrice($modificationId = 0, $price = 0, $oldPrice = 0)
    {
        $table = $this->modx->getTableName('msopModification');
        $sql = "UPDATE {$table} SET `price` = {$this->prepareNumeric($price)}, `old_price` = {$this->prepareNumeric($oldPrice)} WHERE `id`= {$modificationId}";
        $this->modx->exec($sql);
    }

    /**
     * @param int $currencyId
     * @param int $setId
     */
    public function updateProductsOptionsPrice($currencyId = 0, $setId = 0)
    {
        if (!$this->isExistService('msoptionsprice')) return;
        $setIds = empty($setId) ? $this->getSetIds() : array($setId);
        foreach ($setIds as $setId) {
            if ($list = $this->getProductsOptionsPriceWithCurrency($currencyId, $setId)) {
                foreach ($list as $product) {
                    $price = $this->convertPriceToBaseCurrency($product['msmc_price'], $product['currency_id'], $setId);
                    $oldPrice = $this->convertPriceToBaseCurrency($product['msmc_old_price'], $product['currency_id'], $setId);
                    $this->updateProductOptionsPrice($product['id'], $price, $oldPrice);
                }
            }
        }
    }

    /**
     * @param int $currencyId
     * @param int $setId
     */
    public function updateProductsPrice($currencyId = 0, $setId = 0)
    {
        $setIds = empty($setId) ? $this->getSetIds() : array($setId);

        foreach ($setIds as $setId) {
            if ($list = $this->getProductsWithCurrency($currencyId, $setId)) {
                foreach ($list as $product) {
                    //  $this->modx->log(modX::LOG_LEVEL_ERROR, print_r($product, 1));
                    $price = $this->convertPriceToBaseCurrency($product['msmc_price'], $product['currency_id'], $setId);
                    $oldPrice = $this->convertPriceToBaseCurrency($product['msmc_old_price'], $product['currency_id'], $setId);
                    $response = $this->invokeEvent('msmcOnBeforeUpdateProductPrice', array(
                        'price' => $price,
                        'oldPrice' => $oldPrice,
                        'product' => $product,
                        'currencyId' => $currencyId,
                        'setId' => $setId,
                    ));
                    if ($response['success']) {
                        $price = $response['data']['price'];
                        $oldPrice = $response['data']['oldPrice'];
                        $this->updateProductPrice($product['id'], $price, $oldPrice);
                    } else {
                        $this->modx->log(modX::LOG_LEVEL_ERROR, $response['message']);
                    }
                }
            }
        }
    }

    /**
     * @return array
     */
    public function getSetIds()
    {
        $result = array();
        $classKey = 'MultiCurrencySet';
        $q = $this->modx->newQuery($classKey);
        $q->select($this->modx->getSelectColumns($classKey, $classKey, '', array('id')));

        if ($q->prepare() && $q->stmt->execute()) {
            $result = $q->stmt->fetchAll(PDO::FETCH_COLUMN);
        }

        return $result;
    }

    /**
     * @param int $currencyId
     * @param int $setId
     * @return array|mixed
     */
    public function getCurrencyById($currencyId = 0, $setId = 0)
    {
        $data = array();
        $setId = $setId ? $setId : $this->config['baseCurrencySetId'];
        $cacheOptions = array(
            'cache_key' => $this->namespace . 'get_currency_by_id_' . $currencyId . '_' . $setId,
            'cacheTime' => 0,
        );
        if ($currencyId) {
            if (!$data = $this->getCache($cacheOptions)) {
                $data = array();
                $q = $this->modx->newQuery('MultiCurrencySetMember');
                $q->leftJoin('MultiCurrency', 'MultiCurrency', '`MultiCurrency`.`id` = `MultiCurrencySetMember`.`cid`');
                $q->select($this->modx->getSelectColumns('MultiCurrencySetMember', 'MultiCurrencySetMember', '', array('id'), true));
                $q->select($this->modx->getSelectColumns('MultiCurrency', 'MultiCurrency'));
                $q->where(array(
                    'sid:=' => $setId,
                    'cid:=' => $currencyId,
                ));
                if ($q->prepare() && $q->stmt->execute()) {
                    $data = $q->stmt->fetch(PDO::FETCH_ASSOC);
                }
                $this->setCache($data, $cacheOptions);
            }
        }
        return $data;

    }

    /**
     * @param int $setId
     * @return array|mixed
     */
    public function getBaseCurrency($setId = 0)
    {
        $setId = $setId ? $setId : $this->config['baseCurrencySetId'];
        $currencyId = $this->modx->getOption('msmulticurrency.base_currency', null, 0, true);
        return $this->getCurrencyById($currencyId, $setId);
    }

    /**
     * @return int|mixed
     */
    public function getUserCurrency()
    {
        if ($currency = $this->getUserCurrencyData()) {
            return $currency['id'];
        }
        return 0;
    }

    /**
     * @return string
     */
    public function getSessionCurrencyKey()
    {
        $ctx = $this->modx->context->get('key');
        $cartContext = $this->modx->getOption('ms2_cart_context');
        $cultureKey = $this->modx->getOption('cultureKey');
        $key = $this->modx->getOption('msmulticurrency.session_key', null, 'msmc', true);
        if ($cartContext) {
            $key .= ":id:{$cultureKey}";
        } else {
            $key .= ":id:{$ctx}:{$cultureKey}";
        }
        return $key;

    }

    /**
     * @return string
     */
    public function getSessionContextKey()
    {
        $key = $this->modx->getOption('msmulticurrency.session_key', null, 'msmc', true);
        $key .= ":ctx";
        return $key;

    }

    /**
     * @param array $fields
     * @param int $setId
     * @return array
     */
    public function getUserCurrencyData($fields = array(), $setId = 0)
    {
        $setId = $setId ? $setId : $this->config['baseCurrencySetId'];
        $forceBaseCurrency = $this->modx->getOption('msmc_force_base_currency');
        $baseCurrency = $this->modx->getOption('msmulticurrency.base_currency', null, 0, true);

        if ($forceBaseCurrency) {
            $currencyId = $baseCurrency;
        } else {
            $key = $this->getSessionContextKey();
            $ctx = $this->modx->context->get('key');
            if ($ctx != 'mgr' && isset($_SESSION[$key]) && $ctx != $_SESSION[$key]) {
                $this->modx->switchContext($_SESSION[$key]);
            }
            $key = $this->getSessionCurrencyKey();
            // $this->modx->log(modX::LOG_LEVEL_ERROR, '$key1='.$key);
            if (!empty($_COOKIE[$key])) {
                $currencyId = (int)$_COOKIE[$key];
            } else if (!empty($_SESSION[$key])) {
                $currencyId = (int)$_SESSION[$key];
            } else {
                $currencyId = $this->modx->getOption('msmulticurrency.selected_currency_default', null, 0, true);
                if (!$currencyId) {
                    $currencyId = $baseCurrency;
                }
            }
        }

        $currency = $this->getCurrencyById($currencyId, $setId);

        if (!$forceBaseCurrency) {
            $_SESSION[$key] = $currencyId;
            setcookie($key, $currencyId, time() + 31556926, '/');
            if ($currency['code'] != $this->modx->getPlaceholder('msmc.code')) {
                $this->makePlaceholders($currency);
            }
        }

        if (empty($fields)) {
            return $currency;
        } else {
            $tmp = array();
            foreach ($fields as $key) {
                if (!isset($currency[$key])) continue;
                $tmp[$key] = $currency[$key];
            }
            return $tmp;
        }
    }

    /**
     * @param int $id
     * @param int $setId
     * @return array
     */
    public function setUserCurrency($id, $setId = 0)
    {
        $setId = $setId ? $setId : $this->config['baseCurrencySetId'];
        $key = $this->getSessionCurrencyKey();
        if ($currency = $this->getCurrencyById($id, $setId)) {
            $_SESSION[$key] = $id;
            setcookie($key, $id, time() + 31556926, '/');
            $this->makePlaceholders($currency);
            $this->invokeEvent('msmcOnToggleCurrency', array(
                'currency' => $currency,
            ));
        }
        return $currency;
    }


    /**
     * @param array $ids
     * @param int $currencyId
     * @param bool $isFormat
     * @return array
     */
    public function getProductsPriceInCurrency($ids = array(), $currencyId = 0, $isFormat = true)
    {
        $result = array();
        if (empty($ids)) return $result;

        $currencyId = $currencyId ? $currencyId : $this->getUserCurrency();
        $classKey = 'msProductData';
        $q = $this->modx->newQuery($classKey);
        $q->select($this->modx->getSelectColumns($classKey, $classKey, '', array('id', 'price', 'old_price', 'currency_id', 'currency_set_id')));
        $q->where(array('id:IN' => $ids));
        if ($q->prepare() && $q->stmt->execute()) {
            while ($product = $q->stmt->fetch(PDO::FETCH_ASSOC)) {
                $result[$product['id']] = array(
                    'id' => $product['id'],
                    'price' => $this->getPrice($product['price'], $product['id'], $currencyId, 0, $isFormat),
                    'old_price' => $this->getPrice($product['old_price'], $product['id'], $currencyId, 0, 0, $isFormat),
                );
            }
        }
        return $result;
    }

    /**
     * @param array $currency
     * @return bool|void
     */
    public function makePlaceholders($currency = array())
    {
        $pls = array('name', 'code', 'symbol_left', 'symbol_right', 'val');
        $currency = $currency ? $currency : $this->getUserCurrencyData();

        if (empty($currency)) return;

        foreach ($pls as $key) {
            if (isset($currency[$key])) {
                $this->modx->setPlaceholder('msmc.' . $key, $currency[$key]);
            }
        }

        return true;

    }

    /**
     * @param int $currencyId -  ID Валюты в которой нужно вернуть цену.  По умолчанию  0
     * @param bool $isFormat - Выводить цену отформатированной согласно параметру miniShop2 ms2_price_format. По умолчанию false
     * @return float|int
     */
    public function getCartTotalCost($currencyId, $isFormat = false)
    {
        $cost = 0;
        $ctx = $this->modx->context->key;
        $cartContext = $this->modx->getOption('ms2_cart_context');
        if ($this->getMs2Instance()) {
            if ($products = $this->ms2->cart->get()) {
                foreach ($products as $key => $product) {
                    if (!$cartContext && $ctx != $product['ctx']) continue;
                    $price = $this->getPrice($product['price'], 0, $currencyId, 0, $isFormat);
                    $cost += $price * $product['count'];
                }
            }
        }
        return $cost;
    }

    /**
     * @param string $key - ID товара в корзине
     * @param int $currencyId -  ID Валюты в которой нужно вернуть цену.  По умолчанию  0
     * @param bool $isFormat - Выводить цену отформатированной согласно параметру miniShop2 ms2_price_format. По умолчанию false
     * @return float|int
     */
    public function getCartCost($key, $currencyId, $isFormat = false)
    {
        $cost = 0;
        if ($this->getMs2Instance()) {
            if ($products = $this->ms2->cart->get()) {
                if (!empty($products[$key])) {
                    $product = $products[$key];
                    $price = $this->getPrice($product['price'], 0, $currencyId, 0, $isFormat);
                    $cost += $price * $product['count'];
                }
            }
        }
        return $cost;
    }

    public function getCartTotalBaseCost()
    {
        $cost = 0;
        $ctx = $this->modx->context->key;
        $cartContext = $this->modx->getOption('ms2_cart_context');
        if ($ms2 = $this->getMs2Instance()) {
            if ($products = $this->ms2->cart->get()) {
                foreach ($products as $product) {
                    if (!$cartContext && $ctx != $product['ctx']) continue;
                    //$price = $this->getPrice($product['price'], 0, $currencyId, 0, $isFormat);
                    //  $cost += $price * $product['count'];
                    $cost += $product['price'] * $product['count'];
                }
            }
        }
        return $cost;
    }

    public function getOrderCost(msOrderHandler $order, $currencyId = 0, $with_cart = true)
    {
        $cost = 0;
        if ($with_cart) {
            $cost = $this->getCartTotalCost($currencyId);
        }

        /** @var msDelivery $delivery */
        if (!empty($order->order['delivery']) && $delivery = $this->modx->getObject('msDelivery',
                array('id' => $order->order['delivery']))
        ) {
            $cost = $this->getPrice($cost, 0, $currencyId, 0, false);
        }

        /** @var msPayment $payment */
        if (!empty($order->order['payment']) && $payment = $this->modx->getObject('msPayment',
                array('id' => $order->order['payment']))
        ) {
            $cost = $payment->getCost($this, $cost);
        }
        return $cost;
    }

    /**
     * @param float $price - Цена
     * @param int $productId - ID товара (проверяется привязан ли товар к одной из валют). По умолчанию  0
     * @param int $currencyId -  ID Валюты в которой нужно вернуть цену.  По умолчанию  0
     * @param float $course - Коэффициент на который  следует  поделить цену (Если его указать то функция сразу вернет результат деления).  По умолчанию  0
     * @param bool $isFormat - Выводить цену отформатированной согласно параметру miniShop2 ms2_price_format. По умолчанию true
     * @param bool $isBaseCurrency
     * @return int|string
     */


    public function getPrice($price = 0, $productId = 0, $currencyId = 0, $course = 0.0, $isFormat = true, $isBaseCurrency = true)
    {
        $price = is_numeric($price) ? $price : floatval(preg_replace('~\s+~s', '', $price));
        $newPrice = $price;
        $currency = array();
        $userCurrency = array();
        $baseCurrencyId = $this->modx->getOption('msmulticurrency.base_currency', null, 0, true);
        $setId = $this->config['baseCurrencySetId'];

        if (!empty($course)) {
            $course = floatval($course);
            $newPrice = empty($isBaseCurrency) ? ($price * $course) : ($price / $course);
        } else if ($userCurrency = $this->getUserCurrencyData(array(), $setId)) {
            $currency = $userCurrency;
            if (!empty($currencyId) && $currencyId != $currency['id']) {
                $currency = $this->getCurrencyById($currencyId, $setId);
            }
            if (!empty($currency)) {
                if (empty($isBaseCurrency)) {
                    $newPrice = $price * $userCurrency['val'];
                } else if ($baseCurrencyId != $currency['id']) {
                    $newPrice = $price / $currency['val'];
                }
            }
        }
        $response = $this->invokeEvent('msmcOnGetPrice', array(
            'price' => $price,
            'newPrice' => $newPrice,
            'productId' => $productId,
            'currencyId' => $currencyId,
            'setId' => $setId,
            'course' => $course,
            'isBaseCurrency' => $isBaseCurrency,
            'currency' => $currency,
            'userCurrency' => $userCurrency,
        ));

        if (!$response['success']) {
            $this->modx->log(modX::LOG_LEVEL_ERROR, $response['message']);
            $newPrice = $price;
        } else {
            $currency = $response['data']['currency'];
            $newPrice = $response['data']['newPrice'];
        }

        $precision = $currency['precision'];
        return $isFormat ? $this->formatPrice($newPrice, array($precision, '.', ' ')) : $this->formatPrice($newPrice, array($precision, '.', ''));

    }

    /**
     * @param float $price
     * @param int $currencyId
     * @param int $setId
     * @param bool $isFormat
     * @return float|int
     */
    public function convertPriceToBaseCurrency($price = 0.0, $currencyId = 0, $setId = 0, $isFormat = false)
    {
        if (empty($price)) return $price;

        $price = is_numeric($price) ? $price : floatval(preg_replace('~\s+~s', '', $price));
        $setId = $setId ? $setId : $this->config['baseCurrencySetId'];
        if (empty($currencyId)) {
            $userCurrencyData = $this->getUserCurrencyData();
            $currencyId = $userCurrencyData['id'];
        }
        if (!$currencies = $this->getCurrencies(true, false, $setId)) return $price;
        if (!isset($currencies[$currencyId])) return $price;
        $price = $this->prepareNumeric($price);
        $course = $currencies[$currencyId]['val'];
        $precision = $currencies[$currencyId]['precision'];
        $newPrice = $price * $course;
        return $isFormat ? $this->formatPrice($newPrice, array($precision, '.', ' ')) : $this->formatPrice($newPrice, array($precision, '.', ''));

    }

    /**
     * @param float $price
     * @param int $currencyId
     * @param int $setId
     * @param bool $isFormat
     * @return float|int
     */
    public function convertPriceToCurrency($price = 0.0, $currencyId = 0, $setId = 0, $isFormat = false)
    {
        if (empty($price)) return $price;

        $price = is_numeric($price) ? $price : floatval(preg_replace('~\s+~s', '', $price));
        $setId = $setId ? $setId : $this->config['baseCurrencySetId'];

        if (!$currencies = $this->getCurrencies(true, false, $setId)) return $price;
        if (!isset($currencies[$currencyId])) return $price;

        $price = $this->prepareNumeric($price);
        $course = $currencies[$currencyId]['val'];
        $precision = $currencies[$currencyId]['precision'];
        $newPrice = $price / $course;

        return $isFormat ? $this->formatPrice($newPrice, array($precision, '.', ' ')) : $this->formatPrice($newPrice, array($precision, '.', ''));

    }

    /**
     * @param int $price
     * @param array $pf
     * @return int|string
     */
    public function formatPrice($price = 0, $pf = array())
    {

        $price = $this->prepareNumeric($price);
        if (empty($pf)) {
            if (!$pf = json_decode($this->modx->getOption('ms2_price_format', null, '[2, ".", " "]'), true)) {
                $pf = array(2, '.', ' ');
            }
        }
        $price = $this->roundNumeric($price, $pf[0]);
        $price = number_format($price, $pf[0], $pf[1], $pf[2]);

        if ($this->modx->getOption('ms2_price_format_no_zeros', null, true)) {
            $tmp = explode($pf[1], $price);
            $tmp[1] = rtrim(rtrim(@$tmp[1], '0'), '.');
            $price = !empty($tmp[1])
                ? $tmp[0] . $pf[1] . $tmp[1]
                : $tmp[0];
        }

        return $price;
    }

    /**
     * @return array
     */
    public function getCartStatus()
    {
        $result = array();
        $cartUserCurrency = $this->modx->getOption('msmulticurrency.cart_user_currency', null, 0, true);
        if ($ms2 = $this->getMs2Instance()) {
            $result = $ms2->cart->status();
            if ($cartUserCurrency) {
                $userCurrencyData = $this->getUserCurrencyData();
                $result['total_cost'] = $this->getPrice($result['total_cost'], 0, $userCurrencyData['id'], 0, false);
            }
            $ms2->cart->status();
        }
        return $result;

    }


    /**
     * @param string $val
     * @return bool
     */
    public function isNumeric($val = '')
    {
        return preg_match('/^\d+[\.,]?\d*$/', $val) ? true : false;
    }

    /**
     * @param string|float $val
     * @return string
     */
    public function prepareNumeric($val = '')
    {
        return str_replace(',', '.', $val);

    }

    /**
     * @param float|string $number
     * @param int $precision
     * @return float
     */
    public function roundNumeric($number, $precision = 2)
    {
        if (0 == (int)$number) return $number;
        $number = $this->prepareNumeric($number);
        $tmp = explode('.', $number);
        if (count($tmp) > 1) {
            $number = intval($number) . '.' . substr(end($tmp), 0, $precision);
        }
        return (float)$number;
    }


    /**
     * @param string $service
     * @return bool
     */
    public function isExistService($service = '')
    {
        $service = strtolower($service);

        return file_exists(MODX_CORE_PATH . 'components/' . $service . '/model/' . $service . '/');
    }

    public function extendMsOptionsPriceModel()
    {
        if ($this->isExistService('msoptionsprice')) {
            $this->extendModel('msoptionsprice.mysql.inc.php');
        }
    }

    /**
     * @param string $modelFileName
     */
    public function extendModel($modelFileName)
    {
        $include = include_once($this->config['modelPath'] . 'extend/' . $modelFileName);
        if (is_array($include)) {
            foreach ($include as $class => $map) {
                if (!isset($this->modx->map[$class])) {
                    $this->modx->loadClass($class);
                }
                if (isset($this->modx->map[$class])) {
                    foreach ($map as $key => $values) {
                        $this->modx->map[$class][$key] = array_merge($this->modx->map[$class][$key], $values);
                    }
                }
            }
        }
    }

    /**
     * @param modManagerController $controller
     * @param bool $inject
     */
    public function loadControllerJsCss(modManagerController &$controller, $inject = true)
    {
        $config = array(
            'baseCurrency' => $this->getBaseCurrency(),
            'showInProduct' => $this->modx->getOption('msmulticurrency.show_currency_in_product', null, 1, true),
            'baseCurrencySetId' => $this->modx->getOption('msmulticurrency.base_currency_set', null, 1, true),
        );
        $config = array_merge($this->config, $config);

        $controller->addHtml('<script type="text/javascript">
            MsMC.config = ' . $this->modx->toJSON($config) . ';
        </script>');

        $controller->addJavascript($this->config['jsUrl'] . 'mgr/msmc.js');
        $controller->addJavascript($this->config['jsUrl'] . 'mgr/widgets/set.combo.js');
        $controller->addJavascript($this->config['jsUrl'] . 'mgr/widgets/setmember.combo.js');

        if ($inject) {
            $controller->addJavascript($this->config['jsUrl'] . 'mgr/inject/product.js');
            if ($this->isExistService('msoptionsprice')) {
                $controller->addJavascript($this->config['jsUrl'] . 'mgr/inject/msoptionsprice.js');
            }
        }

        $controller->addCss($this->config['cssUrl'] . 'mgr/ms2.css');
        $controller->addLexiconTopic('msmulticurrency:ms2');
    }

    /**
     * @param modManagerController $controller
     * @param bool $inject
     */
    public function loadControllerJsCssOrder(modManagerController &$controller)
    {
        $this->modx->getOption('msmulticurrency.cart_user_currency', null, 0, true);

        $config = array(
            'currencyField' => $this->modx->getOption('msmulticurrency.order_currency_field', null, 'code', true),
            'baseCurrencyData' => $this->getBaseCurrency()
        );
        $config = array_merge($this->config, $config);

        $controller->addHtml('<script type="text/javascript">
            MsMC.config = ' . $this->modx->toJSON($config) . ';
        </script>');

        $controller->addJavascript($this->config['jsUrl'] . 'mgr/msmc.js');
        $controller->addJavascript($this->config['jsUrl'] . 'mgr/inject/order.js');
        $controller->addLexiconTopic('msmulticurrency:ms2');
    }


    /**
     * @param int $categoryId
     * @param int $currencyId
     * @param int $setId
     * @param string $ctx
     * @param int $depth
     * @return int
     */
    public function recountPriceProductForСategory($categoryId, $currencyId, $setId = 0, $ctx = 'web', $depth = 10)
    {
        $count = 0;
        $classKey = 'msProductData';
        $table = $this->modx->getTableName($classKey);
        $setId = $setId ? $setId : $this->config['baseCurrencySetId'];

        if (!$ids = $this->modx->getChildIds($categoryId, $depth, array('context' => $ctx))) return $count;

        $q = $this->modx->newQuery($classKey);
        $q->select($this->modx->getSelectColumns($classKey, $classKey));
        $q->where(array('id:IN' => $ids));

        if ($q->prepare() && $q->stmt->execute()) {
            while ($product = $q->stmt->fetch(PDO::FETCH_ASSOC)) {
                $price = $this->convertPriceToBaseCurrency($product['msmc_price'], $currencyId, $setId);
                $oldPrice = $this->convertPriceToBaseCurrency($product['msmc_old_price'], $currencyId, $setId);
                $sql = "UPDATE {$table} SET `price` = {$this->prepareNumeric($price)}, `old_price` = {$this->prepareNumeric($oldPrice)}, `currency_id` ={$currencyId} , `currency_set_id` = {$setId}  WHERE `id`= {$product['id']}";
                if ($this->modx->exec($sql)) $count++;
            }

        }
        return $count;
    }


    protected function checkStat()
    {
        $key = strtolower(__CLASS__);
        /** @var modDbRegister $registry */
        $registry = $this->modx->getService('registry', 'registry.modRegistry')
            ->getRegister('user', 'registry.modDbRegister');
        $registry->connect();
        $registry->subscribe('/modstore/' . md5($key));
        if ($res = $registry->read(array('poll_limit' => 1, 'remove_read' => false))) {
            return;
        }
        $c = $this->modx->newQuery('transport.modTransportProvider', array('service_url:LIKE' => '%modstore%'));
        $c->select('username,api_key');
        /** @var modRest $rest */
        $rest = $this->modx->getService('modRest', 'rest.modRest', '', array(
            'baseUrl' => 'https://modstore.pro/extras',
            'suppressSuffix' => true,
            'timeout' => 1,
            'connectTimeout' => 1,
        ));
        if ($rest) {
            $level = $this->modx->getLogLevel();
            $this->modx->setLogLevel(modX::LOG_LEVEL_FATAL);
            $rest->post('stat', array(
                'package' => $key,
                'version' => $this::version,
                'keys' => $c->prepare() && $c->stmt->execute()
                    ? $c->stmt->fetchAll(PDO::FETCH_ASSOC)
                    : array(),
                'uuid' => $this->modx->uuid,
                'database' => $this->modx->config['dbtype'],
                'revolution_version' => $this->modx->version['code_name'] . '-' . $this->modx->version['full_version'],
                'supports' => $this->modx->version['code_name'] . '-' . $this->modx->version['full_version'],
                'http_host' => $this->modx->getOption('http_host'),
                'php_version' => XPDO_PHP_VERSION,
                'language' => $this->modx->getOption('manager_language'),
            ));
            $this->modx->setLogLevel($level);
        }
        $registry->subscribe('/modstore/');
        $registry->send('/modstore/', array(md5($key) => true), array('ttl' => 3600 * 24));
    }
}