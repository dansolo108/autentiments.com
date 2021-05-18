<?php
/** @var modX $modx */
switch ($modx->event->name) {
    case 'OnManagerPageBeforeRender':
        if($controller->config['controller'] == "mgr/settings" && $controller->config['namespace'] == "minishop2") {
            
            $modx->lexicon->load('msoptionhexcolor:default');
            
            $controller->addJavascript(MODX_ASSETS_URL . 'components/msoptionhexcolor/js/mgr/msoptionhexcolor.js');
            $controller->addLastJavascript(MODX_ASSETS_URL . 'components/msoptionhexcolor/js/mgr/widgets/colors.grid.js');
            $controller->addLastJavascript(MODX_ASSETS_URL . 'components/msoptionhexcolor/js/mgr/widgets/colors.window.js');
    
            $controller->addHtml('<script type="text/javascript">
                    msOptionHexColor.config = [];
                    msOptionHexColor.config.connector_url = "' . MODX_ASSETS_URL . 'components/msoptionhexcolor/connector.php";
                    Ext.ComponentMgr.onAvailable("minishop2-settings-tabs", function () {
                        this.items.push({
                            title: "' . addslashes($modx->lexicon('msoptionhexcolor_colors')) . '",
                            layout: "anchor",
                            items: [{
                                html: "' . addslashes($modx->lexicon('msoptionhexcolor_colors_intro')) . '",
                                bodyCssClass: "panel-desc",
                            }, {
                                xtype: "minishop2-grid-hexcolor",
                                cls: "main-wrapper",
                            }]
                        });
                    });
                </script>
            ');
        }
        if($controller->config['controller'] == "resource/update" || $controller->config['controller'] == "resource/create") {
            $modx->lexicon->load('msoptionhexcolor:default');
            $controller->addJavascript(MODX_ASSETS_URL . 'components/msoptionhexcolor/js/mgr/msoptionhexcolor.js');
            $controller->addHtml('<script type="text/javascript">
                    msOptionHexColor.config = [];
                    msOptionHexColor.config.hexcolor_title = "'.$modx->lexicon('msoptionhexcolor_product_hexcolor').'";
                    msOptionHexColor.config.connector_url = "' . MODX_ASSETS_URL . 'components/msoptionhexcolor/connector.php";
                </script>
            ');
        }
        break;
    case 'OnMODXInit':
        $modx->lexicon->load('msoptionhexcolor:default');
        break;
}