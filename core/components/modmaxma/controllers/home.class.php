<?php

/**
 * The home manager controller for modMaxma.
 *
 */
class modMaxmaHomeManagerController extends modExtraManagerController
{
    /** @var modMaxma $modMaxma */
    public $modMaxma;


    /**
     *
     */
    public function initialize()
    {
        $this->modMaxma = $this->modx->getService('modMaxma', 'modMaxma', MODX_CORE_PATH . 'components/modmaxma/model/');
        parent::initialize();
    }


    /**
     * @return array
     */
    public function getLanguageTopics()
    {
        return ['modmaxma:default'];
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
        return $this->modx->lexicon('modmaxma');
    }


    /**
     * @return void
     */
    public function loadCustomCssJs()
    {
        $this->addCss($this->modMaxma->config['cssUrl'] . 'mgr/main.css');
        $this->addJavascript($this->modMaxma->config['jsUrl'] . 'mgr/modmaxma.js');
        $this->addJavascript($this->modMaxma->config['jsUrl'] . 'mgr/misc/utils.js');
        $this->addJavascript($this->modMaxma->config['jsUrl'] . 'mgr/misc/combo.js');
        $this->addJavascript($this->modMaxma->config['jsUrl'] . 'mgr/widgets/items.grid.js');
        $this->addJavascript($this->modMaxma->config['jsUrl'] . 'mgr/widgets/items.windows.js');
        $this->addJavascript($this->modMaxma->config['jsUrl'] . 'mgr/widgets/home.panel.js');
        $this->addJavascript($this->modMaxma->config['jsUrl'] . 'mgr/sections/home.js');

        $this->addHtml('<script type="text/javascript">
        modMaxma.config = ' . json_encode($this->modMaxma->config) . ';
        modMaxma.config.connector_url = "' . $this->modMaxma->config['connectorUrl'] . '";
        Ext.onReady(function() {MODx.load({ xtype: "modmaxma-page-home"});});
        </script>');
    }


    /**
     * @return string
     */
    public function getTemplateFile()
    {
        $this->content .= '<div id="modmaxma-panel-home-div"></div>';

        return '';
    }
}