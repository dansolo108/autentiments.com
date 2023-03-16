<?php
require_once(dirname(dirname(__DIR__)) . '/vendor/autoload.php');

/**
 * MODx Polylang Class
 *
 * @package polylang
 */
class Polylang
{
    const version = '1.1.1-pl';
    /** @var modX $modx */
    public $modx;
    /** @var PolylangTools $tools */
    protected $tools;
    /** @var PolylangTranslator $translator */
    protected $translator;
    /** @var string $namespace */
    protected $namespace = 'polylang';


    /**
     * Polylang constructor.
     * @param modX $modx
     * @param array $config
     */
    function __construct(modX &$modx, array $config = array())
    {
        $this->modx =& $modx;
        $this->modx->lexicon->load('polylang:default', 'polylang:site');
        $corePath = $modx->getOption('polylang.core_path', $config, $modx->getOption('core_path', null, MODX_CORE_PATH) . 'components/polylang/');
        $assetsUrl = $modx->getOption('polylang.assets_url', $config, $modx->getOption('assets_url') . 'components/polylang/');
        $assetsPath = $modx->getOption('polylang.assets_path', $config, $modx->getOption('assets_path', null, MODX_ASSETS_PATH) . 'components/polylang/');
        $this->config = array_merge(array(
            'chunksPath' => $corePath . 'elements/chunks/',
            'controllersPath' => $corePath . 'controllers/',
            'corePath' => $corePath,
            'assetsUrl' => $assetsUrl,
            'assetsPath' => $assetsPath,
            'modelPath' => $corePath . 'model/',
            'processorsPath' => $corePath . 'processors/',
            'jsUrl' => $assetsUrl . 'js/',
            'cssUrl' => $assetsUrl . 'css/',
            'templatesPath' => $corePath . 'elements/templates/',
            'connector_url' => $assetsUrl . 'connector.php',
            'actionUrl' => $assetsUrl . 'action.php',
            'handlersPath' => $corePath . 'handlers/',
            'jsonResponse' => true,
            'prepareResponse' => false,
            'cacheTime' => $this->modx->getOption('polylang_cache_time', null, 1800, true),
            'toolsHandler' => $this->modx->getOption('polylang_tools_handler_class', null, 'PolylangTools', true),
            'dbHelperHandler' => $this->modx->getOption('polylang_dbhelper_handler_class', null, 'PolylangDbHelper', true),
        ), $config);
        $this->modx->addPackage('polylang', $this->config['modelPath']);
        // $this->checkStat();
    }

    /**
     * @return string
     */
    public function getNamespace()
    {
        return $this->namespace;
    }

    /**
     * Shorthand for the call of processor
     *
     * @access public
     *
     * @param string $action Path to processor
     * @param array $data Data to be transmitted to the processor
     *
     * @return mixed The result of the processor
     */
    public function runProcessor($action, $data = array())
    {
        if (empty($action)) {
            return $this->modx->error->failure();
        }
        if ($this->modx->context->get('key') !== 'mgr') {
            $action = 'web/' . $action;
        }
        $this->modx->error->reset();
        $processorsPath = !empty($this->config['processorsPath'])
            ? $this->config['processorsPath']
            : MODX_CORE_PATH . 'components/polylang/processors/';

        $response = $this->modx->runProcessor($action, $data, array(
            'processors_path' => $processorsPath,
        ));

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
                $message = $this->modx->lexicon('err_unknown');
            }
            $output = $this->failure($message);
        }
        if ($this->config['jsonResponse'] and is_array($output)) {
            $output = $this->modx->toJSON($output);
        } else if (!$this->config['jsonResponse'] and !is_array($output)) {
            $output = $this->modx->fromJSON($output);
        }
        return $output;
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
            'message' => $message,
            'data' => $data,
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
            'message' => $message,
            'data' => $data,
        );

        return $this->config['jsonResponse'] ? $this->modx->toJSON($response) : $response;
    }

    /**
     * @param array $config
     * @return PolylangTools
     */
    public function getTools(array $config = array())
    {
        if (!is_object($this->tools) || !($this->tools instanceof PolylangTools)) {
            $toolsClass = $this->modx->loadClass('tools.' . $this->config['toolsHandler'], $this->config['handlersPath'], true, true);
            if ($toolsClass) {
                $config = array_merge($this->config, $config);
                $this->tools = new $toolsClass($this, $config);
            }
        }
        return $this->tools;
    }

    /**
     * @param array $config
     * @return PolylangTranslator
     */
    public function getTranslator(array $config = array())
    {
        if (!is_object($this->translator) || !($this->translator instanceof PolylangTranslatorInterface)) {
            $config = array_merge($this->config, $config);
            $translatorHandler = $this->modx->getOption('polylang_class_translator', $config, 'PolylangTranslatorGoogle', true);
            $translatorClass = $this->modx->loadClass('translator.' . $translatorHandler, $this->config['handlersPath'], true, true);
            if ($translatorClass) {
                $this->translator = new $translatorClass($this, $config);
            }
        }
        return $this->translator;
    }


    /**
     * @param int $template
     * @return bool
     */
    public function isWorkingTemplates($template)
    {
        $templates = $this->getTools()->explodeAndClean($this->modx->getOption('polylang_working_templates', null, ''));
        return empty($templates) || in_array($template, $templates);
    }

    /**
     * @param int $rid
     * @param modManagerController $controller
     */
    public function loadControllerJsCss($rid, modManagerController &$controller)
    {
        $controller->addLexiconTopic('polylang:default');
        $defaultLanguage = $this->modx->getOption('cultureKey');
        $config = array_merge($this->config, array(
            'rid' => $rid,
            'showTranslateBtn' => $this->modx->getOption('polylang_show_translate_btn'),
            'disallowTranslationCompletedField' => $this->modx->getOption('polylang_disallow_translation_completed_field'),
            'defaultLanguage' => $this->modx->getOption('polylang_default_language', null, $defaultLanguage, true),
            'useResourceEditorStatus' => $this->modx->getOption('polylang_use_resource_editor_status', null, 1, true),
            'editorHeight' => $this->modx->getOption('polylang_editor_height', null, 300, true),
        ));
        $controller->addHtml("<script type='text/javascript'>  Polylang.config = {$this->modx->toJSON($config)}</script>");
        $controller->addJavascript($this->config['jsUrl'] . 'mgr/polylang.js');
        $controller->addJavascript($this->config['jsUrl'] . 'mgr/misc/combo.js');
        $controller->addJavascript($this->config['jsUrl'] . 'mgr/misc/strftime-min-1.3.js');
        $controller->addJavascript($this->config['jsUrl'] . 'mgr/misc/default.grid.js');
        $controller->addJavascript($this->config['jsUrl'] . 'mgr/misc/default.window.js');
        $controller->addJavascript($this->config['jsUrl'] . 'mgr/polylangcontent/polylangcontent.grid.js');
        $controller->addJavascript($this->config['jsUrl'] . 'mgr/inject/inject.js');

        $controller->addCss($this->config['cssUrl'] . 'mgr/main.css');
        $controller->addCss($this->config['cssUrl'] . 'mgr/bootstrap.buttons.css');
        $controller->addCss($this->config['assetsUrl'] . 'vendor/fontawesome/css/font-awesome.min.css');

    }

    /**
     * @param modManagerController $controller
     */
    public function loadControllerTVJsCss(modManagerController &$controller)
    {
        $controller->addLexiconTopic('polylang:default');
        $controller->addJavascript($this->config['jsUrl'] . 'mgr/inject/inject.tv.js');
    }

    /**
     * @param modManagerController $controller
     */
    public function loadControllerMs2OptionJsCss(modManagerController &$controller)
    {
        $controller->addLexiconTopic('polylang:default');
        $controller->addJavascript($this->config['jsUrl'] . 'mgr/inject/inject.option.js');
    }


    public function extendTvModel()
    {
        $this->extendModel('tv.mysql.inc.php');
    }

    public function extendMs2OptionModel()
    {
        if ($this->getTools()->hasAddition('minishop2')) {
            $this->extendModel('option.mysql.inc.php');
        }
    }

    public function extendModel($filename)
    {
        $include = include_once($this->config['modelPath'] . 'extend/' . $filename);
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