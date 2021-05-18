msOptionHexColor.page.Home = function (config) {
    config = config || {};
    Ext.applyIf(config, {
        components: [{
            xtype: 'msoptionhexcolor-panel-home',
            renderTo: 'msoptionhexcolor-panel-home-div'
        }]
    });
    msOptionHexColor.page.Home.superclass.constructor.call(this, config);
};
Ext.extend(msOptionHexColor.page.Home, MODx.Component);
Ext.reg('msoptionhexcolor-page-home', msOptionHexColor.page.Home);