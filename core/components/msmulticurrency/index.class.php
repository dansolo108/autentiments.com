<?php
/**
 * @package msmulticurrency
 * @subpackage controllers
 */
require_once dirname(__FILE__) . '/model/msmulticurrency/msmc.class.php';

class IndexManagerController extends modExtraManagerController {
    public static function getDefaultController() { return 'index'; }
}


abstract class MsMCMainController extends modExtraManagerController {
    /** @var MsMC $msmc */
    public $msmc;
    public static function getInstance(modX &$modx, $className, array $config = array()) {
        $action = call_user_func(array($className,'getDefaultController'));
        if (isset($_REQUEST['a'])) {
            $action = str_replace(array('../','./','.','-','@'),'',$_REQUEST['a']);
        }
        $className = self::getControllerClassName($action,$config['namespace']);
        $classPath = $config['namespace_path'].'controllers/default/'.$action.'.class.php';
        require_once $classPath;
        /** @var modManagerController $controller */
        $controller = new $className($modx,$config);
        return $controller;
    }
    public function initialize() {
        $this->msmc = new MsMC($this->modx);
        $this->addJavascript($this->msmc->config['jsUrl'].'mgr/msmc.js');
        $this->addHtml('<script type="text/javascript">
        Ext.onReady(function() {
            MsMC.config = '.$this->modx->toJSON($this->msmc->config).';
        });
        </script>');
        return parent::initialize();
    }

    public function getLanguageTopics() {
        return array('msmulticurrency:default');
    }

    public function checkPermissions() { return true;}
}