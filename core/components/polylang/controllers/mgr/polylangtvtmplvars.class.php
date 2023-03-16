<?php
if (!class_exists('PolylangManagerController')) {
    require_once dirname(dirname(__FILE__)) . '/manager.class.php';
}


class PolylangMgrPolylangTvTmplvarsManagerController extends PolylangManagerController
{

    public function loadCustomCssJs()
    {

        $this->addJavascript($this->polylang->config['jsUrl'] . 'mgr/polylangtvtmplvars/polylangtvtmplvars.grid.js');
        $this->addJavascript($this->polylang->config['jsUrl'] . 'mgr/polylangtvtmplvars/polylangtvtmplvars.panel.js');
        $this->addHtml('<script type="text/javascript">
        // <![CDATA[
        Ext.onReady(function() {
            MODx.add({
              xtype: "polylang-panel-polylangtvtmplvars",
              });
        });
        // ]]>
        </script>');
        $this->modx->invokeEvent('polylangOnManagerCustomCssJs', array('controller' => &$this, 'page' => 'polylangtvtmplvars'));
    }

    public function getPageTitle()
    {
        return $this->modx->lexicon('polylang_page_title_polylangtvtmplvars');
    }
}