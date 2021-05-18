<?php

abstract class msPromoCodeMainController extends modExtraManagerController
{
    public $msPromoCode;
    public $miniShop2;

    /**
     */
    public function initialize()
    {
        if (!include_once MODX_CORE_PATH . 'components/minishop2/model/minishop2/minishop2.class.php') {
            throw new Exception('You must install miniShop2 first');
        }

        $version = $this->modx->getVersionData();
        $modx23 = !empty($version) && version_compare($version['full_version'], '2.3.0', '>=');
        if (!$modx23) {
            $this->addCss(MODX_ASSETS_URL . 'components/msearch2/css/mgr/font-awesome.min.css');
        }

        require_once MODX_CORE_PATH . 'components/mspromocode/model/mspromocode/mspromocode.class.php';

        $this->msPromoCode = new msPromoCode($this->modx);
        $this->miniShop2 = new miniShop2($this->modx);

        $this->addJavascript(MODX_MANAGER_URL . 'assets/modext/util/datetime.js');
        //$this->addCss($this->msPromoCode->config['cssUrl'] .'mgr/main.css');
        $this->addJavascript($this->msPromoCode->config['jsUrl'] . 'mgr/mspromocode.js');
        //$this->addJavascript($this->msPromoCode->config['jsUrl'] .'mgr/misc/extjs.utils.js');

        $this->addJavascript($this->miniShop2->config['jsUrl'] . 'mgr/minishop2.js');
        $this->addJavascript($this->miniShop2->config['jsUrl'] . 'mgr/misc/ms2.utils.js');
        $this->addJavascript($this->miniShop2->config['jsUrl'] . 'mgr/misc/ms2.combo.js');
        $this->addLastJavascript($this->miniShop2->config['jsUrl'] . 'mgr/misc/strftime-min-1.3.js');

        $this->addHtml('
        <script type="text/javascript">
            MODx.modx23 = ' . (int)$modx23 . ';
            miniShop2.config = ' . $this->modx->toJSON($this->miniShop2->config) . ';
            miniShop2.config.connector_url = "' . $this->miniShop2->config['connectorUrl'] . '";
            msPromoCode.config = ' . $this->modx->toJSON($this->msPromoCode->config) . ';
            msPromoCode.config.connector_url = "' . $this->msPromoCode->config['connectorUrl'] . '";
            msPromoCode.config.regexp_gen_code = "' . $this->modx->getOption('mspromocode_regexp_gen_code') . '";
        </script>
        ');

        parent::initialize();
    }

    /**
     * @return array
     */
    public function getLanguageTopics()
    {
        return array('mspromocode:default');
    }

    /**
     * @return bool
     */
    public function checkPermissions()
    {
        return true;
    }
}

/**
 * The home manager controller for msPromoCode.
 */
class msPromoCodeHomeManagerController extends msPromoCodeMainController
{
    /**
     * @var msPromoCode $msPromoCode
     */
    public $msPromoCode;

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
        return $this->modx->lexicon('mspromocode');
    }

    /**
     */
    public function loadCustomCssJs()
    {
        // css
        $this->addCss($this->msPromoCode->config['cssUrl'] . 'mgr/main.css');
        $this->addCss($this->msPromoCode->config['cssUrl'] . 'mgr/bootstrap.buttons.css');

        // misc
        $this->addJavascript($this->msPromoCode->config['jsUrl'] . 'mgr/misc/utils.js');
        $this->addJavascript($this->msPromoCode->config['jsUrl'] . 'mgr/misc/mspromocode.combo.js');

        // inject
        $this->addJavascript($this->msPromoCode->config['jsUrl'] . 'mgr/inject/resources.grid.tab.js');
        $this->addJavascript($this->msPromoCode->config['jsUrl'] . 'mgr/inject/conditions.grid.tab.js');
        $this->addJavascript($this->msPromoCode->config['jsUrl'] . 'mgr/inject/orders.grid.tab.js');

        // widgets
        $this->addJavascript($this->msPromoCode->config['jsUrl'] . 'mgr/widgets/actions.grid.js');
        $this->addJavascript($this->msPromoCode->config['jsUrl'] . 'mgr/widgets/actions.windows.js');
        //
        $this->addJavascript($this->msPromoCode->config['jsUrl'] . 'mgr/widgets/coupons.grid.js');
        $this->addJavascript($this->msPromoCode->config['jsUrl'] . 'mgr/widgets/coupons.windows.js');

        // controllers
        $this->addJavascript($this->msPromoCode->config['jsUrl'] . 'mgr/widgets/home.panel.js');
        $this->addJavascript($this->msPromoCode->config['jsUrl'] . 'mgr/sections/home.js');

        $this->addHtml('<script type="text/javascript">
			Ext.onReady(function() {
				MODx.load({ xtype: "mspromocode-page-home"});
			});
		</script>');
    }

    /**
     * @return string
     */
    public function getTemplateFile()
    {
        return $this->msPromoCode->config['templatesPath'] . 'home.tpl';
    }
}
