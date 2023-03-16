<?php
require_once dirname(dirname(dirname(__FILE__))) . '/index.class.php';

class ControllersMgrMultiCurrencyManagerController extends MsMCMainController
{
    public static function getDefaultController()
    {
        return 'multicurrency';
    }
}

class msMultiCurrencyMultiCurrencyManagerController extends MsMCMainController
{
    public function process(array $scriptProperties = array())
    {

    }

    public function loadCustomCssJs()
    {
        $mgrUrl = $this->modx->getOption('manager_url', null, MODX_MANAGER_URL);
        $this->addJavascript($this->msmc->config['jsUrl'] . 'mgr/misc/default.grid.js');
        $this->addJavascript($this->msmc->config['jsUrl'] . 'mgr/misc/search.combo.js');
        $this->addJavascript($this->msmc->config['jsUrl'] . 'mgr/widgets/set.combo.js');
        $this->addJavascript($this->msmc->config['jsUrl'] . 'mgr/widgets/currency.combo.js');
        $this->addJavascript($this->msmc->config['jsUrl'] . 'mgr/widgets/currency.checkboxgroup.js');
        $this->addJavascript($this->msmc->config['jsUrl'] . 'mgr/widgets/multicurrency.grid.js');
        $this->addJavascript($this->msmc->config['jsUrl'] . 'mgr/widgets/multicurrencyset.grid.js');
        $this->addJavascript($this->msmc->config['jsUrl'] . 'mgr/widgets/multicurrencysetmember.grid.js');
        $this->addJavascript($this->msmc->config['jsUrl'] . 'mgr/widgets/multicurrencyprovider.grid.js');
        $this->addJavascript($this->msmc->config['jsUrl'] . 'mgr/widgets/multicurrencyprovidercurrency.grid.js');
        $this->addJavascript($this->msmc->config['jsUrl'] . 'mgr/widgets/multicurrency.panel.js');
        $this->addLastJavascript($this->msmc->config['jsUrl'] . 'mgr/sections/multicurrency.js');

        $this->addCss($this->msmc->config['assetsUrl'] . 'vendor/fontawesome/css/font-awesome.min.css');
        $this->addCss($this->msmc->config['cssUrl'] . 'mgr/bootstrap.buttons.css');
        $this->addCss($this->msmc->config['cssUrl'] . 'mgr/main.css');
    }

    public function getLanguageTopics()
    {
        return array(
            'msmulticurrency:default',
            'msmulticurrency:multicurrency',
            'msmulticurrency:multicurrencyset',
            'msmulticurrency:multicurrencysetmember',
        );
    }

    public function getPageTitle()
    {
        return $this->modx->lexicon('msmulticurrency.page.multicurrency_title');
    }

    public function getTemplateFile()
    {
        return $this->msmc->config['templatesPath'] . 'mgr/multicurrency.tpl';
    }
}