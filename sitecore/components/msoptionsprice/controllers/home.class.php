<?php

/**
 * The home manager controller for msoptionsprice.
 *
 */
class msoptionspriceHomeManagerController extends msoptionspriceMainController
{
    /* @var msoptionsprice $msoptionsprice */
    public $msoptionsprice;


    /**
     * @param array $scriptProperties
     */
    public function process(array $scriptProperties = array())
    {
    }


    /**
     * @return null|string
     */
    public function getPageTitle()
    {
        return $this->modx->lexicon('msoptionsprice');
    }


    /**
     * @return void
     */
    public function loadCustomCssJs()
    {
        $this->addCss($this->msoptionsprice->config['cssUrl'] . 'mgr/main.css');
        $this->addCss($this->msoptionsprice->config['cssUrl'] . 'mgr/bootstrap.buttons.css');
        $this->addJavascript($this->msoptionsprice->config['jsUrl'] . 'mgr/misc/utils.js');
        $this->addJavascript($this->msoptionsprice->config['jsUrl'] . 'mgr/widgets/items.grid.js');
        $this->addJavascript($this->msoptionsprice->config['jsUrl'] . 'mgr/widgets/items.windows.js');
        $this->addJavascript($this->msoptionsprice->config['jsUrl'] . 'mgr/widgets/home.panel.js');
        $this->addJavascript($this->msoptionsprice->config['jsUrl'] . 'mgr/sections/home.js');
        $this->addHtml('<script type="text/javascript">
		Ext.onReady(function() {
			MODx.load({ xtype: "msoptionsprice-page-home"});
		});
		</script>');
    }


    /**
     * @return string
     */
    public function getTemplateFile()
    {
        return $this->msoptionsprice->config['templatesPath'] . 'home.tpl';
    }
}