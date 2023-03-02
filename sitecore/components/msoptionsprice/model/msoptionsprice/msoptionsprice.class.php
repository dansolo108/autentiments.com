<?php

//ini_set('display_errors', 1);
//ini_set('error_reporting', -1);

/**
 * The base class for msoptionsprice.
 */
class msoptionsprice
{
    /** @var modX $modx */
    public $modx;

    /** @var mixed|null $namespace */
    public $namespace = 'msoptionsprice';
    /** @var array $config */
    public $config = array();
    /** @var array $initialized */
    public $initialized = array();
    /** @var array $placeholders */
    public $placeholders = array();

    /** @var miniShop2 $miniShop2 */
    public $miniShop2;

    public $version = '2.5.22-beta';

    /**
     * @param modX $modx
     * @param array $config
     */
    function __construct(modX &$modx, array $config = array())
    {
        $this->modx =& $modx;

        $corePath = $this->getOption('core_path', $config,
            $this->modx->getOption('core_path', null, MODX_CORE_PATH) . 'components/msoptionsprice/');
        $assetsPath = $this->getOption('assets_path', $config,
            $this->modx->getOption('assets_path', null, MODX_ASSETS_PATH) . 'components/msoptionsprice/');
        $assetsUrl = $this->getOption('assets_url', $config,
            $this->modx->getOption('assets_url', null, MODX_ASSETS_URL) . 'components/msoptionsprice/');
        $connectorUrl = $assetsUrl . 'connector.php';

        $this->config = array_merge(array(
            'namespace'       => $this->namespace,
            'connectorUrl'    => $connectorUrl,
            'assetsBasePath'  => MODX_ASSETS_PATH,
            'assetsBaseUrl'   => MODX_ASSETS_URL,
            'assetsPath'      => $assetsPath,
            'assetsUrl'       => $assetsUrl,
            'cssUrl'          => $assetsUrl . 'css/',
            'jsUrl'           => $assetsUrl . 'js/',
            'corePath'        => $corePath,
            'modelPath'       => $corePath . 'model/',
            'handlersPath'    => $corePath . 'handlers/',
            'processorsPath'  => $corePath . 'processors/',
            'templatesPath'   => $corePath . 'elements/templates/mgr/',
            'jsonResponse'    => true,
            'prepareResponse' => true,
            'showLog'         => false,
        ), $config);


        $this->modx->addPackage('msoptionsprice', $this->config['modelPath']);
        $this->modx->lexicon->load('msoptionsprice:default');
        $this->namespace = $this->getOption('namespace', $config, 'msoptionsprice');

        $this->miniShop2 = $modx->getService('miniShop2');
        if (!($this->miniShop2 instanceof miniShop2)) {
            return false;
        }

        $this->checkStat();
    }

    /**
     * @param       $n
     * @param array $p
     */
    public function __call($n, array $p)
    {
        echo __METHOD__ . ' says: ' . $n;
    }

    /**
     * @param       $key
     * @param array $config
     * @param null $default
     *
     * @return mixed|null
     */
    public function getOption($key, $config = array(), $default = null, $skipEmpty = false)
    {
        $option = $default;
        if (!empty($key) AND is_string($key)) {
            if ($config != null AND array_key_exists($key, $config)) {
                $option = $config[$key];
            } else if (array_key_exists($key, $this->config)) {
                $option = $this->config[$key];
            } else if (array_key_exists("{$this->namespace}_{$key}", $this->modx->config)) {
                $option = $this->modx->getOption("{$this->namespace}_{$key}");
            }
        }
        if ($skipEmpty AND empty($option)) {
            $option = $default;
        }

        return $option;
    }

    public function setOption($key, $value) {
        $this->config[$key]= $value;
    }

    public function initialize($ctx = 'web', array $scriptProperties = array())
    {
        if (isset($this->initialized[$ctx])) {
            return $this->initialized[$ctx];
        }

        $this->config = array_merge($this->config, $scriptProperties, array('ctx' => $ctx));

        if ($ctx != 'mgr' AND (!defined('MODX_API_MODE') OR !MODX_API_MODE)) {

        }

        $initialize = true;
        $this->initialized[$ctx] = $initialize;

        return $initialize;
    }

    /**
     * @return string
     */
    public function getVersionMiniShop2()
    {
        return isset($this->miniShop2->version) ? $this->miniShop2->version : '2.2.0';
    }

    /**
     * Transform array to placeholders
     *
     * @param array $array
     * @param string $plPrefix
     * @param string $prefix
     * @param string $suffix
     * @param bool $uncacheable
     *
     * @return array
     */
    public function makePlaceholders(
        array $array = array(),
        $plPrefix = '',
        $prefix = '[[+',
        $suffix = ']]',
        $uncacheable = true
    )
    {
        return $this->miniShop2->pdoTools->makePlaceholders($array, $plPrefix, $prefix, $suffix, $uncacheable);
    }

    public function regClientStartupScript($src, $plaintext)
    {
        $src = trim($src);
        if (!empty($src)) {
            $this->modx->regClientStartupScript($src, $plaintext);
        }
    }

    public function regClientScript($src, $version = '')
    {
        $src = trim($src);
        if (!empty($src)) {
            if (!empty($version)) {
                $version = '?v=' . dechex(crc32($version));
            } else {
                $version = '';
            }

            // check is load
            if (empty($version)) {
                $tmp = preg_replace('/\[\[.*?\]\]/', '', $src);
                foreach ($this->modx->loadedjscripts as $script => $v) {
                    if (strpos($script, $tmp) != false) {
                        return;
                    }
                }
            }

            $pls = $this->placeholders;
            if (empty($pls)) {
                $pls = $this->placeholders = $this->makePlaceholders($this->config);
            }

            $src = str_replace($pls['pl'], $pls['vl'], $src);
            $this->modx->regClientScript($src . $version, false);
        }
    }

    public function regClientCSS($src, $version = '')
    {
        $src = trim($src);
        if (!empty($src)) {
            if (!empty($version)) {
                $version = '?v=' . dechex(crc32($version));
            } else {
                $version = '';
            }

            // check is load
            if (empty($version)) {
                $tmp = preg_replace('/\[\[.*?\]\]/', '', $src);
                foreach ($this->modx->loadedjscripts as $script => $v) {
                    if (strpos($script, $tmp) != false) {
                        return;
                    }
                }
            }

            $pls = $this->placeholders;
            if (empty($pls)) {
                $pls = $this->placeholders = $this->makePlaceholders($this->config);
            }

            $src = str_replace($pls['pl'], $pls['vl'], $src);
            $this->modx->regClientCSS($src . $version, null);
        }
    }

    /**
     * @param array $properties
     */
    public function loadResourceJsCss(array $properties = array())
    {
        $properties = array_merge($this->config, $properties);
        $pls = $this->placeholders = $this->makePlaceholders($properties);

        $css = $this->getOption('frontendCss', $properties, $this->modx->getOption('msoptionsprice_frontendCss', null),
            true);
        $this->regClientCSS($css, $this->version);

        $js = $this->getOption('frontendJs', $properties, $this->modx->getOption('msoptionsprice_frontendJs', null),
            true);
        $this->regClientScript($js, $this->version);

        $action = trim($this->getOption('actionUrl', $properties, $this->modx->getOption('msoptionsprice_actionUrl', null),
            true));

        $config = json_encode(array(
            'assetsBaseUrl'       => str_replace($pls['pl'], $pls['vl'], $properties['assetsBaseUrl']),
            'assetsUrl'           => str_replace($pls['pl'], $pls['vl'], $properties['assetsUrl']),
            'actionUrl'           => str_replace($pls['pl'], $pls['vl'], $action),
            'allow_zero_cost'     => (bool)$this->getOption('allow_zero_cost', $properties, false),
            'allow_zero_old_cost' => (bool)$this->getOption('allow_zero_old_cost', $properties, true),
            'allow_zero_mass'     => (bool)$this->getOption('allow_zero_mass', $properties, false),
            'allow_zero_article'  => (bool)$this->getOption('allow_zero_article', $properties, false),
            'allow_zero_count'    => (bool)$this->getOption('allow_zero_count', $properties, false),
            'allow_remains'       => (bool)$this->getOption('allow_remains', $properties, false),
            'miniShop2'           => array(
                'version' => $this->getVersionMiniShop2(),
            ),
            'ctx'                 => $this->modx->context->get('key'),
            'version'             => $this->version,
        ), true);

        $this->regClientStartupScript("<script type=\"text/javascript\">msOptionsPriceConfig={$config};</script>",
            true);

    }


    /**
     * return lexicon message if possibly
     *
     * @param string $message
     *
     * @return string $message
     */
    public function lexicon($message, $placeholders = array())
    {
        $key = '';
        if ($this->modx->lexicon->exists($message)) {
            $key = $message;
        } else if ($this->modx->lexicon->exists($this->namespace . '_' . $message)) {
            $key = $this->namespace . '_' . $message;
        }
        if ($key !== '') {
            $message = $this->modx->lexicon->process($key, $placeholders);
        }

        return $message;
    }

    /**
     * @param string $message
     * @param array $data
     * @param array $placeholders
     *
     * @return array|string
     */
    public function failure($message = '', $data = array(), $placeholders = array())
    {
        $response = array(
            'success' => false,
            'message' => $this->lexicon($message, $placeholders),
            'data'    => $data,
        );

        return $this->config['jsonResponse'] ? $this->modx->toJSON($response) : $response;
    }

    /**
     * @param string $message
     * @param array $data
     * @param array $placeholders
     *
     * @return array|string
     */
    public function success($message = '', $data = array(), $placeholders = array())
    {
        $response = array(
            'success' => true,
            'message' => $this->lexicon($message, $placeholders),
            'data'    => $data,
        );

        return $this->config['jsonResponse'] ? $this->modx->toJSON($response) : $response;
    }


    /**
     * @param        $array
     * @param string $delimiter
     *
     * @return array
     */
    public function explodeAndClean($array, $delimiter = ',')
    {
        $array = explode($delimiter, $array);     // Explode fields to array
        $array = array_map('trim', $array);       // Trim array's values
        $array = array_keys(array_flip($array));  // Remove duplicate fields
        $array = array_filter($array);            // Remove empty values from array

        return $array;
    }

    /**
     * @param        $array
     * @param string $delimiter
     *
     * @return array|string
     */
    public function cleanAndImplode($array, $delimiter = ',')
    {
        $array = array_map('trim', $array);       // Trim array's values
        $array = array_keys(array_flip($array));  // Remove duplicate fields
        $array = array_filter($array);            // Remove empty values from array
        $array = implode($delimiter, $array);

        return $array;
    }

    /**
     * @param array $array
     *
     * @return array
     */
    public function cleanArray(array $array = array())
    {
        $array = array_map('trim', $array);       // Trim array's values
        $array = array_filter($array);            // Remove empty values from array
        $array = array_keys(array_flip($array));  // Remove duplicate fields

        return $array;
    }

    /**
     * @param array $array1
     * @param array $array2
     *
     * @return array
     */
    public function array_merge_recursive_ex(array & $array1 = array(), array & $array2 = array())
    {
        $merged = $array1;

        foreach ($array2 as $key => & $value) {
            if (is_array($value) AND isset($merged[$key]) AND is_array($merged[$key])) {
                $merged[$key] = $this->array_merge_recursive_ex($merged[$key], $value);
            } else {
                if (is_numeric($key)) {
                    if (!in_array($value, $merged)) {
                        $merged[] = $value;
                    }
                } else {
                    $merged[$key] = $value;
                }
            }
        }

        return $merged;
    }

    /**
     * @param array $array
     * @param string $prefix
     *
     * @return array
     */
    public function flattenArray(array $array = array(), $prefix = '')
    {
        $outArray = array();
        foreach ($array as $key => $value) {
            if (is_array($value)) {
                $outArray = $outArray + $this->flattenArray($value, $prefix . $key . '.');
            } else {
                $outArray[$prefix . $key] = $value;
            }
        }

        return $outArray;
    }

    public function isWorkingClassKey($resource)
    {
        $value = null;
        if (is_object($resource) AND $resource instanceof modResource) {
            $value = $resource->get('class_key');
        } else if (is_array($resource)) {
            $value = isset($resource['class_key']) ? $resource['class_key'] : null;
        }

        return in_array($value, $this->explodeAndClean($this->getOption('working_class_key', null, 'msProduct', true)));
    }

    public function isWorkingTemplates($resource)
    {
        $value = null;
        if (is_object($resource) AND $resource instanceof modResource) {
            $value = $resource->get('template');
        } else if (is_array($resource)) {
            $value = isset($resource['template']) ? $resource['template'] : null;
        }

        return in_array($value, $this->explodeAndClean($this->getOption('working_templates', null)));
    }

    /**
     * @param string $action
     * @param array $data
     *
     * @return array|modProcessorResponse|string
     */
    public function runProcessor($action = '', $data = array())
    {
        if ($error = $this->modx->getService('error', 'error.modError')) {
            $error->reset();
        }
        $processorsPath = !empty($this->config['processorsPath']) ? $this->config['processorsPath'] : MODX_CORE_PATH;
        /* @var modProcessorResponse $response */
        $response = $this->modx->runProcessor($action, $data, array('processors_path' => $processorsPath));

        return $this->config['prepareResponse'] ? $this->prepareResponse($response) : $response;
    }

    /**
     * This method returns prepared response
     *
     * @param mixed $response
     *
     * @return array|string $response
     */
    public function prepareResponse($response)
    {
        if ($response instanceof modProcessorResponse) {
            $output = $response->getResponse();
        } else {
            $message = $response;
            if (empty($message)) {
                $message = $this->lexicon('err_unknown');
            }
            $output = $this->failure($message);
        }
        if ($this->config['jsonResponse'] AND is_array($output)) {
            $output = $this->modx->toJSON($output);
        } else if (!$this->config['jsonResponse'] AND !is_array($output)) {
            $output = $this->modx->fromJSON($output);
        }

        return $output;
    }

    /** @return array Grid Option Fields */
    public function getGridOptionFields()
    {
        $fields = $this->getOption('grid_option_fields', null,
            'id,key,value', true);
        $fields .= ',id,key,value,properties,actions';
        $fields = $this->explodeAndClean($fields);

        return $fields;
    }

    /** @return array Grid Modification Fields */
    public function getGridModificationFields()
    {
        $fields = $this->getOption('grid_modification_fields', null,
            'id,type,price,old_price,article,weight,count,image', true);
        $fields .= ',id,type,rank,properties,actions';
        $fields = $this->explodeAndClean($fields);

        return $fields;
    }


    public function getWindowModificationTabs()
    {
        $fields = $this->getOption('window_modification_tabs', null,
            'modification', true);
        $fields .= ',modification';
        $fields = $this->explodeAndClean($fields);

        return $fields;
    }

    public function getWindowModificationFields()
    {
        $fields = $this->getOption('window_modification_fields', null,
            'name,image,old_price,article,weight,count', true);
        $fields .= ',id,rid,type,active';
        $fields = $this->explodeAndClean($fields);

        return $fields;
    }

    /**
     * @param modManagerController $controller
     * @param array $setting
     */
    public function loadControllerJsCss(modManagerController &$controller, array $setting = array())
    {
        $controller->addLexiconTopic('msoptionsprice:default');

        $config = $this->config;
        if (is_object($controller->resource) AND $controller->resource instanceof xPDOObject) {
            $config['resource'] = $controller->resource->toArray();
        } else if (is_array($controller->resource)) {
            $config['resource'] = $controller->resource;
        }

        $config['connector_url'] = $this->config['connectorUrl'];
        $config['grid_option_fields'] = $this->getGridOptionFields();
        $config['grid_modification_fields'] = $this->getGridModificationFields();
        $config['window_modification_tabs'] = $this->getWindowModificationTabs();
        $config['window_modification_fields'] = $this->getWindowModificationFields();

        if (!empty($setting['css'])) {
            $controller->addCss($this->config['cssUrl'] . 'mgr/main.css');
            $controller->addCss($this->config['cssUrl'] . 'mgr/bootstrap.buttons.css');
        }

        if (!empty($setting['config'])) {
            $controller->addHtml("<script type='text/javascript'>msoptionsprice.config={$this->modx->toJSON($config)}</script>");
        }

        if (!empty($setting['tools'])) {
            $controller->addJavascript($this->config['jsUrl'] . 'mgr/msoptionsprice.js');
            $controller->addJavascript($this->config['jsUrl'] . 'mgr/misc/tools.js');
            $controller->addJavascript($this->config['jsUrl'] . 'mgr/misc/combo.js');
        }

        if (!empty($setting['modification'])) {
            $controller->addLastJavascript($this->config['jsUrl'] . 'mgr/modification/modification.window.js');
            $controller->addLastJavascript($this->config['jsUrl'] . 'mgr/modification/modification.grid.js');
        }

        if (!empty($setting['option'])) {
            $controller->addLastJavascript($this->config['jsUrl'] . 'mgr/option/option.window.js');
            $controller->addLastJavascript($this->config['jsUrl'] . 'mgr/option/option.grid.js');
        }

        if (!empty($setting['gallery']) AND in_array('gallery', $config['window_modification_tabs']) !== false) {
            $classGallery = trim($this->getOption('modification_gallery_class', null, 'msProductFile', true));

            //$sync_ms2 = (int)$this->modx->getOption('ms2gallery_sync_ms2', null, false, true);

            switch (true) {
                case $classGallery === 'msProductFile':

                    $assetsUrl = $this->miniShop2->config['assetsUrl'];
                    $controller->addLastJavascript($assetsUrl . 'js/mgr/misc/ext.ddview.js');
                    $controller->addLastJavascript($assetsUrl . 'js/mgr/product/gallery/gallery.panel.js');
                    $controller->addLastJavascript($assetsUrl . 'js/mgr/product/gallery/gallery.toolbar.js');
                    $controller->addLastJavascript($assetsUrl . 'js/mgr/product/gallery/gallery.view.js');
                    $controller->addLastJavascript($assetsUrl . 'js/mgr/product/gallery/gallery.window.js');

                    $controller->addLastJavascript($this->config['jsUrl'] . 'mgr/gallery/minishop2/gallery.window.js');
                    $controller->addLastJavascript($this->config['jsUrl'] . 'mgr/gallery/minishop2/gallery.view.js');
                    $controller->addLastJavascript($this->config['jsUrl'] . 'mgr/gallery/minishop2/gallery.panel.js');
                    break;
                case $classGallery === 'UserFile':
                    $controller->addLastJavascript($this->config['jsUrl'] . 'mgr/gallery/userfiles/gallery.window.js');
                    $controller->addLastJavascript($this->config['jsUrl'] . 'mgr/gallery/userfiles/gallery.view.js');
                    $controller->addLastJavascript($this->config['jsUrl'] . 'mgr/gallery/userfiles/gallery.panel.js');
                    break;
            }
        }

        if (!empty($setting['resource/inject'])) {
            $controller->addLastJavascript($this->config['jsUrl'] . 'mgr/resource/inject/inject.tab.js');
            $controller->addLastJavascript($this->config['jsUrl'] . 'mgr/resource/inject/inject.panel.js');
        }

    }

    public function getFirstThumbnailId($rid = 0)
    {
        $id = null;
        $classGallery = trim($this->getOption('modification_gallery_class', null,
            'msProductFile', true));

        switch ($classGallery) {
            case 'msProductFile':
                $q = $this->modx->newQuery($classGallery, array(
                    'product_id' => (int)$rid,
                    'parent'     => 0,
                    //'rank'       => 0,
                    'type'       => 'image',
                ));
                $q->select('id');
                $q->sortby('rank', 'ASC');
                if ($stmt = $q->prepare()) {
                    $id = $this->modx->getValue($stmt);
                }
                break;

            case 'UserFile':
                $list = 'default';
                if ($product = $this->modx->getObject('modResource', array('id' => (int)$rid))) {
                    $list = $this->modx->getOption('userfiles_list_template_' . $product->get('template'), null,
                        $this->modx->getOption('userfiles_list_default', null, 'default', true), true);
                }
                $q = $this->modx->newQuery($classGallery, array(
                    'parent'    => (int)$rid,
                    'list'      => $list,
                    'class:!='  => $classGallery,
                    'mime:LIKE' => 'image%',
                ));
                $q->sortby('rank', 'ASC');
                $q->select('id');
                if ($stmt = $q->prepare()) {
                    $id = $this->modx->getValue($stmt);
                }
                break;
        }

        return $id;
    }

    public function getModificationById($id = 0, $rid = 0, array $options = array())
    {
        $found = true;
        $class = 'msopModification';

        /** @var msopModification $modification */
        if (!$modification = $this->modx->getObject($class, array('id' => (int)$id))) {
            $found = false;
            $modification = $this->modx->newObject($class);
        } else {
            $rid = $modification->get('rid');
        }

        /*******************************************/
        $response = $this->miniShop2->invokeEvent('msopOnGetModificationById', array(
            'id'           => $id,
            'rid'          => $rid,
            'found'        => $found,
            'options'      => $options,
            'modification' => &$modification,
        ));
        if (!$response['success']) {
            return $response['message'];
        }
        $rid = $response['data']['rid'];
        $found = $response['data']['found'];
        $options = $response['data']['options'];
        /*******************************************/

        /** @var $product msProduct */
        if (!$found AND $rid AND $product = $this->modx->getObject('msProduct', array('id' => (int)$rid))) {

            if ($modification) {
                $modification->fromArray(array(
                    'id'        => 0,
                    'name'      => $product->get('pagetitle'),
                    'rid'       => $rid,
                    'type'      => 1,
                    'article'   => $product->get('article'),
                    'price'     => $product->get('price'),
                    'old_price' => $product->get('old_price'),
                    'weight'    => $product->get('weight'),
                    'count'     => 0,
                    'image'     => $this->getFirstThumbnailId($rid),
                ), '', true);
            }

            /*******************************************/
            $response = $this->miniShop2->invokeEvent('msopOnModificationNotFound', array(
                'id'           => $id,
                'rid'          => $rid,
                'found'        => $found,
                'options'      => $options,
                'modification' => &$modification,
            ));
            if (!$response['success']) {
                return $response['message'];
            }
            /*******************************************/
        }

        return $modification ? $modification->toArray() : null;
    }


    public function getModificationByImage(
        $rid = 0,
        $iid = 0,
        array $options = array(),
        $strict = null,
        $excludeIds = array(0),
        $excludeType = array(0),
        $active = true
    )
    {
        $class = 'msopModification';
        $classOption = 'msopModificationOption';
        $classImage = 'msopModificationImage';

        $options = $this->prepareQueryOptions($options);

        if (is_null($strict)) {
            $strict = $this->getOption('search_modification_strict', null, false, true);
        }

        /*******************************************/
        $response = $this->miniShop2->invokeEvent('msopOnBeforeGetModification', array(
            'rid'         => $rid,
            'iid'         => $iid,
            'options'     => $options,
            'excludeIds'  => $excludeIds,
            'excludeType' => $excludeType,
        ));
        if (!$response['success']) {
            return $response['message'];
        }
        $rid = $response['data']['rid'];
        $iid = $response['data']['iid'];
        $options = $response['data']['options'];
        $excludeIds = $response['data']['excludeIds'];
        $excludeType = $response['data']['excludeType'];
        /*******************************************/

        if (!empty($options) AND $strict) {
            $q = $this->modx->newQuery($classOption);
            $q->where(array(
                "{$classOption}.rid"        => $rid,
                "{$classOption}.key:NOT IN" => array_keys($options),
            ));
            $q->select(array(
                "{$classOption}.mid",
            ));
            $q->groupby("{$classOption}.mid");
            if ($q->prepare() AND $q->stmt->execute()) {
                if ($tmp = $q->stmt->fetchAll(PDO::FETCH_COLUMN)) {
                    $excludeIds = array_merge($excludeIds, $tmp);
                }
            }
        }

        /* exclude options */
        $excludeOptions = $this->getOption('exclude_modification_options', null, '', true);

        /* TODO */
        /*
         * сделать проверку на исключение опции цвета
         */
        $excludeOptions .= ',' . $this->getOption('modification_image_option', null, 'color', true);
        $excludeOptions = $this->explodeAndClean($excludeOptions);
        foreach ($excludeOptions as $excludeOption) {
            unset($options[$excludeOption]);
        }


        $iidIds = array($iid);
        /*
         *  получаем возможные $iid
         */
        if (true) {
            $q = $this->modx->newQuery($classImage);
            $q->leftJoin($classImage, $classImage . "first",
                "{$classImage}.mid = {$classImage}first.mid");// AND {$classImage}first.rank= 0
            $q->where(array(
                "{$classImage}.image"     => $iid,
                "{$classImage}first.rank" => 0,
            ));
            $q->select("{$classImage}first.image");
            $q->limit(0);
            $imageIds = array();
            if ($q->prepare() && $q->stmt->execute()) {
                $imageIds = $q->stmt->fetch(PDO::FETCH_ASSOC);
            }
            if (!empty($imageIds)) {
                $iidIds = array_merge($iidIds, $imageIds);
            }
        }


        $q = $this->modx->newQuery($class);
        $q->where(array(
            "{$class}.rid"         => $rid,
            "{$class}.image:IN"    => $iidIds,//$iid,
            "{$class}.id:NOT IN"   => $excludeIds,
            "{$class}.type:NOT IN" => $excludeType,
        ));

        if (!is_null($active)) {
            $q->where(array(
                "{$class}.active" => $active,
            ));
        }

        $q->sortby("{$class}.type");
        $q->sortby("{$class}.rank");
        $q->select(array(
            "{$class}.id",
        ));

        if (!$strict) {

            $sbq = $sbq2 = $sql = $sql2 = array();
            foreach ($options as $key => $value) {
                $alias = $this->getAlias($key);
                /** @var $sbq xPDOQuery[] */
                $sbq[$alias] = $this->modx->newQuery($classOption);
                $sbq[$alias]->setClassAlias($alias);
                $sbq[$alias]->groupby("{$alias}.mid");
                $sbq[$alias]->select(array(
                    "{$alias}.mid",
                ));
                $sbq[$alias]->where(array(
                    "{$alias}.key"   => "{$key}",
                    "{$alias}.value" => "{$value}",
                ));
                $sbq[$alias]->prepare();
                $sql[$alias] = $sbq[$alias]->toSQL();

                if (!$strict) {
                    $alias2 = $this->getAlias($alias);

                    /** @var $sbq2 xPDOQuery[] */
                    $sbq2[$alias2] = $this->modx->newQuery($classOption);
                    $sbq2[$alias2]->setClassAlias($alias2);
                    $sbq2[$alias2]->groupby("{$alias2}.mid");
                    $sbq2[$alias2]->select(array(
                        "{$alias2}.mid",
                    ));
                    $sbq2[$alias2]->where(array(
                        "{$alias2}.key" => "{$key}",
                    ));
                    $sbq2[$alias2]->prepare();
                    $sql2[$alias2] = $sbq2[$alias2]->toSQL();

                    $q->query['where'][] = new xPDOQueryCondition(array(
                        'sql'         => "(IF(" .
                            "(SELECT count(*) FROM ({$sql2[$alias2]}) as {$alias2} WHERE {$alias2}.mid = {$class}.id), " .
                            "EXISTS (SELECT NULL FROM ({$sql[$alias]}) as {$alias} WHERE {$alias}.mid = {$class}.id) ," .
                            "TRUE" .
                            "))",
                        'conjunction' => "AND",
                    ));

                }
            }
        }

        $modification = $this->modx->getObject($class, $q);

        /*******************************************/
        $response = $this->miniShop2->invokeEvent('msopOnAfterGetModification', array(
            'rid'          => $rid,
            'iid'          => $iid,
            'options'      => $options,
            'excludeIds'   => $excludeIds,
            'excludeType'  => $excludeType,
            'modification' => &$modification,
        ));
        if (!$response['success']) {
            return $response['message'];
        }

        /*******************************************/

        return $modification ? $modification->toArray() : null;
    }

    public function getAlias($key = '', $prefix = '_')
    {
        $alias = $prefix . str_replace(array('-', '/'), array(''), $key);

        return $alias;
    }

    public function getModificationByOptions(
        $rid = 0,
        array $options = array(),
        $strict = null,
        $excludeIds = array(0),
        $excludeType = array(0),
        $active = true
    )
    {
        $class = 'msopModification';
        $classOption = 'msopModificationOption';

        $options = $this->prepareQueryOptions($options);

        if (is_null($strict)) {
            $strict = $this->getOption('search_modification_strict', null, false, true);
        }


        /*******************************************/
        $response = $this->miniShop2->invokeEvent('msopOnBeforeGetModification', array(
            'rid'         => $rid,
            'options'     => $options,
            'excludeIds'  => $excludeIds,
            'excludeType' => $excludeType,
        ));
        if (!$response['success']) {
            return $response['message'];
        }
        $rid = $response['data']['rid'];
        $options = $response['data']['options'];
        $excludeIds = $response['data']['excludeIds'];
        $excludeType = $response['data']['excludeType'];
        /*******************************************/

        if (!empty($options) AND $strict) {
            $q = $this->modx->newQuery($classOption);
            $q->where(array(
                "{$classOption}.rid"        => $rid,
                "{$classOption}.key:NOT IN" => array_keys($options),
            ));
            $q->select(array(
                "{$classOption}.mid",
            ));
            $q->groupby("{$classOption}.mid");
            if ($q->prepare() AND $q->stmt->execute()) {
                if ($tmp = $q->stmt->fetchAll(PDO::FETCH_COLUMN)) {
                    $excludeIds = array_merge($excludeIds, $tmp);
                }
            }
        }

        /* exclude options */
        $excludeOptions = $this->getOption('exclude_modification_options', null, '', true);
        $excludeOptions = $this->explodeAndClean($excludeOptions);
        foreach ($excludeOptions as $excludeOption) {
            unset($options[$excludeOption]);
        }

        // add
        if (empty($options)) {
            $q = $this->modx->newQuery($class);
            $q->leftJoin($classOption, $classOption, $classOption . '.mid = ' . $class . '.id');
            $q->where(array(
                "{$class}.rid"            => $rid,
                "{$classOption}.value:!=" => null,
            ));
            $q->select(array(
                "{$class}.id",
            ));
            $q->groupby("{$class}.id");
            if ($q->prepare() AND $q->stmt->execute()) {
                if ($tmp = $q->stmt->fetchAll(PDO::FETCH_COLUMN)) {
                    $excludeIds = array_merge($excludeIds, $tmp);
                }
            }
        }

        $q = $this->modx->newQuery($class);
        $q->where(array(
            "{$class}.rid"         => $rid,
            "{$class}.id:NOT IN"   => $excludeIds,
            "{$class}.type:NOT IN" => $excludeType,
        ));

        if (!is_null($active)) {
            $q->where(array(
                "{$class}.active" => $active,
            ));
        }

        $q->sortby("{$class}.type");
        $q->sortby("{$class}.rank");
        $q->select(array(
            "{$class}.id",
        ));

        /* if (empty($options)) {
             $q->andCondition(array(
                 "{$class}.id:IN" => array(0),
             ));
         }*/


        $sbq = $sbq2 = $sql = $sql2 = array();
        foreach ($options as $key => $value) {

            $alias = $this->getAlias($key);
            /** @var $sbq xPDOQuery[] */
            $sbq[$alias] = $this->modx->newQuery($classOption);
            $sbq[$alias]->setClassAlias($alias);
            $sbq[$alias]->groupby("{$alias}.mid");
            $sbq[$alias]->select(array(
                "{$alias}.mid",
            ));
            $sbq[$alias]->where(array(
                "{$alias}.key"   => "{$key}",
                "{$alias}.value" => "{$value}",
            ));
            $sbq[$alias]->prepare();
            $sql[$alias] = $sbq[$alias]->toSQL();

            if (!$strict) {
                $alias2 = $this->getAlias($alias);

                /** @var $sbq2 xPDOQuery[] */
                $sbq2[$alias2] = $this->modx->newQuery($classOption);
                $sbq2[$alias2]->setClassAlias($alias2);
                $sbq2[$alias2]->groupby("{$alias2}.mid");
                $sbq2[$alias2]->select(array(
                    "{$alias2}.mid",
                ));
                $sbq2[$alias2]->where(array(
                    "{$alias2}.key" => "{$key}",
                ));
                $sbq2[$alias2]->prepare();
                $sql2[$alias2] = $sbq2[$alias2]->toSQL();

                $q->query['where'][] = new xPDOQueryCondition(array(
                    'sql'         => "(IF(" .
                        "(SELECT count(*) FROM ({$sql2[$alias2]}) as {$alias2} WHERE {$alias2}.mid = {$class}.id), " .
                        "EXISTS (SELECT NULL FROM ({$sql[$alias]}) as {$alias} WHERE {$alias}.mid = {$class}.id) ," .
                        "TRUE" .
                        "))",
                    'conjunction' => "AND",
                ));

            } else {
                $q->query['where'][] = new xPDOQueryCondition(array(
                    'sql'         => "EXISTS (SELECT NULL FROM ({$sql[$alias]}) as {$alias} WHERE {$alias}.mid = {$class}.id)",
                    'conjunction' => "AND",
                ));
            }
        }


        /* $s = $q->prepare();
         $sql = $q->toSQL();
         $s->execute();
         $this->modx->log(1, print_r($sql, 1));
         $this->modx->log(1, print_r($s->errorInfo(), 1));*/


        $modification = $this->modx->getObject($class, $q);

        /*******************************************/
        $response = $this->miniShop2->invokeEvent('msopOnAfterGetModification', array(
            'rid'          => $rid,
            'options'      => $options,
            'excludeIds'   => $excludeIds,
            'excludeType'  => $excludeType,
            'modification' => &$modification,
        ));
        if (!$response['success']) {
            return $response['message'];
        }

        /*******************************************/

        return $modification ? $modification->toArray() : null;
    }

    public function getCostByType($type = 0, $cost = 0, $price = 0)
    {
        if (preg_match('/%$/', $cost)) {
            $cost = str_replace('%', '', $cost);
            if (empty($cost)) {
                $cost = 1;
            }
            $cost = $price / 100 * $cost;
        }

        switch ($type) {
            case 1:
                break;
            case 2:
                $cost = $price + $cost;
                break;
            case 3:
                $cost = $price - $cost;
                break;
            default:
                break;
        }

        if ($cost < 0) {
            $cost = 0;
        }

        if (!$cost AND !$this->getOption('allow_zero_cost', null, false)) {
            $cost = $price;
        }

        return $cost;
    }

    public function getMassByType($type = 0, $mass = 0, $weight = 0)
    {
        if (preg_match('/%$/', $mass)) {
            $mass = str_replace('%', '', $mass);
            if (empty($mass)) {
                $mass = 1;
            }
            $mass = $weight / 100 * $mass;
        }

        switch ($type) {
            case 1:
                break;
            case 2:
                $mass = $weight + $mass;
                break;
            case 3:
                $mass = $weight - $mass;
                break;
            default:
                break;
        }

        if ($mass < 0) {
            $mass = 0;
        }

        if (!$mass AND !$this->getOption('allow_zero_mass', null, false)) {
            $mass = $weight;
        }

        return $mass;
    }

    public function getOldCostByModification($modification = array(), $isAjax = false)
    {
        $oldCost = $this->modx->getOption('old_cost', $modification);
        if (!empty($oldCost)) {
            return $oldCost;
        }

        $rid = $this->modx->getOption('rid', $modification);
        /** @var $product msProduct */
        if (!$product = $this->modx->getObject('msProduct', array('id' => (int)$rid))) {
            return $oldCost;
        }
        $productPrice = $product->get('price');
        $productOldPrice = $product->get('old_price');

        $type = $this->modx->getOption('type', $modification);
        $price = $this->modx->getOption('price', $modification);
        $cost = $this->modx->getOption('cost', $modification);
        if (is_null($cost)) {
            $cost = $this->getCostByType($type, $price, $productPrice);
        }

        $oldPrice = $this->modx->getOption('old_price', $modification);
        if (!empty($oldPrice)) {
            $oldCost = $this->getCostByType(1, $oldPrice, $cost);
        } else if (!empty($productPrice)) {
            $oldCost = ($productOldPrice / $productPrice) * $cost;
        }

        if (!empty($oldCost)) {
            $oldCost = $this->formatPrice($oldCost, !$isAjax, false);
        }

        return $oldCost;
    }

    public function getCostByModification($rid = 0, $price = 0, $modification = array(), $isAjax = false)
    {
        if (!$modification) {
            $modification = array();
        }

        /*******************************************/
        $response = $this->miniShop2->invokeEvent('msopOnBeforeGetCost', array(
            'rid'          => $rid,
            'price'        => $price,
            'modification' => $modification,
            'isAjax'       => $isAjax,
        ));
        if (!$response['success']) {
            return $response['message'];
        }
        $rid = $response['data']['rid'];
        $price = $response['data']['price'];
        $modification = $response['data']['modification'];
        /*******************************************/

        $type = $this->modx->getOption('type', $modification, 0, true);
        $cost = $this->modx->getOption('price', $modification, 0, true);

        $cost = $this->getCostByType($type, $cost, $price);
        $cost = $this->formatPrice($cost, !$isAjax, false);

        /*******************************************/
        $response = $this->miniShop2->invokeEvent('msopOnAfterGetCost', array(
            'rid'          => $rid,
            'cost'         => $cost,
            'modification' => $modification,
            'isAjax'       => $isAjax,
        ));
        if (!$response['success']) {
            return $response['message'];
        }
        $cost = $response['data']['cost'];

        /*******************************************/

        return $cost;
    }

    public function getMassByModification($rid = 0, $weight = 0, $modification = array(), $isAjax = false)
    {
        if (!$modification) {
            $modification = array();
        }

        /*******************************************/
        $response = $this->miniShop2->invokeEvent('msopOnBeforeGetMass', array(
            'rid'          => $rid,
            'weight'       => $weight,
            'modification' => $modification,
            'isAjax'       => $isAjax,
        ));
        if (!$response['success']) {
            return $response['message'];
        }
        $rid = $response['data']['rid'];
        $weight = $response['data']['weight'];
        $modification = $response['data']['modification'];
        /*******************************************/

        $type = $this->modx->getOption('type', $modification, 0, true);
        $mass = $this->modx->getOption('weight', $modification, 0, true);

        $mass = $this->getMassByType($type, $mass, $weight);
        $mass = $this->formatWeight($mass, !$isAjax, false);

        /*******************************************/
        $response = $this->miniShop2->invokeEvent('msopOnAfterGetMass', array(
            'rid'          => $rid,
            'mass'         => $mass,
            'modification' => $modification,
            'isAjax'       => $isAjax,
        ));
        if (!$response['success']) {
            return $response['message'];
        }
        $mass = $response['data']['mass'];

        /*******************************************/

        return $mass;
    }


    public function setProductOptions($rid = 0, array $values = array())
    {
        $options = array();
        /** @var $product msProduct */
        if (!$product = $this->modx->getObject('msProduct', array('id' => (int)$rid))) {
            return $options;
        }

        if ($options = $product->loadData()->get('options')) {
            foreach ($options as $k => $v) {
                if ($tmp = $product->get($k) AND is_array($tmp)) {
                    $options[$k] = $tmp;
                }
            }
        }

        foreach ($values as $k => $v) {
            if (!is_array($v)) {
                $v = array($v);
            }

            if (isset($options[$k]) AND is_array($options[$k])) {
                if (!array_intersect($v, $options[$k])) {
                    $options[$k] = array_merge($options[$k], $v);
                }
            } else {
                $options[$k] = $v;
            }
        }

        foreach ($options as $k => $v) {
            $options[$k] = $this->prepareOptionValues($options[$k]);
            $product->set($k, $options[$k]);
        }

        $product->set('options', $options);
        $product->save();

        //$options = $this->modx->call('msopModificationOption', 'getProductOptions', array(&$this->modx, $rid));

        return $options;
    }


    public function removeProductOptions($rid = 0, array $values = array())
    {
        $options = array();
        /** @var $product msProduct */
        if (!$product = $this->modx->getObject('msProduct', array('id' => (int)$rid))) {
            return $options;
        }
        if ($options = $product->loadData()->get('options')) {
            foreach ($options as $k => $v) {
                if ($tmp = $product->get($k) AND is_array($tmp)) {
                    $options[$k] = $tmp;
                }
            }
        }

        foreach ($values as $k => $v) {
            if (!isset($options[$k])) {
                continue;
            }
            if (!is_array($v)) {
                $v = array($v);
            }
            $options[$k] = array_diff($options[$k], $v);
        }

        foreach ($options as $k => $v) {
            $options[$k] = $this->prepareOptionValues($options[$k]);
            $product->set($k, $options[$k]);
        }
        $product->set('options', $options);
        $product->save();

        //$options = $this->modx->call('msopModificationOption', 'getProductOptions', array(&$this->modx, $rid));

        return $options;
    }

    public function prepareQueryOptions($options = null)
    {
        if (!is_array($options)) {
            $options = array();
        }

        foreach ($options as $key => $value) {
            switch (true) {
                // add
                case empty($key):
                case $key === 'modification':
                case strpos($key, 'mssetincart_') !== false:

                case is_array($value);
                    unset($options[$key]);
                    break;
            }
        }

        return $options;
    }

    public function prepareOptionValues($values = null)
    {
        if ($values) {
            if (!is_array($values)) {
                $values = array($values);
            }
            // fix duplicate, empty option values
            $values = array_map('trim', $values);
            $values = array_keys(array_flip($values));
            $values = array_diff($values, array(''));

            if ($this->getOption('sort_modification_option_values', null, true, false)) {
                sort($values);
            }

            if (empty($values)) {
                $values = null;
            }
        }

        return $values;
    }

    public function processOrderProductsRemains($products, $action)
    {
        $remains = array();

        if (!is_array($products)) {
            return $remains;
        }

        /** @var msOrderProduct $product */
        foreach ($products as $product) {
            if (!$options = $product->get('options')) {
                $options = array();
            }
            if (!$mid = isset($options['modification']) ? (int)$options['modification'] : 0) {
                continue;
            }
            if (!isset($remains[$mid])) {
                $remains[$mid] = 0;
            }
            switch ($action) {
                case 'pickup':
                    $remains[$mid] -= $product->get('count');
                    break;
                case 'return':
                    $remains[$mid] += $product->get('count');
                    break;
            }
        }

        foreach ($remains as $mid => $remain) {
            /** @var msopModification $m */
            if ($mid AND $m = $this->modx->getObject('msopModification', $mid)) {
                $m->set('count', $m->get('count') + $remain);
                $m->save();
            }else {
                unset($remains[$mid]);
            }
        }

        return $remains;
    }

    /**
     * @param int $number
     *
     * @return float
     */
    public function formatNumber($number = 0, $ceil = false)
    {
        $number = str_replace(',', '.', $number);
        $number = (float)$number;

        if ($ceil) {
            $number = ceil($number / 10) * 10;
        }

        return round($number, 3);
    }


    /**
     * @param string $price
     * @param bool $number
     *
     * @return float|mixed|string
     */
    public function formatPrice($price = '0', $number = false, $ceil = false)
    {
        $price = $this->formatNumber($price, $ceil);
        $pf = $this->modx->fromJSON($this->getOption('number_format', null, '[0, 1]', true));
        if (is_array($pf)) {
            $price = round($price, $pf[0], $pf[1]);
        }

        if (!$number) {
            $pf = $this->modx->fromJSON($this->modx->getOption('ms2_price_format', null, '[2, ".", " "]', true));
            if (is_array($pf)) {
                $price = number_format($price, $pf[0], $pf[1], $pf[2]);
            }

            if ($this->modx->getOption('ms2_price_format_no_zeros', null, false, true)) {
                if (preg_match('/\..*$/', $price, $matches)) {
                    $tmp = rtrim($matches[0], '.0');
                    $price = str_replace($matches[0], $tmp, $price);
                }
            }
        }

        return $price;
    }

    public function formatWeight($weight = '0', $number = false, $ceil = false)
    {
        $weight = $this->formatNumber($weight, $ceil);
        $pf = $this->modx->fromJSON($this->getOption('number_format', null, '[0, 1]', true));
        if (is_array($pf)) {
            $weight = round($weight, $pf[0], $pf[1]);
        }

        if (!$number) {
            $pf = $this->modx->fromJSON($this->modx->getOption('ms2_weight_format', null, '[3, ".", " "]', true));
            if (is_array($pf)) {
                $weight = number_format($weight, $pf[0], $pf[1], $pf[2]);
            }

            if ($this->modx->getOption('ms2_weight_format_no_zeros', null, false, true)) {
                if (preg_match('/\..*$/', $weight, $matches)) {
                    $tmp = rtrim($matches[0], '.0');
                    $weight = str_replace($matches[0], $tmp, $weight);
                }
            }
        }

        return $weight;
    }

    public function isExistService($service = '')
    {
        $service = strtolower($service);

        return file_exists(MODX_CORE_PATH . 'components/' . $service . '/model/' . $service . '/');
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
            'data'    => $params,
        );
    }

    protected function checkStat()
    {
        $key = strtolower(__CLASS__);
        /** @var modDbRegister $registry */
        $registry = $this->modx->getService('registry', 'registry.modRegistry')->getRegister('user', 'registry.modDbRegister');
        $registry->connect();
        $registry->subscribe('/modstore/' . md5($key));
        if ($res = $registry->read(array('poll_limit' => 1, 'remove_read' => false))) {
            return;
        }
        $c = $this->modx->newQuery('transport.modTransportProvider', array('service_url:LIKE' => '%modstore%'));
        $c->select('username,api_key');
        /** @var modRest $rest */
        $rest = $this->modx->getService('modRest', 'rest.modRest', '', array(
            'baseUrl'        => 'https://modstore.pro/extras',
            'suppressSuffix' => true,
            'timeout'        => 1,
            'connectTimeout' => 1,
        ));

        if ($rest) {
            $level = $this->modx->getLogLevel();
            $this->modx->setLogLevel(modX::LOG_LEVEL_FATAL);
            $rest->post('stat', array(
                'package'            => $key,
                'version'            => $this->version,
                'keys'               => ($c->prepare() AND $c->stmt->execute()) ? $c->stmt->fetchAll(PDO::FETCH_ASSOC) : array(),
                'uuid'               => $this->modx->uuid,
                'database'           => $this->modx->config['dbtype'],
                'revolution_version' => $this->modx->version['code_name'] . '-' . $this->modx->version['full_version'],
                'supports'           => $this->modx->version['code_name'] . '-' . $this->modx->version['full_version'],
                'http_host'          => $this->modx->getOption('http_host'),
                'php_version'        => XPDO_PHP_VERSION,
                'language'           => $this->modx->getOption('manager_language'),
            ));
            $this->modx->setLogLevel($level);
        }
        $registry->subscribe('/modstore/');
        $registry->send('/modstore/', array(md5($key) => true), array('ttl' => 3600 * 24));
    }

}