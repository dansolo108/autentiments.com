<?php
if (!class_exists('PolylangManagerController')) {
    require_once dirname(dirname(__FILE__)) . '/manager.class.php';
}


class PolylangMgrPolylangLanguageManagerController extends PolylangManagerController
{

    public function loadCustomCssJs()
    {

        $this->addJavascript($this->polylang->config['jsUrl'] . 'mgr/polylangfield/polylangfield.filter.js');
        $this->addJavascript($this->polylang->config['jsUrl'] . 'mgr/polylangfield/polylangfield.grid.js');
        $this->addJavascript($this->polylang->config['jsUrl'] . 'mgr/polylangtvtmplvars/polylangtvtmplvars.grid.js');
        $this->addJavascript($this->polylang->config['jsUrl'] . 'mgr/polylangtvtmplvars/polylangtvtmplvars.filter.js');
        $this->addJavascript($this->polylang->config['jsUrl'] . 'mgr/polylanglanguage/polylanglanguage.grid.js');
        $this->addJavascript($this->polylang->config['jsUrl'] . 'mgr/polylanglanguage/polylanglanguage.panel.js');
        $this->addHtml('<script type="text/javascript">
        // <![CDATA[
        Ext.onReady(function() {
            MODx.add({
              xtype: "polylang-panel-polylanglanguage",
              });
        });
        // ]]>
        </script>');

        $this->modx->invokeEvent('polylangOnManagerCustomCssJs', array('controller' => &$this, 'page' => 'polylanglanguage'));
    }

    public function getPageTitle()
    {
        return $this->modx->lexicon('polylang_page_polylanglanguage_title');
    }
}