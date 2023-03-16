<?php
if (!class_exists('PolylangManagerController')) {
    require_once dirname(dirname(__FILE__)) . '/manager.class.php';
}


class PolylangMgrPolylangContentManagerController extends PolylangManagerController
{

    public function loadCustomCssJs()
    {

        $this->addJavascript($this->polylang->config['jsUrl'] . 'mgr/polylangcontent/polylangcontent.grid.js');
        $this->addJavascript($this->polylang->config['jsUrl'] . 'mgr/polylangcontent/polylangcontent.panel.js');
        $this->addHtml('<script type="text/javascript">
        // <![CDATA[
        Ext.onReady(function() {
            MODx.add({
              xtype: "polylang-panel-polylangcontent",
              });
        });
        // ]]>
        </script>');
        $this->modx->invokeEvent('polylangOnManagerCustomCssJs', array('controller' => &$this, 'page' => 'polylangcontent'));
    }

    public function getPageTitle()
    {
        return $this->modx->lexicon('polylang_page_title_polylangcontent');
    }
}