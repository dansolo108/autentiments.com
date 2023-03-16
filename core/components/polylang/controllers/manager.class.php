<?php
/**
 * @package polylang
 * @subpackage controllers
 */


class PolylangManagerController extends modExtraManagerController
{
    /** @var Polylang $polylang */
    public $polylang;

    public function initialize()
    {
        $this->polylang = $this->modx->getService('polylang', 'Polylang');

        $this->addJavascript($this->polylang->config['jsUrl'] . 'mgr/polylang.js');
        $this->addJavascript($this->polylang->config['jsUrl'] . 'mgr/misc/strftime-min-1.3.js');
        $this->addJavascript($this->polylang->config['jsUrl'] . 'mgr/misc/clipboard.js');
        $this->addJavascript($this->polylang->config['jsUrl'] . 'mgr/misc/default.grid.js');
        $this->addJavascript($this->polylang->config['jsUrl'] . 'mgr/misc/default.window.js');
        $this->addJavascript($this->polylang->config['jsUrl'] . 'mgr/misc/combo.js');

        $this->addCss($this->polylang->config['cssUrl'] . 'mgr/main.css');
        $this->addCss($this->polylang->config['cssUrl'] . 'mgr/bootstrap.buttons.css');
        $this->addCss($this->polylang->config['assetsUrl'] . 'vendor/fontawesome/css/font-awesome.min.css');


        $config = $this->polylang->config;
        $config['currency'] = array(
            'enable' => false,
            'connector_url' => ''
        );
        if ($this->polylang->getTools()->hasAddition('msmulticurrency')) {
            /** @var MsMC $msmc */
            $msmc = $this->modx->getService('msmulticurrency', 'MsMC');
            if ($msmc) {
                $config['currency']['enable'] = true;
                $config['currency']['connector_url'] = $msmc->config['connectorUrl'];
                $config['currency']['baseCurrency'] = $msmc->config['baseCurrencyId'];
                $config['currency']['baseCurrencySet'] = $msmc->config['baseCurrencySetId'];
            }
        }
        $this->addHtml('<script type="text/javascript">
        Ext.onReady(function() {
            Polylang.config = ' . $this->modx->toJSON($config) . ';
        });
        </script>');

        return parent::initialize();
    }

    public function getLanguageTopics()
    {
        return array('polylang:default');
    }

    public function checkPermissions()
    {
        return true;
    }
}