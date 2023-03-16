<?php
if (!class_exists('PolylangManagerController')) {
    require_once dirname(dirname(__FILE__)) . '/manager.class.php';
}


class PolylangMgrPolylangProductOptionManagerController extends PolylangManagerController
{

    public function loadCustomCssJs()
    {

        $this->addJavascript($this->polylang->config['jsUrl'] . 'mgr/polylangproductoption/polylangproductoption.grid.js');
        $this->addJavascript($this->polylang->config['jsUrl'] . 'mgr/polylangproductoption/polylangproductoption.panel.js');
        $this->addHtml('<script type="text/javascript">
        // <![CDATA[
        Ext.onReady(function() {
            MODx.add({
              xtype: "polylang-panel-polylangproductoption",
              });
        });
        // ]]>
        </script>');
        $this->modx->invokeEvent('polylangOnManagerCustomCssJs', array('controller' => &$this, 'page' => 'polylangproductoption'));
    }

    public function getPageTitle()
    {
        return $this->modx->lexicon('polylang_page_title_polylangproductoption');
    }
}