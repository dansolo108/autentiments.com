<?php

/**
 * The home manager controller for msOptionHexColor.
 *
 */
class msOptionHexColorHomeManagerController extends modExtraManagerController
{
    /** @var msOptionHexColor $msOptionHexColor */
    public $msOptionHexColor;


    /**
     *
     */
    public function initialize()
    {
        $this->msOptionHexColor = $this->modx->getService('msOptionHexColor', 'msOptionHexColor', MODX_CORE_PATH . 'components/msoptionhexcolor/model/');
        parent::initialize();
    }


    /**
     * @return array
     */
    public function getLanguageTopics()
    {
        return ['msoptionhexcolor:default'];
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
        return $this->modx->lexicon('msoptionhexcolor');
    }


    /**
     * @return void
     */
    public function loadCustomCssJs()
    {
        $this->addCss($this->msOptionHexColor->config['cssUrl'] . 'mgr/main.css');
        $this->addJavascript($this->msOptionHexColor->config['jsUrl'] . 'mgr/msoptionhexcolor.js');
        $this->addJavascript($this->msOptionHexColor->config['jsUrl'] . 'mgr/misc/utils.js');
        $this->addJavascript($this->msOptionHexColor->config['jsUrl'] . 'mgr/misc/combo.js');
        $this->addJavascript($this->msOptionHexColor->config['jsUrl'] . 'mgr/widgets/sellers.grid.js');
        $this->addJavascript($this->msOptionHexColor->config['jsUrl'] . 'mgr/widgets/sellers.windows.js');
        $this->addJavascript($this->msOptionHexColor->config['jsUrl'] . 'mgr/widgets/home.panel.js');
        $this->addJavascript($this->msOptionHexColor->config['jsUrl'] . 'mgr/sections/home.js');

        $this->addHtml('<script type="text/javascript">
        msOptionHexColor.config = ' . json_encode($this->msOptionHexColor->config) . ';
        msOptionHexColor.config.connector_url = "' . $this->msOptionHexColor->config['connectorUrl'] . '";
        Ext.onReady(function() {MODx.load({ xtype: "msoptionhexcolor-page-home"});});
        </script>');
    }


    /**
     * @return string
     */
    public function getTemplateFile()
    {
        $this->content .= '<div id="msoptionhexcolor-panel-home-div"></div>';

        return '';
    }
}