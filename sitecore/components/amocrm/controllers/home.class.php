<?php

/**
 * The home manager controller for amocrm.
 *
 */
class amocrmHomeManagerController extends modExtraManagerController
{
    /** @var amocrm $amocrm */
    public $amocrm;


    /**
     *
     */
    public function initialize()
    {
        $this->amocrm = $this->modx->getService('amocrm', 'amocrm', MODX_CORE_PATH . 'components/amocrm/model/');
        parent::initialize();
    }


    /**
     * @return array
     */
    public function getLanguageTopics()
    {
        return ['amocrm:default'];
    }


    /**
     * @return bool
     */
    public function checkPermissions()
    {
        return true;
    }


    /**
     * @return null|string
     */
    public function getPageTitle()
    {
        return $this->modx->lexicon('amocrm');
    }


    /**
     * @return void
     */
    public function loadCustomCssJs()
    {
        $this->addCss($this->amocrm->config['cssUrl'] . 'mgr/main.css');
        $this->addJavascript($this->amocrm->config['jsUrl'] . 'mgr/amocrm.js');
        $this->addJavascript($this->amocrm->config['jsUrl'] . 'mgr/misc/utils.js');
        $this->addJavascript($this->amocrm->config['jsUrl'] . 'mgr/misc/combo.js');
        $this->addJavascript($this->amocrm->config['jsUrl'] . 'mgr/widgets/items.grid.js');
        $this->addJavascript($this->amocrm->config['jsUrl'] . 'mgr/widgets/items.windows.js');
        $this->addJavascript($this->amocrm->config['jsUrl'] . 'mgr/widgets/home.panel.js');
        $this->addJavascript($this->amocrm->config['jsUrl'] . 'mgr/sections/home.js');

        $this->addHtml('<script type="text/javascript">
        amocrm.config = ' . json_encode($this->amocrm->config) . ';
        amocrm.config.connector_url = "' . $this->amocrm->config['connectorUrl'] . '";
        Ext.onReady(function() {MODx.load({ xtype: "amocrm-page-home"});});
        </script>');
    }


    /**
     * @return string
     */
    public function getTemplateFile()
    {
        $this->content .= '<div id="amocrm-panel-home-div"></div>';

        return '';
    }
}