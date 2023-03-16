<?php

//ini_set('display_errors', 1);
//ini_set('error_reporting', -1);


/**
 * The base class for msFavorites.
 */
class msFavorites
{

    /* @var modX $modx */
    public $modx;
    /** @var string $version */
    public $version = '3.0.5-beta';
    /** @var mixed|null $namespace */
    public $namespace = 'msfavorites';
    /** @var array $config */
    public $config = [];
    /** @var array $initialized */
    public $initialized = [];

    /**
     * @param  modX   $modx
     * @param  array  $config
     */
    function __construct(modX &$modx, array $config = [])
    {
        $this->modx =& $modx;

        $this->modx->addPackage('msfavorites', MODX_CORE_PATH.'components/msfavorites/model/');
        $this->setConfig($config);
        $this->checkStat();
    }

    /**
     * @param         $n
     * @param  array  $p
     */
    public function __call($n, array $p)
    {
        echo __METHOD__.' says: '.$n;
    }

    public function setConfig(array $config = [], $reset = true)
    {
        $corePath = MODX_CORE_PATH.'components/msfavorites/';
        $assetsUrl = MODX_ASSETS_URL.'components/msfavorites/';

        if ($reset) {
            $config = array_merge([
                'assetsUrl'      => $assetsUrl,
                'cssUrl'         => $assetsUrl.'css/',
                'jsUrl'          => $assetsUrl.'js/',
                'connectorUrl'   => $assetsUrl.'connector.php',
                'corePath'       => $corePath,
                'modelPath'      => $corePath.'model/',
                'customPath'     => $corePath.'custom/',
                'processorsPath' => $corePath.'processors/',
            ], $config);
        } else {
            $config = array_merge($this->config, $config);
        }

        if (empty($config['cssUrl'])) {
            $config['cssUrl'] = $assetsUrl.'css/';
        }
        if (empty($config['jsUrl'])) {
            $config['jsUrl'] = $assetsUrl.'js/';
        }
        if (empty($config['connectorUrl'])) {
            $config['connectorUrl'] = $assetsUrl.'connector.php';
        }
        if (empty($config['customPath'])) {
            $config['customPath'] = $corePath.'custom/';
        }

        $this->config = $config;
    }


    /**
     * @param          $array
     * @param  string  $delimiter
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
     * @param          $array
     * @param  string  $delimiter
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
     * Transform array to placeholders
     *
     * @param  array   $array
     * @param  string  $plPrefix
     * @param  string  $prefix
     * @param  string  $suffix
     * @param  bool    $uncacheable
     *
     * @return array
     */
    public function makePlaceholders(array $array = [], $plPrefix = '', $prefix = '[[+', $suffix = ']]', $uncacheable = true)
    {
        $result = ['pl' => [], 'vl' => []];

        $uncached_prefix = str_replace('[[', '[[!', $prefix);
        foreach ($array as $k => $v) {
            if (is_array($v)) {
                $result = array_merge_recursive($result, $this->makePlaceholders($v, $plPrefix.$k.'.', $prefix, $suffix, $uncacheable));
            } else {
                $pl = $plPrefix.$k;
                $result['pl'][$pl] = $prefix.$pl.$suffix;
                $result['vl'][$pl] = $v;
                if ($uncacheable) {
                    $result['pl']['!'.$pl] = $uncached_prefix.$pl.$suffix;
                    $result['vl']['!'.$pl] = $v;
                }
            }
        }

        return $result;
    }


    public function getOption($key, $config = [], $default = null, $skipEmpty = false)
    {
        $option = $default;
        if (!empty($key) AND is_string($key)) {
            if ($config != null AND array_key_exists($key, $config)) {
                $option = $config[$key];
            } elseif (array_key_exists($key, $this->config)) {
                $option = $this->config[$key];
            } elseif (array_key_exists($this->namespace.'_'.$key, $this->modx->config)) {
                $option = $this->modx->getOption($this->namespace.'_'.$key);
            }
        }
        if ($skipEmpty AND empty($option)) {
            $option = $default;
        }

        return $option;
    }


    public function initialize($ctx = 'web', $config = [])
    {
        if (isset($this->initialized[$ctx])) {
            return $this->initialized[$ctx];
        }

        $this->setConfig(array_merge($config, ['ctx' => $ctx]), false);

        $lang = $this->modx->getOption('cultureKey', null, 'en');
        $this->modx->lexicon->load($lang.':msfavorites:default');
        $this->modx->lexicon->load($lang.':msfavorites:manager');
        $this->modx->lexicon->load($lang.':msfavorites:errors');

        if (!defined('MODX_API_MODE') OR !MODX_API_MODE) {
            $pls = $this->placeholders = $this->makePlaceholders($this->config);

            $css = $this->getOption('frontCss', $config, $this->modx->getOption('msfavorites_front_css', null), true);
            $this->regClientCSS($css, $this->version);
            $js = $this->getOption('frontJs', $config, $this->modx->getOption('msfavorites_front_js', null), true);
            $this->regClientScript($js, $this->version);

            $actionUrl = str_replace($pls['pl'], $pls['vl'], $this->getOption('actionUrl', null, '[[+assetsUrl]]action.php'));
            $this->modx->regClientStartupHTMLBlock(preg_replace(['/^\n/', '/[ ]{2,}|[\t]/'], '', '
            <meta name="msfavorites:version" content="'.$this->version.'">
            <meta name="msfavorites:ctx" content="'.$ctx.'">
            <meta name="msfavorites:actionUrl" content="'.$actionUrl.'">
            '));
        }

        $load = true;
        $this->initialized[$ctx] = $load;

        return $load;
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
                $version = '?v='.dechex(crc32($version));
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
            $this->modx->regClientScript($src.$version, false);
        }
    }


    public function regClientCSS($src, $version = '')
    {
        $src = trim($src);
        if (!empty($src)) {
            if (!empty($version)) {
                $version = '?v='.dechex(crc32($version));
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
            $this->modx->regClientCSS($src.$version, null);
        }
    }


    /**
     * return lexicon message if possibly
     *
     * @param  string  $message
     *
     * @return string $message
     */
    public function lexicon($message, $placeholders = [])
    {
        $key = '';
        if ($this->modx->lexicon->exists($message)) {
            $key = $message;
        } elseif ($this->modx->lexicon->exists($this->namespace.'_'.$message)) {
            $key = $this->namespace.'_'.$message;
        }
        if ($key !== '') {
            $message = $this->modx->lexicon->process($key, $placeholders);
        }

        return $message;
    }


    public function response($success = false, $msg = '', $data = [], $total = null)
    {
        $data = $this->modx->error->toArray($data);
        $success = (boolean)$success;
        $msg = $msg ? $this->lexicon($msg, $data) : $msg;

        $response = [
            'success' => $success,
            'message' => $msg,
            'data'    => $data,
        ];

        return json_encode($response);
    }


    public function success($msg = '', $data = [], $total = null)
    {
        return $this->response(true, $msg, $data, $total);
    }


    public function failure($msg = '', $data = [], $total = null)
    {

        return $this->response(false, $msg, $data, $total);
    }


    public function processContext($ctx = 'web')
    {
        if ($this->modx->context->key != $ctx AND $this->modx->getCount('modContext', ['key' => $ctx])) {
            $this->modx->switchContext($ctx);
            $this->modx->user = null;
            $this->modx->getUser($ctx);
        }
    }


    public function processResource($rid = 0)
    {
        if ($this->modx->getRequest()) {
            $this->modx->invokeEvent("OnWebPageInit");

            if ($this->modx->resource = $this->modx->request->getResource('id', $rid)) {
                $this->modx->beforeProcessing();

                $this->modx->invokeEvent("OnLoadWebDocument");
                if (!$this->modx->getResponse()) {
                    $this->modx->log(modX::LOG_LEVEL_FATAL, 'Could not load response class.');
                }

                // TODO много времени занимает, пока убрал
                //$this->modx->resource->_output = $this->modx->resource->process();

                $this->modx->beforeRender();
            }
        }
    }


    public function processLexicon($topics = [])
    {
        $lang = $this->modx->context->getOption('cultureKey');
        foreach ($topics as $topic) {
            $this->modx->lexicon->load($lang.':'.$topic);
        }
    }


    public function loadCustomClasses($type)
    {
        $type = strtolower($type);
        foreach (scandir($this->config['customPath'].$type, true) as $file) {
            if (preg_match('/.*?\.class\.php$/i', $file)) {
                /** @noinspection PhpIncludeInspection */
                include_once($this->config['customPath'].$type.'/'.$file);
            }
        }
    }


    public function processEvent(modSystemEvent $event, array $props = [])
    {
        $event_class = 'msfEvent'.$event->name;
        if (!class_exists($event_class)) {
            $this->modx->log(xPDO::LOG_LEVEL_ERROR, 'Could not get event class: "'.$event_class.'"');

            return false;
        }

        // Custom class
        $custom_class = $this->getOption('_'.$event_class, null, $event_class, true);
        if ($custom_class != $event_class) {
            $this->loadCustomClasses('event');
            if (!class_exists($custom_class)) {
                $this->modx->log(xPDO::LOG_LEVEL_ERROR, 'Could not get custom event class: "'.$custom_class.'"');
            } else {
                $event_class = $custom_class;
            }
        }

        /** @var msfEvent $handler */
        $handler = new $event_class($this, $event, $props);

        return $handler->process();
    }


    public function processMethod(array $props = [])
    {
        $process_class = 'msfMethod'.ucfirst($props['method']);
        if (empty($props['method']) OR !class_exists($process_class)) {
            $this->modx->log(xPDO::LOG_LEVEL_ERROR, 'Could not get event class: "'.$process_class.'"');

            return false;
        }

        // Custom cart class
        $custom_class = $this->getOption('_'.$process_class, null, $process_class, true);
        if ($custom_class != $process_class) {
            $this->loadCustomClasses('event');
            if (!class_exists($custom_class)) {
                $this->modx->log(xPDO::LOG_LEVEL_ERROR, 'Could not get custom event class: "'.$process_class.'"');
            } else {
                $process_class = $custom_class;
            }
        }

        /** @var msfMethod $handler */
        $handler = new $process_class($this, $props);

        return $handler->process();
    }


    public function invokeEvent($eventName, array $props = [], $glue = '<br/>')
    {
        if (isset($this->modx->event->returnedValues)) {
            $this->modx->event->returnedValues = null;
        }

        $response = $this->modx->invokeEvent($eventName, $props);
        if (is_array($response) AND count($response) > 1) {
            foreach ($response as $k => $v) {
                if (empty($v)) {
                    unset($response[$k]);
                }
            }
        }

        $message = is_array($response) ? implode($glue, $response) : trim((string)$response);
        if (isset($this->modx->event->returnedValues) AND is_array($this->modx->event->returnedValues)) {
            $props = array_merge($props, $this->modx->event->returnedValues);
        }

        return [
            'success' => empty($message),
            'message' => $message,
            'data'    => $props,
        ];
    }


    public function setPlaceholders($subject, $prefix = 'msfavorite', $separator = '.', $restore = false)
    {
        $keys = [];
        $restored = [];
        if (is_array($subject)) {
            foreach ($subject as $key => $value) {
                $rv = $this->modx->toPlaceholder($key, $value, $prefix, $separator, $restore);
                if (isset($rv['keys'])) {
                    foreach ($rv['keys'] as $rvKey) {
                        $keys[] = $rvKey;
                    }
                }
                if ($restore === true AND isset($rv['restore'])) {
                    $restored = array_merge($restored, $rv['restore']);
                }
            }
        }
        $return = ['keys' => $keys];
        if ($restore === true) {
            $return['restore'] = $restored;
        }

        return $return;
    }


    protected function checkStat()
    {
        $key = strtolower(__CLASS__);
        /** @var modDbRegister $registry */
        $registry = $this->modx->getService('registry', 'registry.modRegistry')->getRegister('user', 'registry.modDbRegister');
        $registry->connect();
        $registry->subscribe('/modstore/'.md5($key));
        if ($res = $registry->read(['poll_limit' => 1, 'remove_read' => false])) {
            return;
        }
        $c = $this->modx->newQuery('transport.modTransportProvider', ['service_url:LIKE' => '%modstore%']);
        $c->select('username,api_key');
        /** @var modRest $rest */
        $rest = $this->modx->getService('modRest', 'rest.modRest', '', [
            'baseUrl'        => 'https://modstore.pro/extras',
            'suppressSuffix' => true,
            'timeout'        => 1,
            'connectTimeout' => 1,
        ]);

        if ($rest) {
            $level = $this->modx->getLogLevel();
            $this->modx->setLogLevel(modX::LOG_LEVEL_FATAL);
            $rest->post('stat', [
                'package'            => $key,
                'version'            => $this->version,
                'keys'               => ($c->prepare() AND $c->stmt->execute()) ? $c->stmt->fetchAll(PDO::FETCH_ASSOC) : [],
                'uuid'               => $this->modx->uuid,
                'database'           => $this->modx->config['dbtype'],
                'revolution_version' => $this->modx->version['code_name'].'-'.$this->modx->version['full_version'],
                'supports'           => $this->modx->version['code_name'].'-'.$this->modx->version['full_version'],
                'http_host'          => $this->modx->getOption('http_host'),
                'php_version'        => XPDO_PHP_VERSION,
                'language'           => $this->modx->getOption('manager_language'),
            ]);
            $this->modx->setLogLevel($level);
        }
        $registry->subscribe('/modstore/');
        $registry->send('/modstore/', [md5($key) => true], ['ttl' => 3600 * 24]);
    }

}


abstract class msfEvent
{

    /** @var modX $modx */
    public $modx;
    /** @var msFavorites $msFavorites */
    public $msFavorites;
    /** @var modUser $user */
    public $user;

    /** @var array $props */
    protected $props;
    /** @var bool $anon */
    protected $ctx;
    /** @var bool $anon */
    protected $ajax;
    /** @var bool $anon */
    protected $anon;


    public function __construct(msFavorites &$msFavorites, modSystemEvent $event, &$props)
    {
        $this->msFavorites = &$msFavorites;
        $this->modx = &$msFavorites->modx;
        $this->props =& $props;

        $this->ajax = (!empty($_REQUEST['service']) AND $_REQUEST['service'] == 'msfavorites') AND
        (!empty($_SERVER['HTTP_X_REQUESTED_WITH']) AND $_SERVER['HTTP_X_REQUESTED_WITH'] == 'XMLHttpRequest');

        $this->ctx = !empty($props['ctx']) ? (string)$props['ctx'] : $this->modx->context->key;
        $this->msFavorites->initialize($this->ctx);

        if ($this->modx->user->isAuthenticated($this->modx->context->key)) {
            $this->user = $this->modx->user;
            $this->anon = false;
        } else {
            $this->user = $this->modx->newObject('modUser');
            $this->anon = true;
        }
    }


    abstract public function run();


    public function process()
    {
        $output = $this->run();

        return $output;
    }


    public function success($msg = '', $data = [], $total = null)
    {
        return $this->msFavorites->response(true, $msg, $data, $total);
    }


    public function failure($msg = '', $data = [], $total = null)
    {

        return $this->msFavorites->response(false, $msg, $data, $total);
    }


    public function getProps()
    {
        return $this->props;
    }


    public function getProp($key, $default = null)
    {
        if (isset($this->props[$key])) {
            return $this->props[$key];
        }

        return $default;
    }


    public function getCtx()
    {
        return $this->ctx;
    }


    public function isAjax()
    {
        return $this->ajax;
    }


    public function isAnon()
    {
        return $this->anon;
    }

}


class msfEventOnHandleRequest extends msfEvent
{

    public function run()
    {
        if ($this->isAjax()) {
            $this->msFavorites->processContext($this->getCtx());
            $response = $this->msFavorites->processMethod($_REQUEST);
            $response = is_string($response) ? $response : json_encode($response);
            @session_write_close();
            exit($response);
        }
    }
}


class msfEventOnEmptyTrash extends msfEvent
{

    public function run()
    {
        if ($ids = $this->getProp('ids')) {

            $c = $this->modx->newQuery('msfFavoriteObject');
            $c->leftJoin('msfFavoriteKey', 'FavoriteKey');
            $c->leftJoin('msfFavoriteList', 'FavoriteList');
            $c->where(['FavoriteKey.key:IN' => $ids, 'FavoriteList.type' => 'resource']);
            $instances = $this->modx->getIterator('msfFavoriteObject', $c);
            if ($instances) {
                foreach ($instances as $instance) {
                    $instance->remove();
                }
            }

            /*$c = $this->modx->newQuery('msfFavoriteObject');
            $alias = $this->modx->getTableName('msfFavoriteObject');
            $c->setClassAlias($alias);
            $c->leftJoin('msfFavoriteKey', 'msfFavoriteKey', "{$alias}.`kid` = `msfFavoriteKey`.`id`");
            $c->leftJoin('msfFavoriteList', 'msfFavoriteList', "{$alias}.`lid` = `msfFavoriteList`.`id`");
            $c->where([
                'msfFavoriteKey.key:IN' => $ids,
                'msfFavoriteList.type' => 'resource',
            ]);
            $c->query['command'] = "DELETE {$alias}";
            $c->prepare();
            $c->stmt->execute();*/
        }
    }
}


class msfEventOnCacheUpdate extends msfEvent
{

    public function run()
    {
        if ($this->msFavorites->getOption('clear_anon', null)) {
            $limit = $this->msFavorites->getOption('limit_anon', null, 30, true);

            $time = new DateTime();
            $time = $time->modify('-'.(int)($limit).'day');
            $time = $time->format('Y-m-d H:i:s');

            $instances = $this->modx->getIterator('msfFavoriteObject', ['temp' => true, 'createdon:<=' => $time]);
            if ($instances) {
                foreach ($instances as $instance) {
                    $instance->remove();
                }
            }

        }
    }
}


class msfEventOnUserRemove extends msfEvent
{

    public function run()
    {
        if ($user = $this->getProp('user')) {

            /** @var msfFavoriteObject[] $instance */
            $instances = $this->modx->getIterator('msfFavoriteObject', ['anon' => false, 'uid' => $user->id]);
            if ($instances) {
                foreach ($instances as $instance) {
                    $instance->remove();
                }
            }

        }
    }
}


class msfEventOnWebPageInit extends msfEvent
{

    public function run()
    {
        if ($this->isAnon()) {
            $cookieVar = $this->msFavorites->getOption('cookie_var', null, 'msfavorites', true);
            setcookie($cookieVar, session_id(), time() + (86400 * 365), '/');
        }
    }

}


class msfEventOnWebLogin extends msfEvent
{

    public function run()
    {
        if ($user = $this->getProp('user')) {

            $cookieVar = $this->msFavorites->getOption('cookie_var', null, 'msfavorites', true);
            $session = !empty($_COOKIE[$cookieVar]) ? $_COOKIE[$cookieVar] : session_id();

            $table = $this->modx->getTableName('msfFavoriteObject');
            // вставляем новые
            if ($stmt = $this->modx->prepare("INSERT IGNORE INTO {$table} (lid,kid,uid,anon,temp,createdon) SELECT lid,kid,:uid,'0','0',createdon FROM {$table} WHERE uid=:session;")) {
                $stmt->bindValue(':uid', $user->id);
                $stmt->bindValue(':session', $session);
                if (!$stmt->execute()) {
                    $this->modx->log(xPDO::LOG_LEVEL_ERROR, "[msFavorites] Could not insert object \n".print_r($stmt->errorInfo(), true));
                }
            }
            // удаляем старые
            if ($stmt = $this->modx->prepare("DELETE FROM {$table} WHERE uid=:session;")) {
                $stmt->bindValue(':session', $session);
                if (!$stmt->execute()) {
                    $this->modx->log(xPDO::LOG_LEVEL_ERROR, "[msFavorites] Could not delete object \n".print_r($stmt->errorInfo(), true));
                }
            }

            $table = $this->modx->getTableName('msfFavoriteObjectExtra');
            // вставляем новые
            if ($stmt = $this->modx->prepare("INSERT IGNORE INTO {$table} (lid,kid,uid,extra) SELECT lid,kid,:uid,extra FROM {$table} WHERE uid=:session;")) {
                $stmt->bindValue(':uid', $user->id);
                $stmt->bindValue(':session', $session);
                if (!$stmt->execute()) {
                    $this->modx->log(xPDO::LOG_LEVEL_ERROR, "[msFavorites] Could not insert extra object \n".print_r($stmt->errorInfo(), true));
                }
            }
            // удаляем старые
            if ($stmt = $this->modx->prepare("DELETE FROM {$table} WHERE uid=:session;")) {
                $stmt->bindValue(':session', $session);
                if (!$stmt->execute()) {
                    $this->modx->log(xPDO::LOG_LEVEL_ERROR, "[msFavorites] Could not delete extra object \n".print_r($stmt->errorInfo(), true));
                }
            }

        }
    }
}


abstract class msfMethod
{

    /** @var modX $modx */
    public $modx;
    /** @var msFavorites $msFavorites */
    public $msFavorites;
    /** @var modUser $user */
    public $user;

    /** @var msfFavoriteList $list */
    public $list;
    /** @var msfFavoriteKey $key */
    public $key;
    /** @var msfFavoriteObject $object */
    public $object;

    /** @var string $beforeSaveEvent The name of the event to fire before run */
    public $beforeRunEvent = 'msFavoritesOnBeforeProcessFavorites';
    /** @var string $afterSaveEvent The name of the event to fire after run */
    public $afterRunEvent = 'msFavoritesOnProcessFavorites';

    /** @var array $props */
    protected $props;
    /** @var string $method */
    protected $method;
    /** @var string $bind */
    protected $bind;
    /** @var bool $anon */
    protected $anon;


    public function __construct(msFavorites &$msFavorites, $props)
    {
        $this->msFavorites = &$msFavorites;
        $this->modx = &$msFavorites->modx;
        $this->props =& $props;

        if ($this->modx->user->isAuthenticated($this->modx->context->key)) {
            $this->user = $this->modx->user;
            $this->anon = false;
        } else {
            $this->user = $this->modx->newObject('modUser');
            $this->anon = true;
        }

        $this->method = $this->getProp('method');

        if (in_array($this->method, ['add', 'remove'])) {
            $this->bind = 'one';
        } else {
            $this->bind = 'many';
        }

    }


    abstract public function run();


    public function process()
    {
        /* run the before run event and allow stoppage */
        $before = $this->fireBeforeRunEvent();
        if (!empty($before)) {
            return $this->failure($before);
        }

        $output = $this->run();

        /* run the before run event and allow stoppage */
        $after = $this->fireAfterRunEvent();
        if (!empty($after)) {
            return $this->failure($after);
        }

        return $output;
    }


    public function fireAfterRunEvent()
    {
        if (!empty($this->afterRunEvent)) {
            $response = $this->msFavorites->invokeEvent($this->afterRunEvent, [
                'method' => $this->getMethod(),
                'props'  => $this->getProps(),
                'self'   => &$this,
                'object' => &$this->object,
            ]);

            if (!$response['success']) {
                return $response['message'];
            }
        }

        return false;
    }


    public function fireBeforeRunEvent()
    {
        if (!empty($this->beforeRunEvent)) {
            $response = $this->msFavorites->invokeEvent($this->beforeRunEvent, [
                'method' => $this->getMethod(),
                'props'  => $this->getProps(),
                'self'   => &$this,
                'object' => &$this->object,
            ]);

            if (!$response['success']) {
                return $response['message'];
            }
        }

        return false;
    }


    public function success($msg = '', $data = [], $total = null)
    {
        return $this->msFavorites->response(true, $msg, $data, $total);
    }


    public function failure($msg = '', $data = [], $total = null)
    {

        return $this->msFavorites->response(false, $msg, $data, $total);
    }


    public function getMethod()
    {
        return $this->method;
    }


    public function getProps()
    {
        return $this->props;
    }


    public function getProp($key, $default = null)
    {
        if (isset($this->props[$key])) {
            return $this->props[$key];
        }

        return $default;
    }


    public function isAnon()
    {
        return $this->anon;
    }


    public function isTemp()
    {
        return $this->anon;
    }


    public function getBind()
    {
        return $this->bind;
    }


    public function getParam($key, $default = null)
    {
        if (isset($this->props[$key])) {
            return $this->props[$key];
        }

        return $default;
    }


    public function getLid()
    {
        $lid = null;
        /** @var msfFavoriteList $list */
        if (!$this->list OR !($this->list instanceof msfFavoriteList)) {
            $this->list = $this->modx->getObject('msfFavoriteList', $this->getProps());
        }
        if ($this->list) {
            $lid = $this->list->id;
        }

        return $lid;
    }


    public function getKid()
    {
        $kid = null;
        /** @var msfFavoriteKey $key */
        if (!$this->key OR !($this->key instanceof msfFavoriteKey)) {
            $this->key = $this->modx->getObject('msfFavoriteKey', $this->getProps());
        }
        if ($this->key) {
            $kid = $this->key->id;
        }

        return $kid;
    }


    public function getUid()
    {
        $uid = session_id();
        if (!$this->isAnon()) {
            $uid = $this->user->id;
        }

        return $uid;
    }


    public function getMeta()
    {
        $method = $this->getMethod();
        $bind = $this->getBind();
        $meta = [
            'method' => $method,
            'bind'   => $bind,
        ];

        if ($lid = $this->getLid()) {
            $meta['list'] = $this->list->list;
            $meta['type'] = $this->list->type;
        }
        if ($kid = $this->getKid()) {
            $meta['key'] = $this->key->key;
        }
        if ($uid = $this->getUid()) {
            $meta['total'] = $this->modx->getCount('msfFavoriteObject', ['lid' => $lid, 'uid' => $uid]);
        }

        return $meta;
    }


    public function getResult()
    {
        $result = $keys = $users = [];

        if (!$lid = $this->getLid() OR !$uid = $this->getUid()) {
            return $this->failure('', ['required' => ['list', 'type']]);
        }

        $kid = $this->getKid();

        $c = $this->modx->newQuery('msfFavoriteObject');
        $c->leftJoin('msfFavoriteKey', 'FavoriteKey');
        $c->where(['lid' => $lid, 'uid' => $uid]);

        /* if (empty($kid)) {

         } else {
             $c->where(['lid' => $lid, 'uid' => $uid, 'kid' => $kid]);
         }*/
        $c->limit(0);
        $c->select('key');//count(Favorite.uid) as users
        if ($c->prepare() AND $c->stmt->execute()) {
            while ($row = $c->stmt->fetch(PDO::FETCH_ASSOC)) {
                if (!empty($row['key'])) {
                    $keys[$row['key']] = 1;
                }
            }
        } else {
            $this->modx->log(xPDO::LOG_LEVEL_ERROR, print_r($c->stmt->errorInfo(), 1));
        }

        $c = $this->modx->newQuery('msfFavoriteObject');
        $c->leftJoin('msfFavoriteKey', 'FavoriteKey');
        $c->where(['lid' => $lid]);

        /*if (empty($kid)) {
            $c->where(['lid' => $lid]);
        } else {
            $c->where(['lid' => $lid, 'kid' => $kid]);
        }*/
        $c->limit(0);
        $c->groupby('kid');
        $c->select('key,count(uid) as users');
        if ($c->prepare() AND $c->stmt->execute()) {
            while ($row = $c->stmt->fetch(PDO::FETCH_ASSOC)) {
                if (!empty($row['key'])) {
                    $users[$row['key']] = (int)$row['users'];
                }
            }
        } else {
            $this->modx->log(xPDO::LOG_LEVEL_ERROR, print_r($c->stmt->errorInfo(), 1));
        }

        $result = ['keys' => $keys, 'users' => $users];

        return $result;
    }


    public function getData($data = [])
    {
        $data = array_merge($data, [
            'meta'   => $this->getMeta(),
            'props'  => $this->getProps(),
            'result' => $this->getResult(),
        ]);

        return $data;
    }

}


class msfMethodGet extends msfMethod
{

    public function run()
    {
        if (!$lid = $this->getLid() OR !$uid = $this->getUid()) {
            return $this->failure('', ['required' => ['list', 'type']]);
        }

        return $this->success('', $this->getData());
    }

}


class msfMethodAdd extends msfMethod
{

    public function run()
    {
        if (!$lid = $this->getLid() OR !$kid = $this->getKid() OR !$uid = $this->getUid()) {
            return $this->failure('', ['required' => ['list', 'type', 'key']]);
        }

        $this->object = $this->modx->getObject('msfFavoriteObject', ['lid' => $lid, 'kid' => $kid, 'uid' => $uid]);
        if ($this->object) {
            $this->object->set('anon', $this->isAnon());
            $this->object->set('temp', $this->isTemp());
            $this->object->set('extra', $this->getProp('extra'));
            if ($this->object->save()) {
                return $this->success('', $this->getData());
            }
        }

        return $this->failure('');
    }

}


class msfMethodRemove extends msfMethod
{

    public function run()
    {
        if (!$lid = $this->getLid() OR !$kid = $this->getKid() OR !$uid = $this->getUid()) {
            return $this->failure('', ['required' => ['list', 'type', 'key']]);
        }

        $this->object = $this->modx->getObject('msfFavoriteObject', ['lid' => $lid, 'kid' => $kid, 'uid' => $uid]);
        if ($this->object) {
            if ($this->object->remove()) {
                return $this->success('', $this->getData());
            }
        }

        return $this->failure('');
    }

}


class msfMethodClear extends msfMethod
{

    public function run()
    {
        if (!$lid = $this->getLid() OR !$uid = $this->getUid()) {
            return $this->failure('', ['required' => ['list', 'type']]);
        }

        /** @var msfFavoriteObject[] $instance */
        $instances = $this->modx->getIterator('msfFavoriteObject', ['lid' => $lid, 'uid' => $uid]);
        if ($instances) {
            foreach ($instances as $instance) {
                $this->object = $instance;
                $this->object->remove();
            }
        }

        return $this->success('', $this->getData());
    }
}


if (!function_exists('array_column')) {
    function array_column($array, $column_name)
    {
        return array_map(function ($element) use ($column_name) {
            return $element[$column_name];
        }, $array);
    }
}