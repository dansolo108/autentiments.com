modMaxma.page.Home = function (config) {
    config = config || {};
    Ext.applyIf(config, {
        components: [{
            xtype: 'modmaxma-panel-home',
            renderTo: 'modmaxma-panel-home-div'
        }]
    });
    modMaxma.page.Home.superclass.constructor.call(this, config);
};
Ext.extend(modMaxma.page.Home, MODx.Component);
Ext.reg('modmaxma-page-home', modMaxma.page.Home);