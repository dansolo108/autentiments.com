amocrm.page.Home = function (config) {
    config = config || {};
    Ext.applyIf(config, {
        components: [{
            xtype: 'amocrm-panel-home',
            renderTo: 'amocrm-panel-home-div'
        }]
    });
    amocrm.page.Home.superclass.constructor.call(this, config);
};
Ext.extend(amocrm.page.Home, MODx.Component);
Ext.reg('amocrm-page-home', amocrm.page.Home);